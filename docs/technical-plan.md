# MVP technical plan

Status: bootstrap, active [taxonomy v2](content/taxonomy-v2.md) and the scoped complete backend are implemented. The [24-answer preliminary seed](content/seed-v2/README.md) is imported into an isolated SQLite file; the [100-question live trial](evaluation/corpus-v2/README.md) is prepared but unexecuted. Catalog/migrations/import, selector, VERDICT persistence, POST and minimal permalink SSR are verified offline on SQLite. PostgreSQL, human content and launch evidence remain outstanding. The broader MVP UI remains a target: frontend is deferred with a confirmed **dark theme**. Current setup/contract: [backend guide](backend.md); actual checks: [verification](verification.md). Historical live baseline: [stage-1 results](evaluation/stage-1-results.md).

## 1. Scope

Target MVP: English home page, one question, one contextual result from a prepared library, permalink, basic About/FAQ. SSR through Symfony/Twig; vanilla JavaScript enhances the form with POST fetch. No SPA, client router, text streaming, conversation history, or generated runtime answers.

Jev is the first external classifier; its stage-1 adapter exists. Chaos, lexical/NLP, template builder, A/B tests, accounts, admin, queues, Redis, and microservices are outside the first MVP. Do not add them for abstract scalability.

## 2. Application structure

One Symfony deployable, with internal responsibilities:

| Part | Responsibility |
| --- | --- |
| `Controller` | HTTP validation, status codes, use-case invocation, JSON/Twig |
| `Application/AnswerQuestion` | Classification, fallback, selection, result persistence |
| `Domain/Classification` | Intent, Topic, classification result/status |
| `Domain/Answer` | Eligibility rules, pool selection, immutable result |
| `Infrastructure/Classification/JevClassifier` | TypeSafe HTTP contract, enum checks, timeouts, error normalization |
| `Infrastructure/Persistence` | Doctrine DBAL repositories/migrations |
| `templates/`, `public/` | SSR and a small form JS module |

This is a structural guide, not a requirement to create every directory/interface in advance. Interfaces belong at external boundaries (`QuestionClassifier`, repositories, randomness/clock for reproducible tests). Internal classes do not need interfaces by default. Classification, evaluation, domain/catalog/persistence and backend HTTP exist today; the home skeleton remains static.

Symfony 7.4 LTS was chosen at bootstrap; the [official lifecycle](https://symfony.com/releases/7.4) explains the LTS choice. Development uses locally installed **PHP 8.4**, last/current verified runtime **8.4.10**, with a planned move to **8.5**. The intended minimum is **8.4**. Composer now requires >=8.4 and resolves against platform 8.4.0. The historical [audit finding](verification.md#documentation-audit-findings) is resolved; PHP 8.5 remains unverified.

HttpClient, DBAL + Migrations, RateLimiter and Lock are installed. Input uses a small Question value object and strict JSON checks; no Validator package or ORM is needed for this scope.

## 3. Target request flow

1. `GET /` returns full HTML; JS loads as a regular module without an initial bundler.
2. The form accepts a question. Submit triggers one `POST /answer`, blocks duplicate submits, and sets `aria-busy`.
3. The server validates body, origin, and limits **before** the external API.
4. Jev classifies intent/topic in one call. The question is data and cannot change application endpoint/enums; rubric instructions tell the model to ignore injected commands, without guaranteeing semantic resistance.
5. A validated result reaches the selector, which finds the first nonempty allowed pool and selects one phrase uniformly.
6. A short transaction keeps pool selection and question/answer snapshot persistence consistent. Nullable eligibility FK with ON DELETE SET NULL and short `decision_context` JSON are now accepted for explanation; use the [data model](architecture/data.md). The Jev HTTP call is outside the DB transaction.
7. Return JSON only after successful commit. JS displays text with `textContent` and links to a server page.
8. `GET /a/{publicId}` reads only the stored result and returns HTML/OG; no Jev or random selection.

The current UI has no submit. A functional form/fetch and dark-theme design belong to the next frontend task; backend routes can already be exercised independently.

## 4. Implemented initial backend HTTP contract

### `POST /answer`

`Content-Type: application/json`, same-origin. The initial proposal has no form-encoded fallback; without JS the server home/permalink remain readable and the form explains that JS is required. A normal POST/redirect may be added separately for full non-JS support.

```json
{"question":"When will AI replace programmers?"}
```

Implemented limits: JSON object with one string; trim; 1–500 Unicode code points; maximum 8 KiB request body; reject NUL/invalid UTF-8, arrays, unknown fields. Repeat the size limit in reverse proxy and application. Result language `en`, without automatic locale selection. Other-language questions may classify, but quality is not promised before evaluation.

Success: `201 Created`, `Location: /a/{publicId}`, `Cache-Control: no-store`.

```json
{
  "id":"6d230880-6168-4c76-a18c-3fd47a059d88",
  "question":"When will AI replace programmers?",
  "answer":"Next Thursday. The date is final.",
  "url":"/a/6d230880-6168-4c76-a18c-3fd47a059d88"
}
```

This is synthetic, not a stored result. The implementation generates UUIDv4 and validates the read route. The server builds `url`, not the user/provider. Do not incorporate classifier confidence into satirical `100% confidence`.

Errors have stable shape `{"error":{"code":"invalid_question","message":"Enter a question between 1 and 500 characters."}}` without exception traces.

| Status | Case |
| --- | --- |
| 400 | Invalid JSON |
| 403 | Disallowed Origin / cross-site request |
| 413 | Oversized body |
| 415 | Non-JSON media type |
| 422 | Invalid question |
| 429 | Application limit; `Retry-After` |
| 503 | No available content or result cannot be persisted |

A Jev timeout/failure with an available universal pool is not a user HTTP failure: persist a fallback result. Database/commit failures return no false permalink. A lost response after commit can create another result on manual resubmission; automatic client POST retry is disabled. Idempotency keys are a later improvement if transparent retries are needed.

### Server pages

`GET /a/{publicId}` → `200 text/html` with saved result, canonical URL, metadata. Unknown ID → 404. Implemented policy: anyone with the URL may access it, with `noindex` and no public feed; this is not private storage. Explain question storage/sharing before submit. Defer dynamic OG images; begin with textual OG and a generic image.

`GET /about` → short satire/data explanation. Do not disguise infrastructure errors as fabricated successful verdicts. About is deferred; minimal permalink SSR is implemented.

## 5. Jev and answer selection

The implemented adapter sits behind `QuestionClassifier`. It sends only the question and short category descriptions. API key stays on the backend. The historically checked [Quick start](https://docs.typesafe.ai/introduction/quickstart) documents `POST https://api.typesafe.ai/v1/systemone`; contract/limits: [Jev research](research/jev.md).

Strictly validate response shape, allowed values, numeric ranges. Confidence does not prove correctness; choose thresholds from labeled data rather than arbitrarily. Unknown enum/incomplete/invalid results become `invalid_response`, not successful classification. The application sends failure **directly** to `(ANY, GENERAL)`, skipping normal tiers and recording no fabricated labels. Valid `OTHER / GENERAL` follows normal tiers. Low-confidence policy is future work requiring calibration; the adapter has no threshold.

Pool selection: `(intent, topic)` → `(intent, GENERAL)` → `(ANY, GENERAL)`. For `GENERAL`, avoid repeated identical searches. Deduplicate answer IDs so multiple associations do not increase an answer's probability. Use the first nonempty pool rather than merging tiers. Equal weights for MVP, without hidden rankings.

Accepted `ANSWER_ELIGIBILITY.humor_mechanism` is prepared alongside content and ignored by the selector. Stored diagnostics distinguish Jev categories from the selected pool, recording tier, unique candidate count, and selected eligibility mechanism. The mechanism supports future statistics, not selection. Likes/votes and owner analytics are unimplemented/deferred.

For a small catalog, read active candidates from the database and select an index with `random_int`. Avoid DB-specific `ORDER BY RANDOM()` across the entire table and random min/max IDs with gaps. Cache pool indexes only after profiling, versioned with the catalog.

## 6. Timing budget and resilience

Targets, not guarantees: backend work excluding Jev ≤20 ms; full POST P50 ≤300 ms / P95 ≤700 ms; permalink server response ≤100 ms. Measure cold/warm separately; Xdebug off, `APP_ENV=prod`, `APP_DEBUG=0`, OPcache on. Compare production region and a browser in Warsaw.

The initial Jev spike used `timeout=0.30 s`, `max_duration=0.40 s`. After two development live batches Serhii chose **0.60/0.80 s current configurable defaults**, retries=0. [Symfony HttpClient](https://symfony.com/doc/7.4/http_client.html) distinguishes idle timeout from full duration; `timeout` alone is insufficient. The [comparison](evaluation/stage-1-results.md) measures technical failures, not empty-catalog fallback. It does not establish a production SLA: future validation, DB read/write, and network also consume the total budget.

Distinguish timeout, auth/configuration failure, throttling, invalid response, empty catalog in future metrics. Do not log raw questions/keys. Proposed logs: request ID, durations, actual model, pool tier, fallback reason if present. This does not require separate version/status VERDICT columns; the data model specifies the accepted minimal historical context. Provider 401/422 indicate configuration/contract errors, not infinite retry. Add circuit breakers/shared cache only for demonstrated need.

## 7. Public endpoint and data

The server accepts JSON only from the explicitly configured Origin; block cross-site browser requests and keep CORS closed. This addresses cross-site browsers rather than bots; separate rate limiting and total API budgets are needed. Revisit CSRF if cookie sessions, authentication, or form POST are added.

Trusted proxies default to none; the initial configured trusted-header policy accepts only X-Forwarded-For from configured addresses. See the backend guide for TLS proxy/production gaps. A single instance may use a file limiter; multiple instances need shared storage. Set limits from load evidence, not arbitrary business constants.

Twig autoescape, JS `textContent`, parameterized SQL, no raw HTML in the library. Questions may contain personal data; store only what is necessary and document transmission to Jev/deletion policy before public launch. Answer immutability prevents rerandomization but does not prohibit product-policy deletion.

## 8. Stages and exit criteria

| Stage | Output | Exit criterion / current evidence |
| --- | --- | --- |
| 0. Bootstrap | Minimal Symfony/Twig and local docs | Kernel boots, GET/HEAD work, placeholder rejects POST, documents link correctly; verified historically and by current functional tests |
| 1. Historical content and Jev spike | [Taxonomy v1](content/taxonomy-v1.md), 54 development + 27 holdout, adapter/runner; offline implementation and historical development live baseline | One call with both Choice questions confirmed by mocks and 95 live successes in two batches. Model pin, accuracy, latency, technical failure rate recorded. Holdout/human quality and production latency unverified; empty-catalog fallback does not exist here |
| 2. Domain and catalog | Implemented selector, explicit associations, DB migrations/import | Offline selector/import and real SQLite constraints/FKs pass; preliminary editorial seed with universal fallback, checked in an isolated database. PostgreSQL and human catalog acceptance outstanding |
| 3. Vertical flow | Implemented POST, classifier boundary, immutable persistence, JSON | Offline status/origin/rate/body, fallback and insert/commit rollback tests pass; 201 only after commit |
| 4. UI and sharing | Minimal stored-result SSR/textual OG implemented; dark-theme form/fetch/About deferred | Escaping and GET/HEAD without provider/random verified; browser UI acceptance remains future work |
| 5. Launch readiness | Deployment config, telemetry, backups, data policy | Future measured load targets, backup restore, production PostgreSQL integration matrix |

Data-stage requirements: UUID PK and `created_at` everywhere; separate eligibility UUID with unique triple; no ANSWER.revision/VERDICT.answer_revision. Before production, edit development migrations in place/rebuild disposable dev/test databases. Shared/persistent data requires new incremental migrations and upgrade checks. The local initial migration is implemented. Schema finalization preserved all generated business rows and retained the prior local file; no deployment occurred.

Do not implement later stages based on this plan alone. Stage-1 authorization covered scoped implementation and separately two batches of at most 54 development live calls. Those historical budgets are exhausted; new live runs need a new bounded authorization using the same production key. The documentation audit was historical/read-only; the later explicit backend task authorized and completed local implementation. Form/fetch/design remain unimplemented. [Testing strategy](testing.md) separates existing suites from planned checks.
