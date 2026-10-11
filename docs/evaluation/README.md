# Jev evaluation v1

Date: 2026-10-11. Taxonomy/rubric/corpus version **1.0.0**. [Taxonomy and tie-break rules](../content/taxonomy-v1.md) were frozen before live runs; `JevRubric` constructs the exact provider payload.

## Corpus

- [development.json](corpus-v1/development.json): **54** synthetic English questions for development and the initial live baseline.
- [holdout.json](corpus-v1/holdout.json): **27** separate questions; do not tune the rubric on them. No live holdout run has been authorized/performed in the recorded work.
- `label_status=author_labels_not_independently_reviewed`: author annotations **without independent human review**. Both splits cover all 9 intents/7 topics; development tags cover ambiguity, negation, typos, non-question text, and instruction attacks. This small control corpus is not representative production traffic.
- Split IDs/texts are disjoint. Schema/coverage tests read both without rubric tuning on holdout. Any text/label/split change requires a new corpus version. Live `--limit` takes the frozen corpus prefix; offline replay takes the fixture-selected sequence prefix, in fixture response-map order.

## Running

Default offline replay uses six intentionally mixed fixtures from [evaluation-v1.json](../../tests/Fixtures/Jev/evaluation-v1.json) through the **real JevClassifier with MockHttpClient**. It is not model evaluation. Expected: 4 valid successes, 2 failures; intent 3/6, topic 3/6, joint 2/6; known 400 input / 80 output tokens. Mock latency is not provider latency. [Fixture provenance](../../tests/Fixtures/Jev/README.md).

```sh
rtk proxy php vendor/bin/phpunit
rtk proxy php bin/console app:jev:evaluate
rtk proxy php bin/console app:jev:evaluate --limit=2
```

The runner does not search personal configuration for keys. This personal project uses **one production JEV_API_KEY** for the adapter and authorized evaluation; no separate test account/key or sandbox workflow. Set it in already ignored `.env.local` or process environment, never CLI arguments/fixtures/committed `.env`. Default replay does not use it or access the network. `JEV_MODEL` is configurable, default pin `jev-1.13.0`. Test DI forces dummy/empty credentials and a deny-network client.

Live requires **`--live`, nonempty JEV_API_KEY/JEV_MODEL, and explicit `--limit=1..1000` together**, plus user authorization for the run. Failed preconditions exit 2 before network. No retries, parallel calls, or sleep; at most one call per selected question. Current configurable `JEV_TIMEOUT` / `JEV_MAX_DURATION`: **0.60 / 0.80 seconds**, chosen after comparison. Historical initial baseline: 0.30 / 0.40. Budgets must be finite, timeout positive, max_duration ≥ timeout; redirects=0. Reports record policy. A live run with technical failures still writes a report and exits 1; no failures→exit 0. Offline replay exits 0 even with deliberate fixture failures. Calls never exceed the limit or available selected sample count.

If an existing production cache predates service/source changes, refresh it locally first. The live commands below consume provider budget and require separate explicit authorization; **the audit did not run them**. They reproduce historical policies rather than define new defaults.

```sh
rtk proxy env APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear
rtk proxy env APP_ENV=prod APP_DEBUG=0 JEV_TIMEOUT=0.30 JEV_MAX_DURATION=0.40 php bin/console app:jev:evaluate --live --split=development --limit=54 > /private/tmp/jev-development-report.json
# Larger-budget comparison; also requires separate authorization for 54 calls:
rtk proxy env APP_ENV=prod APP_DEBUG=0 JEV_TIMEOUT=0.60 JEV_MAX_DURATION=0.80 php bin/console app:jev:evaluate --live --split=development --limit=54 > /private/tmp/jev-development-600-800-report.json
# Separate future run, only after holdout authorization:
rtk proxy env APP_ENV=prod APP_DEBUG=0 php bin/console app:jev:evaluate --live --split=holdout --limit=27 > /private/tmp/jev-holdout-report.json
```

Offline fixture format: object with `corpus_version`, `split`, `responses`; responses keyed by corpus sample ID, each with integer `status` and string `body`. `--offline-fixtures=/absolute/path.json` supports another local replay set for that split. Unknown IDs, version/split mismatch, malformed fixtures are rejected before the runner. Bundled fixtures match development; offline holdout requires matching fixtures. Do not copy live Bearer headers/raw traffic into fixtures.

## Metric denominators and limits

Reports include requested model, **resolved-model counts** from valid responses only, taxonomy/rubric/corpus versions, split, UTC timestamp, corpus size, attempted sample count. Each sample records ID, expected/predicted labels or failure, latency, known usage; no raw questions, keys, provider errors.

- Main intent/topic/joint accuracy = correct / **all attempted samples**, counting technical failures as incorrect. Conditional accuracy = correct / **valid successful responses**. Each metric has numerator, denominator, value; zero denominator→`null` value.
- Technical failure rate = typed failures / **all attempts**, with reason breakdown. It is **not empty-catalog fallback rate**: no selector/database exists. HTTP failures differ from semantic mistakes.
- Confusion matrices: rows=expected, columns=predicted; special `TECHNICAL_FAILURE` column. Valid OTHER/GENERAL remains a genuine prediction.
- Latency P50/P95: **nearest rank**, sorted elapsed milliseconds at `ceil(p*N)-1`, all attempts including failures. Classifier wall time includes HTTP/parsing, not POST/end-to-end SLA. Offline timing is explicitly marked mock transport.
- Tokens sum **validated successes only**, with counts for known/unavailable usage. Current normalizer requires usage on every success; failures have no known usage and may still be billed. Mock tokens are synthetic. The runner does not calculate billing/cost.

No confidence threshold, accepted/rejected coverage policy, or automatic release gate exists. High development scores do not prove holdout quality; labels are not independent human gold. Humor evaluation is separate and content drafts are never sent to Jev.

## Live usage and expenses

The recorded 108 requests reuse the same 54 development questions. Their reports total **96,454 known input / 14,655 known output tokens**; 13 timeout attempts have unknown usage. At the **historically published** $0.042/1M input rate, known input implies approximately **$0.00405 estimated cost**, excluding unknown usage and any account-specific charges. This is arithmetic, not measured billing. Output was advertised free at that research date. No dashboard balance/invoice was checked; actual costs remain an explicit future evaluation objective using the production key and a bounded authorized run.

Historical live results/limits: [stage-1-results.md](stage-1-results.md). Original machine reports: [300/400 ms](live-development-v1.json), [600/800 ms](live-development-v1-600-800.json). Offline matrix and distinct verification events: [verification](../verification.md).
