# Development answer expansion — 2026-10-15

**1,055 accepted author drafts / 1,055 explicit eligibilities: 24 retained unchanged + 1,031 additions.** Enabled only as development content. All human scores remain `null`. The complete canonical import is [../catalog.json](../catalog.json), not any individual batch file.

Ten independently written source batches contain 1,145 completed candidates; six additional safety/form failures bring the reviewed proposal count to **1,151**. The final author pass excluded **120** proposals. This was not a template-permutation generator: scripts assign identifiers, serialize decisions, check provenance and count coverage; they do not compose answers or assess humor. Every new accepted text is 6–24 whitespace-separated words. The user explicitly allowed further human rewriting/filtering and removed 1,000 as an upper bound; no count is a publication gate.

## Evidence and reproduction

- `01-*.txt` through `10-*.txt`: authored texts, explicit pool headings, primary mechanism and individual comic shift. The header records the preliminary author rubric and all-probe assessment; final exclusions supersede those preliminary passes.
- [probes.json](probes.json): 38 shared sets of at least eight unchanged original questions, exact one-based lines in [questions.txt](../../../research/questions-v1/questions.txt) and matching [sources.csv](../../../research/questions-v1/sources.csv) URLs. These are author pool annotations, never observed Jev predictions.
- [editorial.json](editorial.json): **11,408 final question × candidate cells**, including **10,272 accepted cells**; stable answer/eligibility IDs, source locations, ordinary expectation, individual shift, final status, provisional author scores and null human scores. These are author judgments on seen questions, not outputs of an automated semantic evaluator. The original 24 answers retain their separate [historical matrices](../editorial.json).
- [decisions.json](decisions.json): explicit failed questions, hold/rejection reasons and related premises for novelty exclusions. Rejected drafts remain available for rewriting; they are absent from the runtime catalog.
- [retained-identities.json](retained-identities.json): original 24 IDs, text hashes, eligibility records and creation times. No existing answer or eligibility was revised or removed.
- [coverage.json](coverage.json): all 182 counts, exact empty-pool list, shallow-pool check, catalog hash and arithmetic.
- [final-verification.json](final-verification.json): final 1,055-row SQLite checks and full test evidence. [database-verification.json](database-verification.json) preserves the earlier 1,062-row run before the last seven editorial exclusions. Historical reports were not overwritten.
- [preservation.json](preservation.json): pre-task hashes of original questions, historical editorial/verification reports, frozen trial files and existing demonstration databases. Canonical catalog and current seed README are the two deliberately updated entries.

From the project root:

```sh
# Check serialization, original quotations/URLs, retained identities, counts and historical hashes:
rtk proxy python3 tools/build-seed-batch.py
# After deliberately editing this batch's author sources/decisions, rebuild its complete replacement:
rtk proxy python3 tools/build-seed-batch.py --write
# Seed the existing current development schema, or recreate it if the table definitions changed:
rtk proxy composer db:seed
rtk proxy composer db:fresh
rtk proxy php tools/verify-local-database.php
```

Candidate sequence numbers within each source file are stable keys: append new candidates; do not reorder, insert ahead of, or renumber existing entries. UUIDv5 uses the fixed namespace in [batch.json](batch.json), plus candidate key and explicit pair. Creation time is the recorded batch finalization time and does not change on rebuild. A substantive edit preserves identity when it is a revision of that answer; deletion/replacement must not recycle retired identities. The builder retains other catalog rows and checks all original identities before writing. Source text is never expanded into mechanical noun/number variants. The importer still replaces the entire catalog; do not import an individual source batch.

## How to read the screening

`P/C/L` = recognizable satirical requested form / no disqualifying context or implication found / legible turn. `U/C/L` deliberately degrades requested form for universal fallback. `S`, `F`, `A`, `I` respectively flag strained form, wrong form, unsupported context and inappropriate implication. A failure rejects that eligibility; passing other cells does not cancel it. Novelty failures may have all-positive portability cells. Accepted author scores are [2, 2, 2, 2, 1, 1] in the guide's order: form and seen-probe portability pass; shift/economy are strong; novelty and comic potential remain conservative handoff judgments. These repeated provisional scores are not independent measurements of amusement.

The ordinary expectation is recorded per candidate, while its specific shift is in the source and structured record. EXPLAIN allows a comic operating principle; METHOD an obviously impossible procedure; CHOICE an impossible criterion without an actual winner; WHO a fictional agent; WHEN an impossible temporal marker. These are the guide's editorial allowances, not proof that readers will find the answers satisfying. There is no hidden serious-question exclusion or runtime safety filter.

GENERAL sets contain specialized fallback cases, including pills, electrical shocks, knee pain, money, and symptom reports. The 32 universal probes collectively cover all 14 intents and all 13 topics. Small seen sets establish neither whole-pool correctness nor classifier accuracy. Many examples satirize models, instructions and unjustified authority; repeated exposure can still feel repetitive. Human novelty, sensitivity and humor filtering remain necessary.

Two annotation refinements were recorded instead of silently narrowing a failed pool: rabbit/cat recognition did not establish FOOD without its source community, so explicit grill-to-oven conversion replaced it and every food method was rechecked; entertainment gained credits and real-performer visibility probes. Every entertainment explanation was checked on both added questions; four fiction-specific answers failed. Three biology-specific health explanations also failed the existing medication-expiry probe. Source questions themselves were not edited.

## Coverage

**29 / 182 classification pools populated; 153 empty.** There are **76 ANY/GENERAL** answers outside that denominator. All populated pools have at least five candidates (smallest: WHERE/GENERAL, seven); “shallow” here means 1–4, not a claim of editorial adequacy. All 13 topics have explanatory depth, including GENERAL. Specialized methods cover nine topics; LANGUAGE also has comparison depth.

| Intent | TECH | WORK_STUDY | MONEY | RELATIONSHIPS | HEALTH | FOOD | HOME | NATURE | TRAVEL | SPORTS | ENTERTAINMENT | LANGUAGE | GENERAL |
| --- | ---: | ---: | ---: | ---: | ---: | ---: | ---: | ---: | ---: | ---: | ---: | ---: | ---: |
| YES_NO | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 |
| WHEN | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 42 |
| DURATION | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 |
| EXPLAIN | 46 | 44 | 38 | 40 | 26 | 44 | 41 | 37 | 19 | 37 | 32 | 45 | 57 |
| METHOD | 23 | 25 | 24 | 0 | 23 | 24 | 22 | 24 | 21 | 0 | 0 | 25 | 61 |
| WHO | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 56 |
| IDENTIFY | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 |
| DESCRIBE | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 |
| WHERE | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 7 |
| QUANTITY | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 |
| CHOICE | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 63 |
| RECOMMEND | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 |
| COMPARE | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 17 | 0 |
| OTHER | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 16 |

All 14 intent/GENERAL pools were assessed. Populated: WHEN 42, EXPLAIN 57, METHOD 61, WHO 56, WHERE 7, CHOICE 63, OTHER 16. Empty: YES_NO, DURATION, IDENTIFY, DESCRIBE, QUANTITY, RECOMMEND, COMPARE. Their exact-topic pools are also empty except COMPARE/LANGUAGE (17). WHEN, WHO, WHERE, CHOICE and OTHER have no specialized pools. METHOD has no specialized RELATIONSHIPS, SPORTS or ENTERTAINMENT pool. The JSON lists every empty pair without counting fallback reachability as exact-pool coverage.

## Concrete limits and exclusions

- YES_NO: even a comic second sentence leaves a literal yes/no decision about medication, surgery, gas smells or bank details.
- DURATION and QUANTITY: plausible intervals/numbers become waiting, storage, structural, hydration or financial instructions. Incompatible units and very long fictional waits do not fix the pool.
- IDENTIFY: a generic fictional label does not identify a plant, word, book or injury. No species-only or harmless-object hidden subgroup was introduced.
- DESCRIBE: a nominal definition fails consequences and reactions in the same pool.
- RECOMMEND: an abstract gadget does not become a suitable printer, food, blanket or power bank.
- COMPARE/GENERAL: describing the act of comparison is often an evasion; implying shared assumptions or equivalent trade-offs failed eggs, financial prices and health distinctions. All 15 proposals were held.
- WHERE: 13 physical-map/direction candidates failed virtual origins or health-source retrieval; seven explicitly fictional locations survived the author pass. Their usefulness as source-location answers still needs human judgment.
- Sensitive topics: diagrams, models and certainty are the satirical targets, not a condition, patient, child or person seeking help. No actual dosage, treatment, investment, repair instruction or literal choice winner was accepted. This is an author gate, not production certification.
- Novelty: repeated self-corroboration, diagram arrows treated as evidence, applause as proof, instruction manuals taking their own exams, oversized explanations and clarification with substituted props were removed. The source archive intentionally preserves those failed attempts for future rewriting.

The batch does not change taxonomy, schema, selection tiers, randomness or mechanism semantics. No Jev calls, paid services, PostgreSQL verification, Git delivery or deployment occurred. Existing demonstration databases retain their historical rows; the default development database contains the complete current catalog and zero verdicts.
