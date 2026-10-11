# Actual results and verification

Date: 2026-10-11. Working directory: `/Users/serhii/OwnProjects/we-know`.

**Current implementation:** scoped stage 1, offline suites, and two historical development live batches. See [stage 1](#stage-1-content-and-jev-spike) and [comparison](evaluation/stage-1-results.md). Skeleton/baseline sections retain earlier verification and pre-stage-1 state. The [documentation-audit event](#documentation-audit-verification-event) is a separate current check, not a replacement live measurement.

**Latest user decisions:** personal project/account; development/minimum PHP 8.4, planned PHP 8.5; locally installed PHP; SQLite local and PostgreSQL production, definite; production Jev key for authorized evaluation, no separate test key/sandbox workflow. Composer's remaining 8.2 minimum/platform is an [error for a separate prompt](#documentation-audit-findings).


## Current status by evidence

| Area | Status | Evidence / limitation |
| --- | --- | --- |
| Kernel/Twig/home placeholder | Implemented and offline tested | GET/HEAD/404/405 and bootstrap tests; historical production HTTP smoke |
| Taxonomy/rubric/corpus and Jev adapter/DI | Implemented and offline tested | Version 1.0.0, 81 synthetic questions; strict request/response/error tests; labels not independently reviewed |
| CLI/metrics | Implemented and offline tested | Actual adapter fixture replay, deterministic denominator/matrix/percentile tests |
| Jev live classification | Historically measured | Two repeated 54-question development batches; no new calls in this audit |
| Humor/eligibility | Proposed editorial content | Hold drafts, known failed probes; no independent human humor assessment or import |
| Selector/database/import/answer HTTP/fetch/permalink | Not implemented | Target plan only; no product acceptance evidence |
| decision_context and final mechanism codes | Proposed, unapproved | Accepted high-level schema boundaries do not approve final JSON/enum |
| Chaos/NLP/weights/admin/likes/queues/cache | Deferred | No implementation authorized by documentation |
| PHP 8.5, holdout, production load, billing/retention | Unverified | Separate future work; Composer's erroneous 8.2 baseline awaits its own correction prompt |

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
