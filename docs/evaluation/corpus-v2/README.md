# Frozen 100-question v2 trial

Prepared offline on 2026-10-12; final annotation audit/freeze on 2026-10-13, before any v2 model responses. [development.json](development.json) contains **100 distinct unchanged original titles**, original one-based line numbers and source URLs. [freeze.json](freeze.json) records content hashes. Taxonomy/rubric/corpus are 2.0.0. Labels and ambiguity notes are **author annotations pending human review**, not measured Jev classifications or independent gold labels.

This is a purposive development trial, not a representative sample or unseen holdout. Editorial screening overlaps these questions. Source collection bias remains in the [collection record](../../research/questions-v1/README.md). Question premises, allegations and events are quoted data, not adopted facts; no source pages were reopened.

| Disjoint group | Count | Assignment rule |
| --- | ---: | --- |
| ordinary | 50 | Relatively direct requested outcomes across all subject areas; missing images may still preserve identification |
| indirect | 20 | Imperatives, elliptical requests and indirect ability/existence wrappers with a recoverable substantive outcome |
| no_outcome | 10 | Question marks, question announcements, AMA headings or help wrappers whose actual requested outcome is omitted |
| boundary | 20 | Chosen primarily to challenge revised form/topic boundaries, first-request ordering or missing context; includes incomplete symptom reports |

Assignment priority was editorial purpose: boundary probes are removed from ordinary/indirect groups; no_outcome is reserved for apparent questions/announcements, while declarative incomplete symptom/fault reports are boundary probes. Each original appears once. The groups describe sampling, not runtime labels or filters. OTHER appears in boundary too. Some no_outcome inputs may reasonably be inferred as METHOD; those disagreements are explicitly annotated rather than hidden.

All **14 intents / 13 topics** occur. Important contrasts:

- L3311 “Recommend a printer?” and L3563 “I need personal cloud storage” → RECOMMEND/TECH; L2407's method imperative → METHOD/TECH.
- L3184 announces a medical question without supplying it → OTHER/HEALTH; L1446 is an incomplete symptom report → provisional OTHER/HEALTH, with explanation/method alternatives recorded.
- L3191 requests a reaction with an absent referent → DESCRIBE/GENERAL; L3281 still requests a cause → EXPLAIN/GENERAL.
- L2431 requests a mechanism; L1600 requests a method to estimate time. L1480 requests duration; L3198 physical length; L3294 a time of occurrence.
- L2980 requests lexical identification indirectly; L3060 asks a known term's meaning. L2409 supplies alternatives; L3566's app-or-strategy request is provisionally RECOMMEND with METHOD ambiguity.
- L459 uses the first independent request. L711 is software removal, despite finance vocabulary. L466 is physical cable installation. L3521 is a friendship boundary, despite travel context.
- L2177/L2217/L2230 do not establish games; source-community metadata must not supply that context. L7 names animals without a cooking context, so NATURE is the frozen topic annotation.

The current runner accepts this corpus by default. Default execution replays only six deliberately mixed **synthetic response fixtures**, not 100 model answers. A unit test runs all 100 questions against a counting failure double to establish one call per selected sample; it establishes no semantic accuracy.

After separate authorization, the single-rubric live command is:

```sh
rtk proxy env APP_ENV=dev APP_DEBUG=0 php bin/console app:jev:evaluate --live --split=development --limit=100 --corpus=docs/evaluation/corpus-v2/development.json > /private/tmp/jev-v2-live-100.json
```

Use the existing production key through local environment configuration; never put it in command arguments. Live execution is sequential, one call per selected input, with no retries or redirects. **This command was not run.** Its 100-call ceiling is for this one rubric; two rubrics on 100 inputs would require 200 separately authorized calls. No old-rubric execution mode or automatic comparison is implemented.

Freeze labels/rubric before calling; preserve disagreements and report results against the frozen annotations. Subsequent semantic edits require a new corpus/rubric artifact version. The fixture schema and metric denominators are in the [evaluation guide](../README.md).
