# Jev evaluation

Active taxonomy/rubric/corpus: **2.0.0**. The sole active 100-question corpus uses unchanged original titles with source references and frozen author annotations. [Sampling, ambiguity notes and live readiness](corpus-v2/README.md) · [taxonomy](../content/taxonomy-v2.md). No v2 live results exist.

## Current offline replay

The default CLI uses six explicitly synthetic responses in [evaluation-v2.json](../../tests/Fixtures/Jev/evaluation-v2.json) through the real Jev adapter with MockHttpClient. Four successes, two HTTP failures; intent 3/6, topic 3/6, joint 2/6; mock usage 400 input / 80 output tokens. This tests payload/normalization/metrics, not Jev classification. The [current offline report](offline-v2.json) records mock timing only.

```sh
rtk proxy php vendor/bin/phpunit
rtk proxy env APP_ENV=test APP_DEBUG=0 JEV_API_KEY= php bin/console app:jev:evaluate
rtk proxy env APP_ENV=test APP_DEBUG=0 JEV_API_KEY= php bin/console app:jev:evaluate --limit=2
```

`--corpus=<file>` selects a corpus matching the active taxonomy/rubric. `--split=development` is the default; no v2 holdout was prepared. Other taxonomy/rubric combinations and mismatching response fixtures fail before classification. No compatibility classifier or old-rubric execution switch exists. Frozen v1 corpora and reports are historical evidence, not relabeled v2 data.

Fixture fields: `corpus_version`, `taxonomy_version`, `rubric_version`, `split`, `responses`; response keys must be corpus sample IDs, each with integer `status` and string `body`. `--offline-fixtures=<file>` supplies another matching local replay. Default replay selects the fixture-map sequence, and `--limit` takes its prefix. Live takes the corpus sequence prefix. Report schema remains 1.0.0 because its structure did not change; taxonomy/rubric/corpus are separately reported as 2.0.0.

## Later bounded live trial — not run

The [100-question trial](corpus-v2/README.md) is prepared, not executed. One production JEV_API_KEY serves application and authorized evaluation; no separate test key or sandbox is required. The command requires `--live`, explicit `--limit=1..1000`, configured model/key, and a separately authorized run. The current corpus bounds a full run to 100 sequential requests, one per question. Two rubrics on 100 inputs would require 200 calls and are not silently substituted for a 100-call test.

Keys remain in local secret/environment configuration, never arguments, fixtures or reports. Current model pin is `jev-1.13.0`; timeout/max_duration are 0.60/0.80 seconds. No retries, redirects, sleeps or parallel calls. Preflight validates finite positive budgets, max_duration ≥ timeout, key/model and mode; failures exit 2 before network. A live run with technical failures writes a report and exits 1; no failures exits 0. Deliberate offline fixture failures still exit 0. Exact labels are annotations to evaluate, not hard assertions about language or model behavior.

## Historical baseline

[Stage-1 results](stage-1-results.md), [54 development titles](corpus-v1/development.json), [27 holdout titles](corpus-v1/holdout.json), and original [300/400 ms](live-development-v1.json) / [600/800 ms](live-development-v1-600-800.json) reports remain unchanged. Their classifications belong to taxonomy/rubric 1.0.0. No historical accuracy transfers to the current taxonomy, and the current runner explicitly rejects those corpora.

## Metric denominators and limits

Reports include requested model, **resolved-model counts** from valid responses only, taxonomy/rubric/corpus versions, split, UTC timestamp, corpus size, attempted sample count. Each sample records ID, expected/predicted labels or failure, latency, known usage; no raw questions, keys, provider errors.

- Main intent/topic/joint accuracy = correct / **all attempted samples**, counting technical failures as incorrect. Conditional accuracy = correct / **valid successful responses**. Each metric has numerator, denominator, value; zero denominator→`null` value.
- Technical failure rate = typed failures / **all attempts**, with reason breakdown. It is **not empty-catalog fallback rate**: this runner does not select or persist answers. HTTP failures differ from semantic mistakes.
- Confusion matrices: rows=expected, columns=predicted; special `TECHNICAL_FAILURE` column. Valid OTHER/GENERAL remains a genuine prediction.
- Latency P50/P95: **nearest rank**, sorted elapsed milliseconds at `ceil(p*N)-1`, all attempts including failures. Classifier wall time includes HTTP/parsing, not POST/end-to-end SLA. Offline timing is explicitly marked mock transport.
- Tokens sum **validated successes only**, with counts for known/unavailable usage. Current normalizer requires usage on every success; failures have no known usage and may still be billed. Mock tokens are synthetic. The runner does not calculate billing/cost.

No confidence threshold, accepted/rejected coverage policy, or automatic release gate exists. High development scores do not prove holdout quality; labels are not independent human gold. Humor evaluation is separate and content drafts are never sent to Jev.

## Live usage and expenses

The recorded 108 requests reuse the same 54 development questions. Their reports total **96,454 known input / 14,655 known output tokens**; 13 timeout attempts have unknown usage. At the **historically published** $0.042/1M input rate, known input implies approximately **$0.00405 estimated cost**, excluding unknown usage and any account-specific charges. This is arithmetic, not measured billing. Output was advertised free at that research date. No dashboard balance/invoice was checked; actual costs remain an explicit future evaluation objective using the production key and a bounded authorized run.

Historical live results/limits: [stage-1-results.md](stage-1-results.md). Original machine reports: [300/400 ms](live-development-v1.json), [600/800 ms](live-development-v1-600-800.json). Offline matrix and distinct verification events: [verification](../verification.md).
