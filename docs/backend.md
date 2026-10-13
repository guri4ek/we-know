# Backend setup and contract

Active taxonomy/rubric **2.0.0**, catalog format **2**. The [isolated editorial seed](content/seed-v2/README.md) is locally imported and offline checked; it is not human-approved. The existing development database remains untouched and is not automatically upgraded.

Implemented locally on 2026-10-11 under the [backend task](handoff/backend-implementation-prompt.md). PHP 8.4 minimum, Symfony 7.4, Doctrine DBAL + Migrations without ORM. Verification and remaining gaps: [record](verification.md#backend-implementation-verification-event). Home/form/fetch/design are deferred; the next frontend must use a **dark theme**.

## Development database and offline reproduction

Nonsecret defaults are in `.env`; existing local credentials were not read or rewritten. `DATABASE_URL` defaults to `sqlite:///%kernel.project_dir%/var/we-know.sqlite`. The actual local file is `/Users/serhii/OwnProjects/we-know/var/we-know.sqlite`, ignored and outside the document root. Check the intended database before migrating; migration setup never deletes existing data.

```sh
rtk proxy php vendor/bin/phpunit
# Create a NEW isolated editorial demonstration using the existing migration/import path:
rtk proxy php tools/verify-seed-v2.php /Users/serhii/OwnProjects/we-know/var/answer-seed-v2-recheck.sqlite
```

The helper refuses existing targets. The v2 task updated the unreleased initial migration directly; an old file already marked with that migration ID is not upgraded by rerunning it. Keep the preserved development file separate. For later application use, explicitly set DATABASE_URL to the intended v2 database; no default target was changed in this task.

The fixture flag explicitly replaces the development catalog with five synthetic answers / six eligibility rows. It is rejected in `prod`; importing files from the fixture directory without that flag is rejected. The offline smoke does not load Dotenv or secrets, explicitly imports the same synthetic catalog, uses a fake timeout classifier, creates one durable verdict, and reads it through another connection. Each run adds one synthetic verdict. The old development file is not a v2 target; run synthetic smoke only after separately preparing a compatible disposable synthetic database. This task did not run it. It is not an editorial or production setup step.

PHPUnit forces a separate SQLite connection configuration in `test`, ignoring development/production database environment variables. Backend tests normally inject freshly migrated in-memory databases; the reboot test uses a uniquely named temporary file. The default test HttpClient denies unexpected outbound calls. No new live Jev calls are authorized or required.

## Production configuration boundaries

Production requires a `postgresql://` PDO connection via secret environment configuration, `pdo_pgsql`, `APP_ENV=prod`, `APP_DEBUG=0`, and the actual canonical `APP_PUBLIC_ORIGIN` (scheme, host, optional port; no trailing slash/path). SQLite is rejected in `prod`. No production database connection, deployment or PHP 8.5 run occurred here. A local prod/debug-off HTTP smoke used a synthetic unavailable DSN and a forcibly empty Jev key to verify controlled errors. After backend/config changes, clear and rebuild the local prod cache before judging debug-off behavior; a stale pre-backend cache can still serve old routes.

`TRUSTED_PROXIES` is empty by default: forwarded headers from arbitrary clients cannot change the IP used by the limiter. The explicit initial trusted-header policy accepts only `X-Forwarded-For` from configured proxy addresses. Keep proxies narrow and configure the real topology before launch. A TLS-terminating proxy must either preserve the correct application scheme or separately configure trusted scheme/port headers; those are not currently trusted. Host/canonical links are never taken from forwarded host headers.

`ANSWER_RATE_LIMIT=10` and `ANSWER_RATE_INTERVAL="1 minute"` are configurable **local defaults**, not measured production limits. The fixed-window limiter counts validated submissions per client IP before classification. State is stored in `var/rate-limit/{environment}` independently of kernel cache, with filesystem locks. This is for a single application host; multiple hosts require shared state/locks. Failures in limiter storage are controlled 503s, never permission to bypass the limiter. Reverse-proxy body/rate controls, load testing and total paid API budgets remain launch work.

## Catalog format v2

`app:catalog:import <file.json>` atomically replaces the entire catalog. Omitted answers/eligibilities are deleted; verdicts remain. Review the complete file before import. The importer does not merge a partial patch. Reimport preserves existing creation times and does not duplicate candidates. Existing answer IDs cannot change creation time; existing eligibility IDs cannot change creation time or answer ownership. A changed triple may keep its eligibility ID if it remains unique. Reassigning/swapping triples that temporarily conflict is rejected atomically; issue new eligibility IDs for changed associations. Deleted IDs must not be reused by content authors; there is no tombstone registry in this initial schema.

Exact fields are required, with no unknown fields. `version` is integer 2 (the active taxonomy 2.0.0 contract; other versions are rejected), `answers` is a list. Each answer has `id`, `text`, `locale`, `enabled`, `created_at`, and a nonempty `eligibility` list. Each eligibility has its own `id`, `intent`, `topic`, `humor_mechanism`, `created_at`. UUIDs are canonical lowercase RFC variant UUIDs (versions 1–8); public verdict IDs are generated UUIDv4. Creation times are valid UTC seconds, exactly `YYYY-MM-DDTHH:MM:SSZ`; reimport rejects changes rather than rewriting them. Text is nonblank valid UTF-8 without NUL, locale is `en`, enabled is boolean.

Synthetic format example, not production content:

```json
{
  "version": 2,
  "answers": [
    {
      "id": "10000000-0000-4000-8000-000000000005",
      "text": "Synthetic backend answer 5.",
      "locale": "en",
      "enabled": true,
      "created_at": "2026-10-11T00:00:00Z",
      "eligibility": [
        {
          "id": "20000000-0000-4000-8000-000000000050",
          "intent": "ANY",
          "topic": "GENERAL",
          "humor_mechanism": "metaphor",
          "created_at": "2026-10-11T00:00:00Z"
        }
      ]
    }
  ]
}
```

Allowed classifier labels come from [taxonomy v2](content/taxonomy-v2.md). Eligibility adds only `(ANY, GENERAL)`; there is no topic ANY. The provisional implementation mechanism allowlist is `metaphor`, `hyperbole`, `oxymoron`, `paradox`, `false_causality`, `false_authority`, `reversal`, `bureaucratic_absurdity`. These are existing proposed strings, not independently approved editorial taxonomy. Mechanisms never filter or weight selection. At least one enabled English universal answer is required. Duplicate IDs/triples, malformed input and invalid records reject the entire import. Creation/ownership checks run inside the transaction; database errors roll it back.

The [development fixture](../tests/Fixtures/Catalog/development-v2.json) contains deliberately plain synthetic test strings, independent of research hold drafts. Read the [authoring guide](content/answer-generation-guide.md) before real content work. Human quality, pool portability and sensitive-question policy remain outstanding.

## Selection and immutable persistence

Unique enabled English answer IDs are selected uniformly using `random_int(0, count - 1)` in the **first nonempty** pool: exact intent/topic, intent/GENERAL, ANY/GENERAL. GENERAL is not looked up twice. Valid OTHER follows those tiers; classification failure goes directly to universal. Empty universal content produces a controlled failure. `RandomIndexSource` supplies deterministic first/last indices in tests.

The Jev call completes before the short transaction. PostgreSQL locks the candidate answer/eligibility rows with `FOR SHARE`; SQLite holds its transaction snapshot and returns a controlled failure on a conflicting write. The selected text, eligibility ID and [diagnostic context](architecture/data.md#accepted-short-decision_context) are persisted before success. A failed insert or COMMIT returns no permalink. Cleanup explicitly handles SQLite deferred-FK commit failures, whose native transaction can remain open after DBAL decrements its nesting level.

Editing/disabling text or changing eligibility metadata does not change saved snapshots. Deleting eligibility (including cascading answer deletion) sets the verdict FK to NULL and preserves text/context. There is no update operation on `VerdictRepository`. Public reads load only stored snapshots and never instantiate classification/selection work.

## HTTP contract

`POST /answer` requires `Content-Type: application/json` (UTF-8), an `Origin` exactly equal to configured `APP_PUBLIC_ORIGIN`, and matching request scheme/host. Missing/null/mismatched Origin is rejected, including non-browser callers unless they supply it explicitly. If `Sec-Fetch-Site` is present it must be `same-origin`. CORS is closed. This is a browser-origin check, not bot authentication.

The body is read at most 8193 bytes to enforce an 8192-byte limit even without Content-Length. JSON must be an object containing only string `question`. Unicode whitespace is trimmed; the result must contain 1–500 Unicode code points and no NUL. Malformed JSON/UTF-8 is rejected before Jev, as are media/origin/validation/rate failures.

Success is `201` with only server-built `id`, trimmed `question`, selected `answer`, and relative `url`; `Location: /a/{id}`, `Cache-Control: no-store`. Symfony also marks the response private. It follows transaction commit. Universal fallback after a Jev failure is still a persisted 201.

Errors use `{"error":{"code":"...","message":"..."}}`, without exception details or provider data:

| Status | Code | Case |
| --- | --- | --- |
| 400 | `invalid_json` | Malformed JSON or invalid UTF-8 |
| 403 | `forbidden_origin` | Missing/disallowed origin, host/scheme mismatch, cross-site fetch |
| 413 | `body_too_large` | Body or declared length above 8 KiB |
| 415 | `unsupported_media_type` | Non-JSON media type or non-UTF-8 charset |
| 422 | `invalid_question` | Wrong object/fields/type, blank/long/NUL question |
| 429 | `rate_limited` | Application limit; integer `Retry-After` seconds |
| 503 | `answer_unavailable` | Empty content, persistence/commit/limiter failure |

If more than one validation rule fails, origin is checked first, then media, size, JSON, question, and rate. The endpoint exposes no diagnostic JSON. POST retry is not automatic; a response lost after commit can lead to another result on resubmission.

`GET`/`HEAD /a/{publicId}` returns a minimal escaped English Twig page with stored text, canonical URL and textual OG from trusted configuration. Invalid/non-v4/unknown IDs return 404. Pages have noindex/nofollow, no-store and no public feed; they are accessible to anyone with the URL. Unavailable persistence returns a plain controlled 503. No form/fetch, styling, dynamic OG image, About/FAQ, deployment or public-launch privacy policy was added.
