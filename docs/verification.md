# Actual results and verification

Updated: 2026-10-15. Working directory: `/Users/serhii/OwnProjects/we-know`. Individual events retain their original dates and evidence.

**Current implementation:** active v2 classifier/evaluation, canonical development catalog, complete scoped backend and dark-theme frontend. The [frontend event](#frontend-implementation--2026-10-15) records the latest offline UI evidence. Earlier events retain their historical stage-1/audit/backend states and live measurements.

Personal project/account; development/minimum PHP 8.4 (manifest/platform corrected), planned PHP 8.5; SQLite local and PostgreSQL production; one production Jev key for separately authorized live evaluation. The backend and frontend implementation tasks made no paid live calls.

## Current status by evidence

| Area | Status | Evidence / limitation |
| --- | --- | --- |
| Kernel/Twig/pages | Implemented and offline tested | Home/form, About/Privacy, styled errors, GET/HEAD/404/405 and bootstrap tests |
| Taxonomy/rubric/corpus and Jev adapter/DI | Implemented and offline tested | Active version 2.0.0, 100 original-question annotations; strict request/response/error tests; labels not independently reviewed |
| CLI/metrics | Implemented and offline tested | Actual adapter fixture replay, deterministic denominator/matrix/percentile tests |
| Jev live classification | Historically measured | Two repeated 54-question development batches; no new calls in this audit |
| Humor/eligibility | Author-screened development catalog | 1,055 answers imported locally; no independent human humor acceptance |
| Selector/database/import/answer HTTP/permalink | Implemented and SQLite/offline tested | Atomic catalog, uniform first pool, immutable snapshots, strict POST and saved SSR; tests use synthetic data |
| Frontend form/fetch/design | Implemented and offline tested | Dark Twig/vanilla-JS UI; ten isolated browser scenarios and HTTP coverage; no live-provider/production acceptance |
| VERDICT/decision_context and DBAL + Migrations | Accepted and implemented | SQLite constraints/FKs/reconnect, immutable JSON and real failed COMMIT rollback verified; no ORM |
| Final mechanism codes | Editorial proposal | No independent content acceptance; mechanism metadata does not affect selection |
| Chaos/NLP/weights/admin/likes/queues/cache | Deferred | No implementation authorized by documentation |
| PHP 8.5, holdout, production load, billing/retention | Unverified | Separate future work; Composer now requires/resolves PHP 8.4 |

## Skeleton: created

- Composer project/lock; minimal Symfony 7.4.20 FrameworkBundle, Console, Dotenv, YAML, TwigBundle 7.4.19; Twig 3.30.0. Symfony core packages were originally constrained to a PHP 8.2 dependency baseline on branch 7.4; contracts/polyfills have separate versions. The intended PHP minimum is now corrected to 8.4, with manifest/lock correction still pending.
- Explicit bootstrap, kernel, console, HTTP front controller, routes/services/framework/Twig config, English GET placeholder.
- PHPUnit 11.5.57, BrowserKit/CssSelector 7.4, test environment, five executable tests.
- Context, plan, C4, data model, ADRs, Jev research, testing strategy.

Composer 2.8.10/PHP 8.4.10 were already installed; no global reinstall. Symfony CLI was not installed. Local `vendor/` is restored from `composer.lock`, never manually edited.

## Skeleton: historical checks performed

| Check | Historical result |
| --- | --- |
| `composer validate --strict --no-check-publish` | Pass, valid manifest |
| `composer check-platform-reqs` | Pass on actual PHP 8.4.10 |
| `php bin/console about` | Kernel boot, Symfony 7.4.20 LTS |
| `php vendor/bin/phpunit` | **5 tests, 16 assertions, pass** |
| Real HTTP with `APP_ENV=prod APP_DEBUG=0` | GET `/` 200; HEAD `/` 200 without body; unknown URL 404; POST `/` 405 |
| Production HTTP error bodies | No stack trace, RouterListener path, local filesystem paths |
| Mermaid 11 parser + jsdom in temporary directory | **6 diagrams parsed:** 5 C4, 1 ER |
| Local Markdown links, JSON blocks, fences | Script checked after documents were completed |

Commands ran through `rtk proxy`. Mermaid/jsdom npm dependencies were placed only in `/private/tmp/we-know-doc-validation`; the PHP project has no Node dependencies. No browser automation was used for those checks.

Application tests establish SSR/English HTML, one main, current robots/noindex, HEAD without body, POST 405, missing-page 404. Bootstrap regression checks process `APP_ENV=prod` / `APP_DEBUG=0` precedence over local Dotenv defaults even when missing from superglobals.

Historical HTTP smoke used a temporary PHP server at `127.0.0.1:18081`, without browser; server stopped afterward. This checked debug-off production configuration through HTTP, not PHP-FPM/Nginx/performance. The first cold start exceeded a short timeout; another run with adequate technical timeout passed. No target P50/P95 was inferred.

Symfony's CLI error renderer may expose technical console details; no-leak evidence was specifically debug-off HTTP smoke. It found a process-environment import issue in Dotenv; after correction the full then-current PHPUnit suite and HTTP smoke were rerun.

## Historical limitations after the skeleton

These describe the earlier checkpoint, not today's stage-1 implementation:

- Business logic, POST fetch, `/answer`, permalink, catalog, migrations, DBAL, Jev adapter/credentials did not exist. No hidden stub masqueraded as working AI. Stage 1 later added classification/access, not the answer flow.
- Business-rule unit tests, offline adapter integration, live contract were **planned, not written/run** then. Stage-1 classification tests/live records below supersede that part.
- TypeSafe account/key/balance were unchecked and no paid calls had occurred at bootstrap. Official research did not replace live integration evidence.
- SQLite/PostgreSQL repositories/migrations were untested. Local `pdo_sqlite`/`pdo_pgsql` were present, not evidence of PostgreSQL server/connection.
- PHPUnit ran on PHP 8.4.10, no real PHP 8.2 runtime. Composer platform 8.2.0 constrained resolution rather than proving runtime compatibility; it is now an erroneous project setting, not the intended minimum.
- Mermaid syntax passed; visual browser layout was not checked. Placeholder UI is not final design.
- No deployment, performance/quality baseline, backups/retention verification, or public domain then. Stage-1 development metrics below are not production acceptance.

## Historical change boundaries and delivery evidence

Initial scaffold was created without Git. Under a later explicit request, a separate agent checked `https://github.com/guri4ek/we-know`; its `git ls-remote` returned no refs. Local repository was initialized on `main` with that origin. Supplied baseline commit: `e0dcdcc9671b9b046cc17ec436d53d60f196a729`, 30 source/config/test/doc/lock files. Existing Git author configuration was used without personal changes. The user separately authorized foundation/documentation push, not product deployment. **This is supplied historical evidence; no Git command was run to verify it in the documentation audit.**

Before the baseline, staged paths/content and 64 lockfile-package source URLs were checked: no credential patterns/URL credentials found. `.env` had local defaults and empty APP_SECRET. `vendor/`, `var/`, local secret env files were ignored; `.idea/` and generated Symfony `config/reference.php` were also added to ignore during documentation refinement without deleting local files. IDE/runtime/generated reference did not enter commits. Personal Codex/Claude files/backups were unchanged. Stage 1 later moved its API key to already ignored `.env.local`.

## Historical post-baseline documentation refinements

- UUID PK/created_at everywhere, separate eligibility UUID with unique triple; removed ANSWER.revision/VERDICT.answer_revision.
- Accepted eligibility humor metadata ignored by future selector. One controlled mechanism string per row proposed; statistics/likes/votes unimplemented.
- Simplified VERDICT with nullable FK/short historical context marked proposed. Jev categories separate from selected pool; catalog changes/deletion do not rewrite snapshots.
- Editable development migrations for disposable data before production; immutable incremental migrations for shared/persistent data. No migrations/reset performed.
- README/map/context/ADRs/C4/plan/Jev/testing reconciled. Research rubric/corpus versions do not imply mandatory VERDICT columns.

Historical checks: before baseline PHPUnit **5 tests, 16 assertions, pass**; after documentation changes Mermaid parsed **6 diagrams**, **10 Markdown files, 34 local links, 4 JSON blocks, paired fences** checked. Historical `git diff --check` passed. No new business tests/live calls/DB checks/browser automation; application code was unchanged during that documentation refinement.

Bootstrap was followed by taxonomy/corpus and the Jev spike in the [plan](technical-plan.md). Skeleton tests alone do not prove product completion.

## Stage 1: content and Jev spike

A new explicit request authorized scoped implementation; separately Serhii authorized two 54-call development live batches. Initial 300/400 ms yielded 41 successes/13 timeouts; 600/800 ms yielded 54 successes/no failures. **Serhii chose 600/800 ms current defaults.** Rubric/labels were unchanged; holdout was not called.

Implemented taxonomy/rubric v1, 54 development + 27 holdout, typed QuestionClassifier, Jev adapter/strict normalizer/DI, configurable budgets/model/key, CLI runner, offline suites. Added only HttpClient 7.4.20 and required contracts 3.7.3/polyfill-php83 1.43.0. Application key moved from versioned `.env` to already ignored `.env.local`, without value display/report copies.

Unit/integration matrix: success, malformed/empty/oversized JSON, missing fields, invalid enums including ANY, numeric types/ranges/non-finite values, incomplete/invalid distributions, model/usage, extra metadata, low confidence without threshold, valid OTHER/GENERAL, 401/403/422/429/529/500/502/503/redirect/non-200, timeout/transport at request/body, exact endpoint/headers/payload, two Choice per call, no retry, DI/deny-network test client. Idle timeout uses documented empty MockResponse chunks, no sleep.

Deterministic metric fixtures cover correct/wrong/failures, empty/all-failed, nearest-rank P50/P95, matrices/numerators/denominators, known/unavailable usage. CLI preflight checks opt-in, key/limit/split/fixtures/budgets; offline replay uses the real adapter. Test services force dummy/empty credentials independently of local secrets.

| Historical final stage-1 check | Recorded result |
| --- | --- |
| Default PHPUnit, all 5 skeleton tests included | **156 tests, 561 assertions, pass**, PHP 8.4.10 |
| Composer strict validation/platform | Pass; original baseline resolution 8.2, actual runtime 8.4.10 |
| YAML / container lint (dev and prod) | Pass, 6 YAML files, compatible DI types |
| PHP syntax | Pass, 27 source/test PHP files |
| Offline CLI replay | 6 attempts, 4 successes, 2 deliberate failures; fixture metrics, 600/800 ms defaults |
| Production offline CLI | 2 bounded samples without network; 600/800 ms policy |
| Documentation | 16 README/docs Markdown files, 94 local links, 5 JSON blocks, 8 corpus/report/fixture/Composer JSON files; paired fences |
| Secret/report consistency | Key only in ignored `.env.local`, not deliverable source/docs/config/fixtures; two 54-attempt reports, matrix sums=54, no retries |

The historical 16-file count covered root README/docs, excluding the fixture README; a later final handoff check described 17 including it. These are historical scopes, not current counts. Diagrams were unchanged and not rerendered during stage 1. No repeated server/browser smoke: functional tests covered GET/HEAD/404/405 and product routes were unchanged.

Offline reproduction:

```sh
rtk proxy composer validate --strict --no-check-publish
rtk proxy composer check-platform-reqs
rtk proxy php vendor/bin/phpunit
rtk proxy php bin/console lint:yaml config
rtk proxy php bin/console lint:container
rtk proxy php bin/console app:jev:evaluate
```

Historical live results, metric denominators, commands: [evaluation](evaluation/README.md), [comparison](evaluation/stage-1-results.md). Valid live responses contained both categories, distributions/confidence, resolved model, tokens in one call. Second batch: intent/topic 52/54 (96.3%), joint 50/54 (92.6%), failures 0/54, P50/P95 238.4/408.9 ms, tokens 54,826/8,330. Synthetic development evidence, not human gold/production SLA. Original JSON reports are unchanged.

Unverified: holdout, independent labels/humor, production region/concurrency/sustained latency/failure, billing. HTTP errors were mocked rather than provoked live. Real PHP 8.2 was historically untested; current user requirement is minimum 8.4 with a separately unverified 8.5 transition. Further calls require new split/model/policy authorization and request limit; the existing production key works. Recorded 108 calls stayed within budget with no retry.

Database/migrations/import/selector/VERDICT/POST/fetch/permalink/admin are unimplemented; decision_context unapproved. Humor/guide research was preserved in stage 1; answer examples are separate hold drafts. Stage 1 ran no Git delivery/deployment/browser or personal/Claude writes. The subsequent audit translates documentation while preserving those evidence artifacts.

## Documentation audit findings

This section was added by the documentation-audit chat on 2026-10-11. The translated historical sections above retain their recorded evidence; those are not new audit runs.

- **Pending separate implementation prompt — erroneous PHP minimum/platform:** the user confirmed development and minimum PHP 8.4, with a planned transition to 8.5. `composer.json` still declares `require.php >=8.2` and `config.platform.php=8.2.0`. These must be corrected consistently with dependency/lock verification under the next prompt. No manifest, lock, or product-code changes were made in this audit.
- **Resolved in translated documentation:** personal project/personal account context replaces imported WHMCS/work reviewer-role assumptions; English documentation/Ukrainian communication is explicit; SQLite locally and PostgreSQL in production are definite choices; one production Jev key serves authorized evaluation, without a separate test key/sandbox workflow.
- **Resolved in translated documentation:** current 600/800 ms policy is distinct from historical 300/400 ms; frozen classifier taxonomy v1 is distinct from proposed editorial eligibility/codes; historical smoke assignments and draft `taxonomy_status: proposal` are preserved with qualifications. Exactly HTTP 200 is required by the adapter; offline limits follow fixture order. The first live report's post-run policy annotation is identified.
- **Resolved in testing/verification:** separated existing suites from planned selector/database/HTTP/JS checks, recorded historical live-PHPUnit/test-key proposals as superseded, reconciled current budgets, and retained distinct historical/current verification events. Future F1/P99/coverage/application-fallback metrics are not claimed as implemented.
- **Unverified:** live holdout, independent human labels/humor, production region/concurrency/sustained rates, actual billing/retention, PHP 8.5 transition. Historical real PHP 8.2 testing was not performed; 8.2 is now an erroneous configuration rather than the intended minimum.

## Documentation audit verification event

Date: 2026-10-11. A separate audit event, not replacement of bootstrap/stage-1/live measurements. The user briefly paused work, then requested continuation in this chat. Translation and artifact validation are complete. Historical research, measurements, and example content were preserved; later user clarifications are explicitly distinguished.

Actually executed in this chat, through `rtk proxy`:

| Check | Result |
| --- | --- |
| `php vendor/bin/phpunit` | **156 tests, 561 assertions, pass**, PHP 8.4.10 / PHPUnit 11.5.57 |
| `composer validate --strict --no-check-publish` | Pass |
| `composer check-platform-reqs` | Pass on installed PHP 8.4.10; does not validate the intended minimum/platform correction or PHP 8.5 |
| `php bin/console lint:yaml config` | Pass, **6 YAML files** |
| `php bin/console lint:container` | Pass, default environment; no production lint run in this chat |
| Default offline `app:jev:evaluate` | Exit 0; 6 attempts, 4 successes, 2 deliberate failures; intent/topic 3/6, joint 2/6, known tokens 400/80; current 600/800 ms, no redirects/retries |
| `APP_ENV=prod APP_DEBUG=0` offline replay, `--limit=2` | Exit 0; 2 attempts/successes, joint 2/2, known tokens 200/40; same 600/800 ms policy, no live traffic |
| Local documentation validation | **20 Markdown files, 153 local links/anchors, 16 fenced blocks, 5 JSON blocks, 8 JSON files**, pass; all documentation English |
| Shell examples | **4 fenced shell blocks**, `sh -n` syntax pass; examples not executed by this check |
| Mermaid 11 + jsdom, existing temporary packages | **6 diagrams parsed**, 5 C4 + 1 ER; syntax only, no browser/visual-layout inspection |
| Preserved artifacts and historical reports | SHA-256 source/config/test/corpus/fixture/manifest/lock/live-report comparison; JSON examples, quoted English examples, smoke rows, URLs retained; live report matrices/accuracy/nearest-rank/tokens recomputed from original rows |

Source/configuration/tests and both original live reports were inspected read-only. No new live API calls, server/browser smoke, Git commands, dependency installation, product edits, or personal/Claude configuration writes occurred. The two original live reports, frozen corpus, and fixture data were preserved. SHA-256 comparison confirmed inventoried source/config/test/corpus/report/fixture files unchanged; `.gitignore` changed concurrently outside this audit and was preserved without edits. Temporary local validators checked document structure, links/anchors, JSON, shell syntax, Mermaid, original report arithmetic, and preserved examples. Current counts above include root AGENTS, both handoff documents, and fixture provenance README. Historical counts retain their original scopes. External citations/pricing/account terms were not refreshed; published-rate expense arithmetic is not measured billing. No real PHP 8.5 or production concurrent/load validation was performed.

The [pause handoff](handoff/documentation-audit-resume.md) is retained as a historical snapshot and has been superseded by this completed audit. The deferred Composer correction remains open.


## Backend handoff preparation event

2026-10-11, after the completed documentation audit: Serhii approved DBAL + Migrations without ORM, the VERDICT columns/FK, diagnostic minimum, and complete next-session backend scope. Dark-theme frontend remains deferred. The implementation prompt and relevant current decision/status documents were updated; no product, dependency, database, or live-API change was made in this preparation step. Historical verification events above keep their original counts and scope.

Current handoff validation: **21 Markdown files, 175 local links/anchors, 16 fenced blocks, 5 JSON blocks, 8 JSON files**, pass; **6 Mermaid diagrams parsed**. Original JSON examples, English answer examples/smoke rows, citations, live-report arithmetic, and inventoried source/config/test/corpus/fixtures/manifest/lock/live-report hashes were preserved. The concurrent .gitignore difference remains outside these edits. Product tests were not rerun for this documentation-only handoff preparation.


## Backend implementation verification event

Date: 2026-10-11. New local implementation under the complete [backend handoff](handoff/backend-implementation-prompt.md), independent of earlier audit/live evidence. No Git commands, subagents, deployment, paid provider calls, personal configuration or Claude writes. Existing secret env files were not inspected/copied/rewritten. Nonsecret DB/origin/proxy/limit defaults were appended separately to `.env`.

Implemented: `Answer`, `Eligibility`, `Question`, `Verdict`, UUID/UTC helpers; `AnswerCatalog`/`RandomIndexSource`; DBAL catalog/verdict repositories, importer and transaction cleanup; initial migration `Version20261011000100`; `migrations:migrate`, `migrations:status`, `app:catalog:import`; `POST /answer`, `GET`/`HEAD /a/{publicId}` with minimal escaped Twig/canonical/textual OG. Precise configuration, content format and error contract: [backend guide](backend.md).

Composer now declares PHP >=8.4 / platform 8.4.0 and PDO. A targeted minimal-change update installed eight packages and updated/removed none: DBAL 4.5.0, Migrations 3.9.8, doctrine/deprecations 1.1.6, doctrine/event-manager 2.1.1, Lock 7.4.20, RateLimiter 7.4.18, Stopwatch 7.4.8, and transitive OptionsResolver 8.0.8 (PHP 8.4 compatible). Plugins/scripts were disabled and Composer cache was placed in `/tmp`. Official dependency manifests for [DBAL](https://github.com/doctrine/dbal/blob/4.5.0/composer.json) and [Migrations](https://github.com/doctrine/migrations/blob/3.9.8/composer.json) permit the chosen PHP/Symfony combination; installed manifest/platform checks passed. Rate limiting follows [Symfony 7.4 documentation](https://symfony.com/doc/7.4/rate_limiter.html).

Actually executed through `rtk proxy`:

| Check | New result |
| --- | --- |
| Full default offline PHPUnit | **237 tests, 911 assertions, pass**, PHP 8.4.10 / PHPUnit 11.5.57; all original 156 tests retained |
| Composer strict validation / installed platform requirements | Pass; PHP 8.4 manifest/platform reconciled with lock |
| YAML / DI / Twig | 8 YAML files; dev and prod container lint; 2 Twig files: pass |
| PHP syntax | 71 PHP source/test/bootstrap/entry files: pass |
| Fresh SQLite migration / integrity | Three business tables + Doctrine metadata; UUID PK/nonnull, unique triple, FK enforcement on each connection and reconnect; pass |
| Catalog import | 5 synthetic development answers / 6 explicit eligibility rows; valid reimport, invalid fields/IDs/time/labels/metadata, stable identities and full atomicity tested |
| Transactions / snapshots | Insert failure and real deferred-FK COMMIT failure roll back verdict/import; edited/disabled/deleted catalog preserves text/context, deletion sets FK NULL |
| HTTP | Listed 400/403/413/415/422/429/503, success 201/headers, Unicode/8 KiB boundaries, rate/spoofed forwarded IP, escaping/canonical, GET/HEAD/404 and kernel reboot with separate file connection; pass |
| Jev offline replay | 6 fixture attempts, 4 valid successes and 2 deliberate failures; known synthetic tokens 400/80; no live traffic |
| Prod/debug-off Jev offline replay | `--limit=2`: 2 fixture attempts/successes; known synthetic tokens 200/40; no live traffic |
| Prod/debug-off HTTP error smoke | Temporary local server, forcibly empty Jev key, synthetic unavailable PostgreSQL DSN: 503, fixed JSON, no Location/private details, no provider call; server stopped. Stale pre-backend prod cache first returned 404; clearing local prod cache and repeating passed |
| Local artifacts | 23 Markdown files, 188 local links/anchors, 18 paired fences, 6 JSON blocks, 9 JSON files; 5 shell blocks pass syntax; 6 Mermaid diagrams parse (no visual-layout claim) |
| Preserved evidence | 34 classifier/evaluation/source/test/corpus/report hashes match the prior inventory; historical live-report arithmetic and research candidate quotes/draft JSON preserved |
| Real local DB smoke with fake classifier | Explicit synthetic import → one persisted timeout fallback → read via new DB connection; one fake classifier call, zero network calls, snapshot equality |

Focused red checks preceded migration/catalog/selector/question/HTTP/command implementations and the COMMIT/reconnect/nonnull fixes. Tests exercise behavior and actual SQLite constraints rather than repeat configuration. The real deferred-COMMIT test caught an open native SQLite transaction after DBAL's counter reset; the shared cleanup now rolls it back. The reconnect test caught PRAGMA initialization only once; driver middleware now initializes every native connection.

The final local file is **`/Users/serhii/OwnProjects/we-know/var/we-know.sqlite`**. Migration application created it; it was not an empty touched placeholder. After adding explicit nonnull UUID PKs to the unreleased migration, a freshly migrated sibling file received all existing business rows, exact row equality/FK checks passed, and the active file was replaced only after checking that its hash had not changed. All **5 answers, 6 eligibility rows and 1 synthetic verdict** were preserved. The prior local file remains at `var/we-know.sqlite.before-schema-finalization`; nothing was destructively reset. This is development schema refinement, not a production upgrade procedure.

The fixture and smoke strings are explicitly synthetic backend data; research hold drafts were not imported or promoted. A saved synthetic result exists at `/a/2ef216a7-3d19-4410-8bed-d9527ae8ce65`. No production-approved answer library, human scores or live measurements were fabricated.

PostgreSQL acceptance **did not run**: no `psql`/`pg_isready`, no local port-5432/socket server, and no running Docker/Podman service was available. No global services were installed/started and no production database was contacted. PostgreSQL-specific JSONB/boolean SQL and candidate-row share locks are implemented, but SQLite results do not prove PostgreSQL runtime, concurrency, migration or query-plan acceptance.

Remaining gaps: real PostgreSQL (version/hosting undecided), multi-process/production concurrency and load, limiter topology/limits and shared storage for multiple hosts, reverse-proxy size/TLS configuration, total paid API budgets, backups/restore, retention/deletion/privacy/sensitive-question policy, human content-pool quality and independently labeled/live holdout, billing, PHP 8.5. No browser/frontend acceptance was run; the next stage is the confirmed **dark-theme frontend**, form/fetch/error/storage notice and sharing UX.

## Taxonomy v2 and seed verification

2026-10-12–13, local implementation and offline authoring under explicit user authorization. The user clarified that this pre-frontend development stage should evolve directly: v2 is the sole active taxonomy, with no parallel v1 execution or upgrade machinery. No Git operations, subagents, external/paid calls, deployment, production changes, personal configuration changes or Claude writes were performed.

Implemented [taxonomy/rubric 2.0.0](content/taxonomy-v2.md): 14 intents, 13 topics, outcome-based English criteria, strict full distributions, catalogue version 2 validation and fresh SQLite schema constraints. The existing unreleased migration was edited directly; the preserved development file was not migrated/reset. Normal selector, diagnostic and immutable snapshot semantics remain unchanged.

Test-first evidence:

- Initial focused contract run: 3 tests failed as expected (enum mismatch, v2 distribution rejected, new catalog version rejected). Implementation then passed all 3 with 195 assertions, including every one of the 182 intent/topic combinations.
- Evaluation tests exposed missing version preflight and the old default corpus path before runner changes. The runner now requires matching taxonomy/rubric and matching response fixtures before classification.
- Final complete PHPUnit run on PHP 8.4.10: **245 tests, 1,314 assertions, pass**. Includes application HTTP, classification mocks, import rollback, SQLite persistence, selector and 100-question counting-double coverage. No test needed a provider key/network.
- Syntax checks: **46 PHP source/helper files passed**. No PHP 8.5 or real PostgreSQL acceptance was performed.
- Actual CLI [offline replay report](evaluation/offline-v2.json): 6 synthetic responses, 4 valid successes / 2 HTTP failures, joint metric 2/6 by fixture design. This is integration/metric evidence, not model accuracy.

The [complete seed](content/seed-v2/catalog.json) has **24 answers / 24 eligibility rows**, 9 populated classification pairs / 173 empty, plus 2 universal answers in ANY/GENERAL. Ten pilot survivors were retained unchanged; B2 was excluded and none revised. Fourteen new texts passed the preliminary gate. [Editorial evidence](content/seed-v2/editorial.md): 252 accepted-candidate cells + 91 excluded-candidate cells = **343 author judgments** across **110 distinct original questions**. Human scores remain null. The requested 30–50 quantity was not filled with failed or repetitive texts.

The actual isolated database `/Users/serhii/OwnProjects/we-know/var/answer-seed-v2-20261012.sqlite` contains the complete seed and five durable verdicts produced with offline classification doubles. [Database record](content/seed-v2/database-verification.json) establishes the existing migration/import path, invalid-input atomicity, real mid-write rollback, repeat-import identity/timestamp preservation, counts, exact → intent/GENERAL → universal routing, successful OTHER versus failure, every candidate index, mechanism independence, immutable snapshots after catalog edit/disable/delete, foreign keys and reconnect durability. Temporary catalog mutation probes rolled back; subsequent read-only comparison matched every catalog field and returned SQLite integrity `ok`. No synthetic catalog importer was run on the editorial database.

[Structural artifact checks](content/seed-v2/artifact-verification.json) validate complete JSON, category consistency, source quotations and CSV alignment, UUID/text/triple uniqueness, matrix counts, all labels and universal probe coverage, freeze hashes and local Markdown targets. The full 3,593-title/source-row alignment remains valid. The existing development database still has modification time 2026-10-11T18:55:42Z, predating this task. No writes targeted frozen corpora, pilot, historical reports or research. The pre-task hash manifest was temporary and unavailable after the session resumed; the artifact record explicitly distinguishes current preservation hashes from a completed before/after byte comparison.

The [100-question live trial](evaluation/corpus-v2/README.md) contains disjoint 50 ordinary / 20 indirect / 10 apparent-question-without-outcome / 20 boundary cases, preserving original text and sources. Expectations, ambiguity notes and rubric/source hashes were frozen before any v2 model responses. One corpus annotation was corrected during the pre-live audit: L7's animals do not establish cooking context. The counting double verifies exactly one call per each of 100 selected originals. **No live v2 trial was run**; a later single-rubric run needs its own bounded authorization. Running two rubrics on 100 questions would require 200 calls, and no comparison mode is implemented.

Remaining acceptance: independent corpus labels, human answer fit/humor and sensitive-question tone, unseen-pool portability, repetition fatigue, v2 model outputs, production PostgreSQL and frontend. SQLite success and mock labels do not establish those results.


## Development library and local database lifecycle — 2026-10-15

Explicit user scope: substantial offline English answer generation, full canonical seed replacement, local DB lifecycle, tests and documentation. Clearing and rebuilding `var/we-know.sqlite` was authorized; the user later removed 1,000 as an upper limit and confirmed that human rewriting/filtering would follow. No Git delivery, deployment, paid/external services, personal configuration or Claude-file changes were performed for this task.

### Editorial artifacts

The final [complete catalog](content/seed-v2/catalog.json) contains **1,055 answers / 1,055 eligibilities**, retaining every original answer, eligibility, text, UUID and creation time and adding **1,031** enabled author drafts. Ten written batches plus six extra form/safety probes produced 1,151 proposals; **120** were held or rejected. The [new evidence](content/seed-v2/batch-20261015/README.md) contains source-exact shared probes, per-candidate shifts, final cell assessments, null human scores, rejected variants and explicit limitations. Scripts performed serialization/identity/provenance/count checks, not semantic or humor evaluation.

There are 29 populated classification pools and 153 empty ones, plus 76 universal answers. All 14 intent/GENERAL pools were assessed; seven remain empty. No populated pool has fewer than five answers. Counts do not establish classifier accuracy, whole-pool suitability or human approval. The final stress pass added real-performer visibility to entertainment and excluded fiction-only explanations; existing medication-expiry probes excluded biology-only health explanations. The 24-answer historical reports and frozen corpora remain byte-identical.

### Test-first and offline verification

- Before lifecycle implementation, the focused suite failed with 16 missing-class errors and two missing Composer-script assertions. The tests then passed against isolated temporary project roots, never against the real development target.
- Before expanding the seed, the full-catalog test failed its expansion assertion on the old 24-answer file. Final tests check every field after real import and repeat import; IDs, timestamps, text/ID/triple uniqueness and enums; induced transaction rollback; all 182 classification pairs; every candidate index; and unchanged selection after rewriting all mechanism metadata.
- Reset tests cover absent and obsolete schemas, migration-state removal, sidecars, preservation of demonstration files, prod/test/remote/memory/alternate/traversal/query targets, symlinked ancestors and files (including dangling links), nonregular sidecars and hard links. Console tests check status codes and no connection-detail disclosure. Composer subprocess tests prove that reset/migration failure prevents later steps.
- The existing standalone demonstration initially failed because METHOD/TECH had gained exact answers. Its fallback example was changed to METHOD/SPORTS, with an original hockey question. The corrected helper passed on a new intermediate-catalog demonstration, including five offline verdicts, import validation, repeat import, database rollback, immutable snapshots, every selectable index, mechanism independence, foreign keys and reconnect durability. Original demonstration files were untouched.
- Final complete run: **`rtk proxy php vendor/bin/phpunit` — 266 tests, 48,052 assertions, all passing**, PHP 8.4.10, 17.609 seconds. Default test networking remains denied; only offline doubles were used.
- `rtk proxy composer validate --no-check-publish` passed. `rtk proxy python3 tools/build-seed-batch.py` passed, including original quotations/URLs, retained identities, current artifact consistency and historical hashes. `git diff --check` passed.

### Actual default database rebuild

The exact commands `rtk proxy composer db:reset`, `rtk proxy composer db:migrate`, and `rtk proxy composer db:seed` succeeded separately. `rtk proxy composer db:fresh` also completed reset → migrate → seed. After the final editorial exclusions, fresh was rerun and seed repeated; the complete stored catalog hash remained identical across repeat import.

[Final database evidence](content/seed-v2/batch-20261015/final-verification.json), recorded at 14:29 UTC, verifies SQLite 3.51.2 at `var/we-know.sqlite`: **1,055 answer rows, 1,055 eligibility rows, zero verdicts**; every catalog field matches the canonical JSON; table/index SQL and migration versions match fresh current migrations; `PRAGMA integrity_check` is `ok`; `PRAGMA foreign_key_check` is empty and foreign keys are enabled. No synthetic smoke content or demonstration verdicts are present. Canonical SHA-256: `6f85953e854e63729b46ad50797f39c26eeca81c828ed563604d00922a7bc0a8`.

The [earlier run record](content/seed-v2/batch-20261015/database-verification.json) deliberately retains intermediate 1,062-row results before seven final exclusions; it does not describe the final database. The unreleased initial schema needed no modification. No ALTER migration, upgrade adapter, second schema or dependency was introduced. PHP 8.5, PostgreSQL, concurrency/load, live classifier behavior and human editorial approval were not verified.

## Frontend implementation — 2026-10-15

User scope: “build frontend”, within the personal project. Implemented locally using the existing Symfony/Twig/vanilla-JS design proposal and JSON contract. No runtime dependencies, package installation, external fonts, bundler, Git delivery, deployment, paid API calls, personal configuration or Claude-file changes. Existing uncommitted catalog/database work was preserved.

### Delivered behavior

Shared dark shell and verdict card; home form and fill-only suggestions; Unicode validation/counter; one pending POST with editing lock; server-returned question/answer rendered as text; focus transfer, explicit permalink and empty new-question reset. The home URL remains unchanged and there is no local-storage history. Saved results preserve existing escaped snapshots, canonical/textual OG and noindex/no-store behavior.

Copy link includes clipboard-denial fallback with a selectable URL. About includes a native expandable FAQ; Privacy describes implemented storage/transmission/public-by-link behavior and explicitly states unresolved launch policy. Shared original-URL 404/503 pages omit exception details, while the answer API keeps JSON errors. The local PHP router now serves static assets directly. Network/invalid-success/15-second client timeout errors preserve the draft and explain possible duplicate results after a manual resubmission; POST is never retried automatically.

### New offline evidence

- Test-first HTTP check: **9 tests / 35 assertions, 7 expected failures** for the missing form, informational pages, styled errors and verdict actions. The first executable browser check also failed before frontend assets existed (CSS returned HTML). These checks preceded the implementation.
- Full `rtk proxy php vendor/bin/phpunit`: **270 tests, 48,096 assertions, all passing**, PHP 8.4.10, 7.059 seconds. Existing creation→commit→immutable escaped permalink, no-provider reads, origin/rate/body policy and persistence cases remain green.
- Browser suite: **10 tests passed**, Node 25.2.1, an existing Playwright 1.61.0 alpha installation, installed headless Chrome and fresh isolated contexts. The command used `NODE_PATH` for that existing installation and `PLAYWRIGHT_CHANNEL=chrome`; no package or browser was installed. See [reproduction and test boundary](testing.md#frontend-browser-checks--2026-10-15).
- Browser scenarios exercised one POST despite another pending submit event; faithful/safe returned text; result focus/reset; Unicode whitespace and 500/501 astral characters; rate-limit expiry without retry; controlled HTTP errors, malformed/unsafe success and network loss; successful/denied clipboard; JS-disabled saved/information pages; fill-only examples, Enter/newline and Space/button submission; 320px pages; 200% CSS zoom and a 1,500-character unbroken answer. Real local asset routing and no uncaught page errors were checked.
- Computed-style samples meet 4.5:1 text contrast and 3:1 normal field-boundary contrast. The textarea receives a visible 2px focus outline. Desktop, mobile and inline-result screenshots were visually inspected; a second mobile capture after responsive layout settled confirmed one header/form and no duplicated content.
- Read-only `rtk proxy php tools/verify-local-database.php` passed: **1,055 answers, 1,055 eligibilities, zero verdicts**, current-schema/canonical-row equality, integrity `ok`, no FK violations, zero network calls. Browser fixtures use in-memory synthetic data; no reset, migration, seed or verdict write targeted the development database.
- `rtk git diff --no-ext-diff --no-textconv --check` passed. Temporary fixture servers and isolated browsers were stopped after checks.

Limits: browser POST responses are intercepted, while PHP HTTP tests cover the real application with fake classification; no live-provider/production end-to-end claim is made. Screen-reader, Safari/Firefox, physical mobile/IME, PHP 8.5, production PostgreSQL, load and deployment checks were not run. Retention/deletion/contact/provider/operational policy still requires explicit decisions before public launch. The current answer library still needs independent human acceptance.
