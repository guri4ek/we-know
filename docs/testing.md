# Testing strategy

As of 2026-10-13. The skeleton and stage 1 have executable **Application, Unit, and Integration** PHPUnit suites. Backend selector/import/SQLite/HTTP suites are also implemented; browser, PostgreSQL and production checks remain plans. Actual runs, installed tools, and results are recorded in [verification](verification.md); planned tests do not imply implemented features.

Jev is an external classifier, not a test framework or answer generator. PHPUnit tests our PHP components. Local HTTP mocks and separately authorized real-service calls provide different integration evidence; human editorial evaluation is separate again.

## Boundaries and invariants

These are implemented classification and backend invariants:

1. The classifier returns only allowed intent/topic or typed failure; the server selects a prepared answer from the editorial library.
2. Selection uses the first nonempty eligible pool. An empty exact pool does not permit a random answer from another intent.
3. A successful answer request creates a stored result; permalink reads its snapshot without Jev or rerandomization.
4. Editing/deactivating library text must not change issued results.
5. Application policy handles Jev degradation; failed DB writes never return a false successful permalink.
6. Server validation is independent of JS. Provider secrets must not enter HTML, browser JSON, exceptions, or test artifacts.
7. Every table has UUID PK/`created_at`; eligibility retains separate unique answer/intent/topic. Humor metadata changes neither eligibility nor selection probability.

After the audit, the user accepted `decision_context` and nullable eligibility FK with ON DELETE SET NULL. SQLite snapshot/metadata/SET NULL tests are implemented; verify production PostgreSQL separately, following the [data model](architecture/data.md) and [backend implementation prompt](handoff/backend-implementation-prompt.md).

Use the [technical plan](technical-plan.md) for proposed URLs/fields/statuses. Its initial HTTP contract is implemented under the complete-backend task; precise checks/error names are in the [backend guide](backend.md).

## Test levels and actual status

| Level | Current status / objective | External dependencies | When relevant |
| --- | --- | --- | --- |
| Skeleton smoke | Existing: kernel, routes, Twig, GET/HEAD/404/405 | None | Bootstrap changes |
| Unit | Existing: taxonomy/rubric/normalizer, runner/metrics. Question validation, fallback, selector, orchestration implemented | None | Relevant logic changes |
| Jev adapter integration | Existing: real adapter + `MockHttpClient` + JSON fixtures | None | Adapter/prompt/taxonomy changes |
| Kernel integration | Existing: classification DI/test client; backend application wiring implemented | No network | Wiring changes |
| Persistence integration | Implemented real SQLite migrations/repositories/transactions; PostgreSQL outstanding | Fresh isolated SQLite; production acceptance requires PostgreSQL | Schema/query changes; release |
| HTTP answer application | Implemented: controller→service→test database, fake classifier | Test database | Creation/read flow changes |
| Browser E2E | Planned: isolated browser, DOM, fetch, navigation | Local server, fake classifier | When JS exists |
| Live Jev contract | CLI exists; two historical development batches recorded; no live PHPUnit suite | Jev, production key, network | Separately authorized model/contract evaluation |
| Quality/performance | Classification metrics implemented; development measured live. Human humor, holdout, production load unverified | Experiment-specific | Separate research runs |

Symfony provides `KernelTestCase` for integration and `WebTestCase` for HTTP application checks. Test kernel/environment configuration is explicit here because Flex is absent. [Symfony 7.4 testing](https://symfony.com/doc/7.4/testing.html).

## Existing skeleton checks

Useful tests boot/render the application rather than repeat a controller `render()` call:

- `GET /`: 200, HTML content type, `lang="en"`, product name, primary heading, exactly one `main`.
- Clear placeholder state and current `noindex, nofollow` robots copy; deliberately revisit at launch.
- `HEAD /`: 200 with page headers and no body; unknown URL→404; `POST /`→405 without business flow.
- Test kernel boots. Bootstrap regression verifies process `APP_ENV=prod` / `APP_DEBUG=0` precedence. A historical debug-off production HTTP smoke checked absence of traces; this audit did not repeat a server/browser smoke.
- No Jev/database/key/browser automation needed.

PHPUnit/BrowserKit/CssSelector/configuration are explicitly installed. Use the inspected lock and [verification](verification.md) for versions. Development/minimum is PHP **8.4**, planned **8.5**; Composer requirements/platform are corrected to PHP 8.4; the [historical audit finding](verification.md#documentation-audit-findings) is resolved in the backend event. Do not select tooling versions without checking runtime requirements.

## Backend tests: deterministic business logic

Inject boundaries only as needed: `QuestionClassifier`, `AnswerRepository`, `RandomIndexSource`, `VerdictRepository`, clock, public-ID generator. Do not add abstraction solely for tests when a simple existing contract supports substitution.

The backend tests cover the following answer-flow requirements on SQLite with fake classification/randomness:

| Subject | Scenarios and expected behavior |
| --- | --- |
| Question validation | Empty/whitespace, `null`, array, 1/500/501 Unicode code points, NUL, malformed UTF-8, unknown fields, multibyte text; 8 KiB body limit separate from character limit |
| Normalization | Contract-defined spaces/newlines, preserve punctuation/meaning; persist the trimmed snapshot; no separate original field exists |
| Taxonomy boundary / future application policy | Existing adapter rejects unknown intent/topic and ANY as invalid_response. Application records failure without invented labels and forces universal pool |
| Fallback | Exact intent/topic→intent/GENERAL→ANY/GENERAL; first nonempty stops search; GENERAL not searched twice; exclude inactive/wrong-locale text; deduplicate IDs |
| Selector | One/multiple candidates; fake random chooses first/last allowed index; invalid index is not concealed by selecting an unrelated category |
| Humor mechanism | Changing metadata does not change candidate IDs, fallback order, or weights; import rejects duplicate triple even with another mechanism |
| Accepted decision context | Exact/fallback success keeps Jev categories distinct from selected pool; tier/count/mechanism reflect actual selection. Timeout fabricates no classifier labels; omit raw response/candidate list/secrets |
| Empty library | Distinct domain and controlled HTTP error; no empty verdict |
| Application service | Success classifies/selects/writes in order; invalid input calls none |
| Jev unavailable | Agreed universal fallback; degradation reason observable; model generates no final text |
| Persistence failure | No success response or nonexistent permalink reused on retry |
| Read permalink | Stored snapshot; classifier/random spies have zero calls |

Do not assert two requests must produce different answers: repeats are valid random outcomes. Do not gate on flaky frequency histograms. Unit tests check candidate eligibility/random boundary; statistical distribution is a separate experiment if needed.

## Existing Jev adapter tests: no network

The adapter uses Symfony `HttpClientInterface`; tests pass `MockHttpClient`/`MockResponse`. Callbacks inspect requests; fixtures exercise parsing and status/transport failures including deterministic timeout. This tests our integration, not live availability. [Symfony HTTP Client testing](https://symfony.com/doc/7.4/http_client.html#testing).

Historical quickstart check, 2026-10-11: POST `https://api.typesafe.ai/v1/systemone`, Bearer credential, `state`, `model`, `questions`, Choice criteria. Fixtures implement that schema; strict parsing subsequently accepted 95 live responses across two batches. [TypeSafe quickstart](https://docs.typesafe.ai/introduction/quickstart). No external contract refresh was performed during the documentation audit.

Existing matrix:

- Method, fixed endpoint, JSON media type, fake Bearer token, configured model, rubric/taxonomy criteria. Question text does not change URL/headers.
- `state` contains only the question; no answer library, cookies, IP, humor metadata, or internal configuration.
- One request with two Choice questions. Unknown enums, missing category, invalid shape→invalid_response. Application fallback is covered separately; ready-joke/instruction text never becomes a product answer.
- Valid body, empty/malformed/oversized JSON, wrong types, missing fields/category, unknown values, extra metadata, invalid distributions/model/usage, finite numeric ranges; low confidence accepted without threshold; valid OTHER/GENERAL is success.
- 401/403/422/429/500/502/503/529, redirect, and non-200 2xx; adapter accepts exactly 200. Transport/timeout at request/body; documented empty mock chunk simulates idle timeout without sleep.
- Typed failures, with auth distinct from timeout and no raw provider body/secret disclosure.
- Exact attempt counts, retries=0, max_redirects=0; current constructor/CLI defaults **600 ms idle / 800 ms total**. Tests also explicitly pass historical 300/400 ms, proving configurable budgets rather than current defaults. Invalid budgets make no request.
- Test container wiring and deny-network default; no credential forwarding on redirects, no real token/personal question in test output.

Fixtures are small, synthetic, dated with schema provenance; [fixture README](../tests/Fixtures/Jev/README.md). No live Authorization HAR. Hand-authored fixtures are not recorded traffic or independent live-schema evidence.

## Live contract and classification quality

**Implemented stage 1:** `app:jev:evaluate`; no live tests in default PHPUnit discovery. Offline replay exercises the real adapter with mocks. Deterministic metrics and CLI preflight are tested. Live requires `--live`, explicit `--limit`, configured model and production key, and separate run authorization; no retries/sleep/parallel requests. Test services force dummy/empty credentials and deny-network client. Defaults 0.60/0.80 s; historical 0.30/0.40 remains comparison evidence. [Format/commands](evaluation/README.md), [two recorded batches](evaluation/stage-1-results.md).

The initial plan suggested `phpunit.live.xml`, `tests/Contract/Live/`, and `RUN_LIVE_JEV_TESTS=1`. **Those files/profile/flag were not implemented**; the CLI is the current opt-in path. The earlier separate-test-account/key proposal was superseded: this personal project uses its production key and no sandbox workflow. Explicit preflight failure is preferable to apparently green skipped checks; current CLI rejects missing live preconditions before network.

Future live preconditions: working adapter, configured production key, confirmed endpoint/model, explicit request budget. Bootstrap made no paid calls; stage 1 later used 108 authorized requests. Never pass the key as a CLI argument/fixture/committed `.env`; use local secret/environment. Prior budgets do not carry forward.

A **proposed** 3–5-question smoke checks availability/schema/both allowlisted dimensions with sequential one-call-per-question execution, bounded budget, no retry on 429. Do not make exact natural-language intent a hard live-contract assertion. Record date, requested/resolved model, rubric/taxonomy, status, timings without secrets. No separate 3–5-question smoke is claimed here; historical evidence is the two development batches.

The original broader quality proposal was **180–300 synthetic English questions**, not the implemented 81-question corpus. Possible future evaluation:

- Every intent/topic, ambiguity/short inputs, punctuation/typos, instruction attacks, out-of-scope and other languages.
- Multiple acceptable labels for ambiguous questions if a future version defines them; current corpus has one author-written intent/topic pair per sample. Freeze holdout and do not tune prompts on it.
- Future per-intent recall, macro F1 on unambiguous samples, acceptability on ambiguity, invalid-schema rate, P99, confidence coverage, and billing. Current runner implements intent/topic/joint accuracy with denominators, matrices, typed failure rate, nearest-rank P50/P95, known tokens. Application empty-catalog fallback rate cannot be measured before selector/catalog exist.
- Separate answer form/fit/diversity/deadpan/humor judgments. “Funny” is not a unit assertion; Codex scores do not replace humans.
- Use baseline to propose quality thresholds, then freeze them **before** the next acceptance evaluation. Do not retroactively claim a passed gate; report data/uncertainty. One stochastic mismatch is not automatically a regression.

Floating model aliases can change results without code changes. Reevaluate classification after model/schema/prompt/taxonomy changes with reproducible corpus comparisons; library changes need separate content evaluation, since the classifier never receives it.

## Persistence evidence and remaining PostgreSQL work

Run the same repository contract on both accepted engines. SQLite does not prove production PostgreSQL compatibility. Fix PostgreSQL major version after hosting choice; before that version-level acceptance remains open. DBAL repositories, migration and real SQLite tests exist. `BackendPersistenceTest`, `CatalogImportTest`, `AnswerQuestionTest` and `CatalogCommandTest` cover schema/FKs/reconnect/nonnull IDs, import atomicity/idempotency/stable identities, snapshot deletion and real failed COMMIT cleanup. PostgreSQL server, true multi-process concurrency and production query plans remain unverified.

| Scenario | Verification requirement (SQLite subset implemented) |
| --- | --- |
| Migrations | Edited unreleased migrations run on rebuilt disposable test databases on both engines. Shared/persistent data makes applied migrations immutable; new migrations upgrade prior schema preserving data. `schema:create` is not migration verification |
| Integrity | UUID PK/created_at everywhere, own eligibility UUID and UNIQUE triple, FKs, NOT NULL, agreed enum/check constraints enforced; explicitly verify SQLite FK enforcement |
| Fixtures | Enough available intent fallback; inactive/duplicate join rows do not bias pool |
| Exact selection | `(EXPLAIN, WORK_STUDY)` and `(WHEN, TECH)` do not imply `(EXPLAIN, TECH)`; collation/case does not alter enums; IDs independent of incidental row order |
| Snapshot | Editing/disabling library text leaves old permalink unchanged without revisions. For the accepted context/FK, changing categories/mechanism leaves JSON unchanged; deleting eligibility nulls FK but preserves verdict/snapshots |
| Catalog import | Answer/eligibility UUIDs and creation times stable across reimport; allowed mechanism strings checked; invalid entries leave no partial update |
| Unicode/time | Emoji, quotes, HTML-like text, UTC timestamps round-trip without silent truncation |
| Atomic write | Insert/commit failure leaves no partial result; success after commit; external HTTP holds no DB transaction |
| Public-ID collision | UUIDv4 generation; a duplicate PK fails persistence without overwriting an existing snapshot; no automatic retry |
| Concurrency | Separate connections/processes create without lost data; controlled SQLite busy/lock and PostgreSQL conflicts |
| Unavailable database | Controlled 503/agreed error, no successful JSON with inaccessible permalink |

Isolate fixtures and restore state per test. Migration/concurrency tests are separate from transaction-rollback suites: one outer rollback does not simulate independent clients. SQLite in-memory is connection-bound; use a temporary file for kernel reboot/multiple processes. PostgreSQL gets a separate test DB/schema per worker. Destructive resets require verified test environment/allowlisted DSN; tests have no production DSN. The backend task creates a separate development SQLite file; tests never reset it.

## Implemented HTTP and deferred browser acceptance

Implemented HTTP tests use fake classifier plus real migrated SQLite end-to-end: valid request→201 JSON stored result, correct Location/no-store→GET permalink→same question/answer after kernel reboot. Two new requests may have different UUIDv4 and identical answer text; both valid. Canonical/OG derive from snapshots/trusted origin; hostile Host must not replace share URL.

Proposed negative matrix: malformed JSON 400; disallowed Origin/cross-site 403; unsupported media 415; oversized body 413; invalid question 422; limit 429 + Retry-After; unknown permalink 404; unavailable DB/content 503. Invalid requests create no record/call. GET spends no provider quota. Provider failure with universal content creates persisted fallback→201, not 503. These statuses and zero-call cases are implemented and tested; no production load guarantee is inferred.

Security scenarios: Twig escaping/DOM text insertion for HTML/script; instruction strings as data; same-origin/media/rate checks before paid calls. If CSRF tokens are adopted, test missing/invalid/valid; CSRF does not replace automated-abuse controls. Production errors omit traces, DSNs, keys, raw provider errors. `100% confidence` is satirical copy only.

Browser E2E requires an isolated test profile, never personal tabs. Choose a runner (e.g. Playwright) when JS exists; this does not require a SPA/framework. No browser automation was used by this audit.

- Enter/click produces one POST; clear pending state; controls usable after success/error.
- Network error/timeout/429/503/HTML instead of JSON/malformed JSON: truthful error and preserved input.
- Double-click does not duplicate client submits. If another submit is allowed while pending, reordered responses must not let stale output overwrite the latest result. AbortController does not guarantee cancellation of a started server write.
- Reload/back/direct permalink remain server pages; share URL matches displayed result.
- Real DOM checks for keyboard focus, labels, accessible live-region messages, narrow viewport, stable critical controls.
- With JS disabled, home/permalink readable and form explains JS requirement; initial HTTP proposal has no form-encoded fallback.

## Planned performance and observability checks

Unit/integration assertions should not require arbitrary-machine wall time below 20 ms. Test budget configuration through controlled transport/fakes; measure real latency separately. Initial [context](context.md) hypotheses: backend excluding provider≤20 ms, POST P50≤300/P95≤700 ms, permalink≤100 ms. These are not accepted/achieved SLAs.

For future performance runs record supplied revision evidence, PHP/DB/model versions, machine/region, cold/warm state, dataset size, concurrency/count, errors/fallbacks, percentiles. Measure validation/provider/pool lookup/write/rendering separately; distinguish end-to-end from classifier latency. Compare normal/fallback and cold/warm DB; realistic catalog/index sizes. Do not automatically load-test Jev alongside local load tests; synthetic delay adapters cover many scenarios without provider traffic.

Future telemetry tests check useful counters/timings and low-cardinality labels. Questions, credentials, permalink IDs must not be metric labels. Distinguish invalid schema, timeout, fallback, DB failure without raw input logs by default.

## Implementation order and acceptance

Before any authorized behavior change, follow the root [test-first rule](../AGENTS.md): a focused failing test, smallest implementation, then verification. Documentation-only work uses verifiable artifact evidence.

1. **Skeleton:** existing bootstrap/HTTP smoke/config; retain verification history.
2. **Classification:** existing taxonomy/adapter/metrics, offline matrix/DI; separately authorized live baseline recorded. Holdout/human quality remains open.
3. **Domain/catalog:** implemented fallback, synthetic fixture pools, Question/selector/application tests and deterministic random boundary; follows the [technical plan](technical-plan.md) rather than silently expanding stage 1.
4. **Data + POST/permalink:** SQLite migrations/snapshots/insert-and-commit rollback/HTTP acceptance implemented; PostgreSQL and production concurrency remain outstanding.
5. **Vanilla JS:** future isolated browser success/error/race/accessibility scenarios.
6. **Release:** future production configuration, PostgreSQL/security/performance acceptance and current live-contract evidence. Separately restore backups into an isolated DB and verify snapshots/permalinks/counts. After retention/deletion decisions, verify deletion without UUID reuse. Blocked/unperformed checks are gaps, not passes.

Existing directories are `tests/Application`, `tests/Unit`, `tests/Integration`, `tests/Fixtures`; discovery includes the first three. Historical proposed `tests/Integration/Jev`, `tests/Integration/Persistence`, `tests/Functional`, `tests/Contract/Live` are not all present. Any future live tests must remain outside default discovery. Current default HTTP dependency is mock/deny-network; unplanned outbound calls fail. PostgreSQL/live checks must expose omitted execution explicitly so a green local suite cannot imply they ran.

Completion requires the task's relevant scenarios and listed exceptions/preconditions in verification. Coverage can expose gaps but does not replace immutable-permalink, fallback, or real-contract evidence. Human humor judgments, billing, and production measurements remain distinct from test-suite results.

## V2 taxonomy and seed checks

Active enums/rubric and strict normalizer cover all 182 v2 pairs. Payload tests establish the sent contract, not semantic Jev compliance. Corpus tests check 100 source-exact questions, disjoint 50/20/10/20 groups and all labels; a counting double establishes one call per question. Unsupported taxonomy/rubric and fixture combinations are rejected before classification. Synthetic response fixtures were separately authored for v2.

The [isolated seed verification](content/seed-v2/database-verification.json) checks actual migration/import, rollback, repeat import, stable UUIDs/times, all first-pool fallbacks, successful OTHER versus failure, every random index, mechanism independence and immutable saved snapshots. [Editorial matrices](content/seed-v2/editorial.md) are separate author judgments. PostgreSQL, live v2 classification and human humor/portability remain unverified.
