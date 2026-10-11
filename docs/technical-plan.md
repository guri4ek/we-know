# MVP technical plan

Status: the target MVP architecture remains proposed. Bootstrap and scoped stage 1 are implemented: taxonomy/corpus, Jev adapter, tests, evaluation runner. Historical live baseline/limits: [stage-1 results](evaluation/stage-1-results.md); checks: [verification](verification.md). Original decisions: [context](context.md). Accepted requirements below do not imply implemented features or authorize the next stage.

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

This is a structural guide, not a requirement to create every directory/interface in advance. Interfaces belong at external boundaries (`QuestionClassifier`, repositories, randomness/clock for reproducible tests). Internal classes do not need interfaces by default. Only classification, evaluation, and the home skeleton exist today.

Symfony 7.4 LTS was chosen at bootstrap; the [official lifecycle](https://symfony.com/releases/7.4) explains the LTS choice. Development uses locally installed **PHP 8.4**, last/current verified runtime **8.4.10**, with a planned move to **8.5**. The intended minimum is **8.4**. Composer still permits PHP 8.2 and resolves against platform 8.2.0: the user identified this as an error for separate correction, documented in [audit findings](verification.md#documentation-audit-findings). Neither a real PHP 8.2 run nor the PHP 8.5 transition has been verified.

Add packages by stage: HttpClient for Jev is already installed; Validator for input, DBAL + Migrations for databases, RateLimiter for public POST remain future work. ORM is unnecessary for the initial three-table proposal; reconsider if the model becomes complex.

## 3. Target request flow

1. `GET /` returns full HTML; JS loads as a regular module without an initial bundler.
2. The form accepts a question. Submit triggers one `POST /answer`, blocks duplicate submits, and sets `aria-busy`.
3. The server validates body, origin, and limits **before** the external API.
4. Jev classifies intent/topic in one call. The question is data and cannot change application endpoint/enums; rubric instructions tell the model to ignore injected commands, without guaranteeing semantic resistance.
5. A validated result reaches the selector, which finds the first nonempty allowed pool and selects one phrase uniformly.
6. A short transaction keeps pool selection and question/answer snapshot persistence consistent. Nullable eligibility FK and short `decision_context` JSON are proposed for explanation; agree structure through the [data model](architecture/data.md). The Jev HTTP call is outside the DB transaction.
7. Return JSON only after successful commit. JS displays text with `textContent` and links to a server page.
8. `GET /a/{publicId}` reads only the stored result and returns HTML/OG; no Jev or random selection.

The current UI has no submit. Add a functional form alongside working backend behavior; do not present a simulated completed service.

## 4. Proposed HTTP contract

### `POST /answer`

`Content-Type: application/json`, same-origin. The initial proposal has no form-encoded fallback; without JS the server home/permalink remain readable and the form explains that JS is required. A normal POST/redirect may be added separately for full non-JS support.

```json
{"question":"When will AI replace programmers?"}
```

Proposed limits: JSON object with one string; trim; 1–500 Unicode code points; maximum 8 KiB request body; reject NUL/invalid UTF-8, arrays, unknown fields. Repeat the size limit in reverse proxy and application. Result language `en`, without automatic locale selection. Other-language questions may classify, but quality is not promised before evaluation.

Success: `201 Created`, `Location: /a/{publicId}`, `Cache-Control: no-store`.

```json
{
  "id":"6d230880-6168-4c76-a18c-3fd47a059d88",
  "question":"When will AI replace programmers?",
  "answer":"Next Thursday. The date is final.",
  "url":"/a/6d230880-6168-4c76-a18c-3fd47a059d88"
}
```

This is synthetic, not a stored result. The future implementation uses UUIDv4 and validates the route. The server builds `url`, not the user/provider. Do not incorporate classifier confidence into satirical `100% confidence`.

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

Under the future policy, a Jev timeout/failure with an available universal pool is not a user HTTP failure: persist a fallback result. If the database is unavailable, return no false permalink. A lost response after commit can create another result on manual resubmission; automatic client POST retry is disabled. Idempotency keys are a later improvement if transparent retries are needed.

### Server pages

`GET /a/{publicId}` → `200 text/html` with saved result, canonical URL, metadata. Unknown ID → 404. Initial proposal: anyone with the URL may access it, but `noindex` and no public feed; this is not private storage. Explain question storage/sharing before submit. Defer dynamic OG images; begin with textual OG and a generic image.

`GET /about` → short satire/data explanation. Do not disguise infrastructure errors as fabricated successful verdicts. Neither page exists yet.

## 5. Jev and answer selection

The implemented adapter sits behind `QuestionClassifier`. It sends only the question and short category descriptions. API key stays on the backend. The historically checked [Quick start](https://docs.typesafe.ai/introduction/quickstart) documents `POST https://api.typesafe.ai/v1/systemone`; contract/limits: [Jev research](research/jev.md).

Strictly validate response shape, allowed values, numeric ranges. Confidence does not prove correctness; choose thresholds from labeled data rather than arbitrarily. Unknown enum/incomplete/invalid results become `invalid_response`, not successful classification. The **future** application policy may use an explicit failure context `OTHER / GENERAL`; with failure status the selector goes **directly** to `(ANY, GENERAL)`, skipping normal tiers. Valid `OTHER / GENERAL` follows normal tiers. Low-confidence policy is future work requiring calibration; the adapter has no threshold.

Pool selection: `(intent, topic)` → `(intent, GENERAL)` → `(ANY, GENERAL)`. For `GENERAL`, avoid repeated identical searches. Deduplicate answer IDs so multiple associations do not increase an answer's probability. Use the first nonempty pool rather than merging tiers. Equal weights for MVP, without hidden rankings.

Accepted `ANSWER_ELIGIBILITY.humor_mechanism` is prepared alongside content and ignored by the future selector. Proposed diagnostics distinguish Jev categories from the selected pool, recording tier, unique candidate count, and selected eligibility mechanism. The mechanism supports future statistics, not selection. Likes/votes and owner analytics are unimplemented/deferred.

For a small catalog, read active candidates from the database and select an index with `random_int`. Avoid DB-specific `ORDER BY RANDOM()` across the entire table and random min/max IDs with gaps. Cache pool indexes only after profiling, versioned with the catalog.

## 6. Timing budget and resilience

Targets, not guarantees: backend work excluding Jev ≤20 ms; full POST P50 ≤300 ms / P95 ≤700 ms; permalink server response ≤100 ms. Measure cold/warm separately; Xdebug off, `APP_ENV=prod`, `APP_DEBUG=0`, OPcache on. Compare production region and a browser in Warsaw.

The initial Jev spike used `timeout=0.30 s`, `max_duration=0.40 s`. After two development live batches Serhii chose **0.60/0.80 s current configurable defaults**, retries=0. [Symfony HttpClient](https://symfony.com/doc/7.4/http_client.html) distinguishes idle timeout from full duration; `timeout` alone is insufficient. The [comparison](evaluation/stage-1-results.md) measures technical failures, not empty-catalog fallback. It does not establish a production SLA: future validation, DB read/write, and network also consume the total budget.

Distinguish timeout, auth/configuration failure, throttling, invalid response, empty catalog in future metrics. Do not log raw questions/keys. Proposed logs: request ID, durations, actual model, pool tier, fallback reason if present. This does not require separate version/status VERDICT columns; the data model proposes minimal historical context. Provider 401/422 indicate configuration/contract errors, not infinite retry. Add circuit breakers/shared cache only for demonstrated need.

## 7. Public endpoint and data

The proposed server accepts JSON only from allowed Origin; block cross-site browser requests and keep CORS closed. This addresses cross-site browsers rather than bots; separate rate limiting and total API budgets are needed. Revisit CSRF if cookie sessions, authentication, or form POST are added.

Define trusted proxies before IP-based limits. A single instance may use a file limiter; multiple instances need shared storage. Set limits from load evidence, not arbitrary business constants.

Twig autoescape, JS `textContent`, parameterized SQL, no raw HTML in the library. Questions may contain personal data; store only what is necessary and document transmission to Jev/deletion policy before public launch. Answer immutability prevents rerandomization but does not prohibit product-policy deletion.

## 8. Stages and exit criteria

| Stage | Output | Exit criterion / current evidence |
| --- | --- | --- |
| 0. Bootstrap | Minimal Symfony/Twig and local docs | Kernel boots, GET/HEAD work, placeholder rejects POST, documents link correctly; verified historically and by current functional tests |
| 1. Content and Jev spike | [Taxonomy v1](content/taxonomy-v1.md), 54 development + 27 holdout, adapter/runner; offline implementation and historical development live baseline | One call with both Choice questions confirmed by mocks and 95 live successes in two batches. Model pin, accuracy, latency, technical failure rate recorded. Holdout/human quality and production latency unverified; empty-catalog fallback does not exist here |
| 2. Domain and catalog | Selector, associations, DB migrations/import | Future unit fallback/random and SQLite/PostgreSQL integration tests; nonempty universal pool |
| 3. Vertical flow | POST, existing Jev adapter, persistence, JSON | Future provider/DB error tests; permalink issued only after commit |
| 4. UI and sharing | Vanilla fetch, result, permalink, About, OG | Future XSS/duplicate-submit/error/retry UX checks; GET never calls Jev |
| 5. Launch readiness | Deployment config, telemetry, backups, data policy | Future measured load targets, backup restore, production PostgreSQL integration matrix |

Data-stage requirements: UUID PK and `created_at` everywhere; separate eligibility UUID with unique triple; no ANSWER.revision/VERDICT.answer_revision. Before production, edit development migrations in place/rebuild disposable dev/test databases. Shared/persistent data requires new incremental migrations and upgrade checks. This is future practice; no database reset, migrations, or deployment are performed here.

Do not implement later stages based on this plan alone. Stage-1 authorization covered scoped implementation and separately two batches of at most 54 development live calls. Those historical budgets are exhausted; new live runs need a new bounded authorization using the same production key. The documentation audit authorizes documentation/project-instruction work and offline validation only. Selector, database/import, VERDICT, POST/fetch, and permalink remain unimplemented. [Testing strategy](testing.md) separates existing suites from planned checks.
