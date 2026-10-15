#!/usr/bin/env python3
"""Serialize the 2026-10-15 author decisions. No semantic scoring or text generation.

Default: check generated artifacts without writing. --write: rebuild this batch in
the complete canonical seed, preserving other answers and historical identities.
Source files are append-only: their candidate sequence numbers define stable IDs.
"""
import argparse
import collections
import csv
import hashlib
import json
import re
import uuid
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
SEED = ROOT / "docs/content/seed-v2"
BATCH = SEED / "batch-20261015"
INTENTS = "YES_NO WHEN DURATION EXPLAIN METHOD WHO IDENTIFY DESCRIBE WHERE QUANTITY CHOICE RECOMMEND COMPARE OTHER".split()
TOPICS = "TECH WORK_STUDY MONEY RELATIONSHIPS HEALTH FOOD HOME NATURE TRAVEL SPORTS ENTERTAINMENT LANGUAGE GENERAL".split()
MECHANISMS = dict(M="metaphor", R="reversal", H="hyperbole", C="false_causality", B="bureaucratic_absurdity", A="false_authority")
EXPECTATIONS = dict(EXPLAIN="A cause, purpose, or operating principle", METHOD="A method for achieving the requested outcome", WHEN="An event time or stage", WHO="A person, role, or responsible agent", CHOICE="A selection among supplied alternatives", OTHER="Considerate clarification of the requested outcome", ANY="A portable self-directed satirical verdict with deliberate form degradation", WHERE="A location or source", COMPARE="Differences, similarities, or trade-offs", YES_NO="Confirmation or denial", DURATION="A temporal interval", QUANTITY="An amount in compatible units", IDENTIFY="Recognition or a name", DESCRIBE="Meaning, properties, state, consequences, or reaction", RECOMMEND="A suitable unsupplied item or resource")


def read(path):
    return json.loads(path.read_text())


def encoded(value):
    return json.dumps(value, ensure_ascii=False, indent=2) + "\n"


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--write", action="store_true")
    args = parser.parse_args()
    meta = read(BATCH / "batch.json")
    namespace = uuid.UUID(meta["uuid_namespace"])
    probes = read(BATCH / "probes.json")
    decisions = read(BATCH / "decisions.json")
    retained = read(BATCH / "retained-identities.json")
    questions = (ROOT / "docs/research/questions-v1/questions.txt").read_text().splitlines()
    with (ROOT / "docs/research/questions-v1/sources.csv").open() as file:
        urls = {int(row["line"]): row["url"] for row in csv.DictReader(file)}
    for pool, rows in probes["pools"].items():
        assert len(rows) >= 8 and len({r["line"] for r in rows}) == len(rows), pool
        for row in rows:
            assert row["question"] == questions[row["line"] - 1], row
            assert row["source_url"] == urls[row["line"]], row
    annotations = probes["universal_coverage_annotations"]
    assert {v["intent"] for v in annotations.values()} == set(INTENTS)
    assert {v["topic"] for v in annotations.values()} == set(TOPICS)
    assert set(map(int, annotations)) <= {r["line"] for r in probes["pools"]["ANY/GENERAL"]}

    drafts = []
    for source in sorted(BATCH.glob("[0-9][0-9]-*.txt")):
        number = 0
        for line_number, line in enumerate(source.read_text().splitlines(), 1):
            if line.startswith("@"):
                pool = line[1:]
                assert pool in probes["pools"], pool
            elif line.strip() and not line.startswith("#"):
                number += 1
                mechanism, text, shift = line.split("|")
                assert mechanism in MECHANISMS
                drafts.append(dict(key=f"{source.name[:2]}-{number:03}", pool=pool,
                                   mechanism=mechanism, text=text, shift=shift,
                                   source=f"{source.name}:{line_number}"))
    assert set(decisions["exclusions"]) <= {d["key"] for d in drafts}
    all_batch_ids = {str(uuid.uuid5(namespace, "answer/" + d["key"])) for d in drafts}
    catalog = read(SEED / "catalog.json")
    catalog["answers"] = [a for a in catalog["answers"] if a["id"] not in all_batch_ids]
    by_id = {a["id"]: a for a in catalog["answers"]}
    for id, old in retained.items():
        answer = by_id[id]
        assert answer["created_at"] == old["created_at"]
        assert hashlib.sha256(answer["text"].encode()).hexdigest() == old["text_sha256"]
        assert answer["eligibility"] == old["eligibility"]

    evidence = []
    for draft in drafts:
        key, pool = draft["key"], draft["pool"]
        intent, topic = pool.split("/")
        id = str(uuid.uuid5(namespace, "answer/" + key))
        eligibility_id = str(uuid.uuid5(namespace, "eligibility/" + key + "/" + pool))
        exclusion = decisions["exclusions"].get(key)
        record = dict(key=key, answer_id=id, eligibility_id=eligibility_id, pool=pool,
                      source=draft["source"], expectation=EXPECTATIONS[intent], shift=draft["shift"],
                      status="accepted_author_draft" if exclusion is None else exclusion["status"],
                      human_scores=None, author_scores=meta["source_scores"] if exclusion is None else None,
                      cells={str(p["line"]): "U/C/L" if intent == "ANY" else "P/C/L" for p in probes["pools"][pool]})
        if exclusion:
            assert set(exclusion["failed_probes"]) <= set(record["cells"])
            record["cells"].update(exclusion["failed_probes"])
            record["reason"] = exclusion["reason"]
            record["related_candidate"] = exclusion["related_candidate"]
        else:
            assert 6 <= len(draft["text"].split()) <= 24, key
            assert not re.search(r"[{}<>\n]|As an AI", draft["text"])
            catalog["answers"].append(dict(id=id, text=draft["text"], locale="en", enabled=True,
                created_at=meta["created_at"], eligibility=[dict(id=eligibility_id, intent=intent, topic=topic,
                    humor_mechanism=MECHANISMS[draft["mechanism"]], created_at=meta["created_at"])]))
        evidence.append(record)
    for draft in decisions["additional_rejected"]:
        intent = draft["pool"].split("/")[0]
        record = dict(draft, status="reject", author_scores=None, expectation=EXPECTATIONS[intent],
                      cells={str(p["line"]): "P/C/L" for p in probes["pools"][draft["pool"]]})
        assert set(draft["failed_probes"]) <= set(record["cells"])
        record["cells"].update(draft["failed_probes"])
        evidence.append(record)

    ids, texts, triples = set(), set(), set()
    counts = collections.Counter()
    for answer in catalog["answers"]:
        assert answer["id"] not in ids
        ids.add(answer["id"])
        normalized = " ".join(answer["text"].casefold().split())
        assert normalized not in texts, answer["text"]
        texts.add(normalized)
        for row in answer["eligibility"]:
            assert row["id"] not in ids
            ids.add(row["id"])
            triple = (answer["id"], row["intent"], row["topic"])
            assert triple not in triples
            triples.add(triple)
            assert row["intent"] in INTENTS + ["ANY"] and row["topic"] in TOPICS
            assert row["intent"] != "ANY" or row["topic"] == "GENERAL"
            counts[row["intent"] + "/" + row["topic"]] += 1
    matrix = {intent: {topic: counts[intent + "/" + topic] for topic in TOPICS} for intent in INTENTS}
    empty = [f"{i}/{t}" for i in INTENTS for t in TOPICS if matrix[i][t] == 0]
    shallow = {f"{i}/{t}": matrix[i][t] for i in INTENTS for t in TOPICS if 0 < matrix[i][t] < 5}
    summary = dict(answers=len(catalog["answers"]), eligibilities=len(triples), retained_answers=len(retained),
                   new_answers=sum(r["status"] == "accepted_author_draft" for r in evidence),
                   proposed=len(evidence), excluded=sum(r["status"] != "accepted_author_draft" for r in evidence),
                   author_pair_cells=sum(len(r["cells"]) for r in evidence),
                   accepted_pair_cells=sum(len(r["cells"]) for r in evidence if r["status"] == "accepted_author_draft"),
                   populated_classification_pools=182-len(empty), empty_classification_pools=len(empty),
                   universal=counts["ANY/GENERAL"], coverage=matrix, empty_pools=empty,
                   shallow_pools_under_five=shallow, catalog_sha256=hashlib.sha256(encoded(catalog).encode()).hexdigest())
    artifacts = {SEED / "catalog.json": catalog, BATCH / "editorial.json": evidence, BATCH / "coverage.json": summary}
    for path, value in artifacts.items():
        content = encoded(value)
        if args.write:
            path.write_text(content)
        else:
            assert path.read_text() == content, f"Out of date: {path}"
    # Historical corpus/reports/demonstration databases remain byte-identical.
    for relative, digest in read(BATCH / "preservation.json").items():
        if relative in ["docs/content/seed-v2/catalog.json", "docs/content/seed-v2/README.md"]:
            continue
        assert hashlib.sha256((ROOT / relative).read_bytes()).hexdigest() == digest, relative
    print(json.dumps({k: v for k, v in summary.items() if k not in ["coverage", "empty_pools"]}, indent=2))


if __name__ == "__main__":
    main()
