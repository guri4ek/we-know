# Stage 1: actual results

Date: 2026-10-11. Offline implementation completed; **live contract and development baseline were actually checked**, beyond a mock spike. This is not production acceptance, independently human-verified quality, or a completed product. The audit translated this record without modifying the original JSON measurements or making new live calls.

Subsequent user confirmation accepted DBAL + Migrations without ORM and VERDICT/decision_context persistence for the [next-session backend](../handoff/backend-implementation-prompt.md). Statements about unapproved persistence below describe the stage-1 checkpoint; no backend code was added while preparing that prompt. The subsequent backend task is now implemented and verified separately in [backend verification](../verification.md#backend-implementation-verification-event); the historical measurements below remain unchanged.

## Implementation

- [Taxonomy/rubric v1](../content/taxonomy-v1.md): 9 intents / 7 topics, ambiguity rules; ANY only future answer eligibility. Refinements to the original proposal are explicit.
- [Corpus 1.0.0](README.md): 54 development + 27 holdout, synthetic English author-labeled baseline. No independent human review; holdout was not used to tune the rubric or called live.
- `QuestionClassifier`, string enums, typed success/failure; `JevClassifier`, strict normalizer, Symfony DI, dedicated plain HttpClient. One POST with two Choice questions; no library/humor metadata, retries, redirects. Failures do not fabricate OTHER/GENERAL. No confidence threshold.
- HttpClient **7.4.20**, contracts **3.7.3**, polyfill-php83 **1.43.0** were the stage-1 additions and remain in the inspected lock. Composer still uses its original PHP 8.2 baseline; the user now identifies that as an error, with minimum **8.4** and planned **8.5**. [Pending correction](../verification.md#documentation-audit-findings).
- `app:jev:evaluate`: real-adapter offline replay or explicitly authorized bounded live runs. JSON includes model/rubric/corpus versions, count, accuracy/denominators, matrices, failures, latency, tokens. Default suite needs no key/network.
- [Answer examples](../content/stage-1-answer-examples.md) were prepared after reading completed research/guide without editing those documents during stage 1. The audit later translated their prose, preserving examples. Drafts are hold with known failed probes, no import/schema or humor assessment.

## Two development live batches

Conditions: local CLI, PHP **8.4.10**, Symfony **7.4.20**, `APP_ENV=prod APP_DEBUG=0`, same corpus/rubric/model pin, sequential calls, one per sample, retries=0. The production key was in `.env.local`, never args/logs/reports. Provider network path/production region was not measured; this is not POST `/answer` or its SLA. Stale local production cache was cleared before runs.

Serhii explicitly authorized the first 54 calls and another 54 for the longer budget: **108 API requests, 95 valid successes, 13 timeouts**. Repeated questions are not 108 independent corpus samples. Results were not used to tune rubric/labels between batches; only timeout policy changed at the user's request.

| Metric | [300/400 ms](live-development-v1.json) | [600/800 ms](live-development-v1-600-800.json) |
| --- | --- | --- |
| Requested/resolved model | `jev-1.13.0` / 41 successes | `jev-1.13.0` / 54 successes |
| Corpus/rubric/taxonomy | `1.0.0` / development | `1.0.0` / development |
| Attempts / valid successes | 54 / 41 | 54 / 54 |
| Intent accuracy, all attempts | 38/54 = **70.4%** | 52/54 = **96.3%** |
| Topic accuracy, all attempts | 38/54 = **70.4%** | 52/54 = **96.3%** |
| Joint accuracy, all attempts | 35/54 = **64.8%** | 50/54 = **92.6%** |
| Intent / topic, among successes | 38/41 = **92.7%** each | 52/54 = **96.3%** each |
| Joint, among successes | 35/41 = **85.4%** | 50/54 = **92.6%** |
| Technical failure rate | 13/54 = **24.1%**, all timeout | 0/54 = **0%** in this batch |
| P50 / P95, all 54 attempts | **249.8 / 304.6 ms** | **238.4 / 408.9 ms** |
| Known input / output tokens | **41,628 / 6,325**, usage known for 41/54 | **54,826 / 8,330**, usage known for 54/54 |

Percentiles use nearest rank across all attempts. The first P95 contains interrupted timeout attempts and **does not show their full API response time**. The second succeeded, but 54 samples cannot guarantee zero failures or stable P95. Comparison is not controlled causal evidence: network/provider load may differ, and successful-sample composition differed in the first batch. It suggests the 300 ms idle budget was too aggressive for that local run. **Serhii chose 600/800 ms current defaults** in `.env` and adapter/runner. Initial 300/400 remains historical baseline, reproducible through environment overrides. This is not a production SLA; no retry/threshold was added.

Full confusion matrices and sample IDs/predictions/confidence/latency/usage are in both JSON reports, without raw questions/keys/provider errors. The first report explicitly notes that HTTP-policy metadata was appended locally **after** its run, with no additional calls. Known totals: **96,454 input / 14,655 output tokens**. Usage for 13 timeouts is unknown and may be billed. Billing/dashboard/balance was not read; the [expense estimate](README.md#live-usage-and-expenses) is separate arithmetic, not a measured charge.

## Semantic mismatches

Four joint mismatches remained in the second batch:

| Sample | Author label | Jev | Evidence gap |
| --- | --- | --- | --- |
| dev-006: purpose of a morning routine | WHAT / LIFE | WHY / LIFE | Purpose is close to why; independent human review of WHAT label needed |
| dev-024: organizing team code reviews | HOW / WORK | HOW / TECH | People-organization/technical-subject boundary is not always followed |
| dev-034: release approval + server repair | WHO / WORK | WHO / TECH | Topic may shift to the second part; first-question rule is not guaranteed |
| dev-053: instruction-only joke generator / key request | OTHER / GENERAL | WHAT / GENERAL | Instruction-only form read as WHAT; output remained a typed label, no key/joke generated |

The first batch also differed on dev-032 (second-question MONEY instead of TECH) and dev-042 (HOW instead of CHOICE); the second matched author labels. These are variable predictions, not evidence of rubric repair. Labels/prompt did not change.

## Sandbox, outstanding checks, and next handoff

On the research date, the public [official documentation index](https://docs.typesafe.ai/llms.txt) and API reference showed **no documented separate sandbox endpoint or special test keys**. [Quick start](https://docs.typesafe.ai/introduction/quickstart) describes Playground and the normal endpoint with a dashboard key. This concerns available public documents, not proof no private/enterprise option exists. The user's project policy is settled: **use the production key; no separate sandbox/test key workflow**. The recorded 108-call budget is exhausted; the audit made no new calls.

Unverified: live holdout; independent human corpus labels; production region/concurrency; sustained failure/latency baseline; actual billing; PHP 8.5 transition; draft-answer fit/humor. A real PHP 8.2 run was not performed historically; 8.2 is no longer the intended project minimum. HTTP errors were mocked, not deliberately triggered against the provider. No confidence threshold is introduced from v1 results.

Future live evaluation needs the existing production key, authorization for split/model/timeout, and a **new request limit**. Holdout is a separate frozen 27-question set; freeze rubric/policy before evaluating, do not tune on it. [Reproduction commands](README.md). Key/model access works; no search for secrets or another account is needed. Actual expenses are a future objective, not inferred from low published prices.

Offline checks: [verification](../verification.md). Database, migrations/import, selector, VERDICT/decision_context, POST/fetch, permalink, and admin are unimplemented. Git/deployment/Claude/personal configuration was outside stage-1 changes; research artifacts were preserved. Subsequent stages remain separately scoped.
