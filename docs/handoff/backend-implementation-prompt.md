# Next session: implement the complete backend

## Task and authorization

Work in `/Users/serhii/OwnProjects/we-know`. Read this file completely and implement the scoped work below, rather than stopping at a plan or review.

This is Serhii's **personal project and personal account**. Do not import WHMCS/work-account reviewer-role defaults as this project's role. This new explicit task authorizes local backend implementation, necessary Composer changes/dependencies, database configuration/migrations, creating the local SQLite database, tests, and related English documentation. Earlier audit-only permissions were historical and do not restrict this newly authorized implementation.

Communicate in **Ukrainian**. Write documentation/instructions in **English**; product strings are English. Read root [AGENTS.md](../../AGENTS.md) and `/Users/serhii/.codex/RTK.md`. Prefix every shell command with `rtk`, normally `rtk proxy`. Work directly without subagents. Do not run Git commands or create/switch branches/worktrees, stage, commit, push, manage PRs, or deploy. Preserve existing work, including concurrent changes.

Claude-specific files/backups are strictly read-only, including indirect generator/installer writes. Personal configuration/skills/backups are outside this task. Do not inspect, print, copy, or rewrite `.env.local` or credentials. Add nonsecret database defaults separately without overwriting the existing Jev secret. Never expose secrets through DSNs, exceptions, logs, fixtures, or reports.

**Frontend implementation is deferred.** Serhii wants a **dark theme** in the later frontend stage. Record this requirement, but do not style/redesign the home page, add a functional form/fetch, a JS framework/build pipeline, or a theme toggle here. A minimal Twig view required to read a persisted permalink is within backend scope; it is not the frontend-design stage.

## Confirmed decisions — do not request the same approval again

Serhii explicitly confirmed all four:

1. **Doctrine DBAL + Migrations, without ORM.** Create domain PHP models/value objects and repositories, not ORM entities/metadata. Use only the abstractions needed for actual boundaries/tests.
2. **VERDICT:** UUID `id`, `question_snapshot`, `answer_snapshot`, nullable `answer_eligibility_id`, `decision_context` JSON, UTC `created_at`. FK policy **ON DELETE SET NULL**. No revision fields. Editing/disabling/deleting catalog entries must not rewrite historical text or diagnostic snapshots.
3. **Diagnostic minimum:** classifier provider when actually called, requested/resolved model, validated intent/topic **or** failure reason; selection tier, fallback reason when applicable, actual pool intent/topic, unique candidate count, algorithm, selected eligibility humor mechanism. No raw response, full candidate list, or confidence unless a future decision depends on it. Use consistent documented JSON names; never fabricate successful OTHER/GENERAL labels on failure.
4. **Complete backend**, following the Composer correction: catalog, migrations, import, selector, VERDICT persistence, `POST /answer`, and reading a stored result. This extends stages 2–3 and includes the backend permalink route. Frontend/theme remains later.

Development and minimum runtime: **locally installed PHP 8.4**, planned transition to **8.5**. Correct `composer.json` from `require.php >=8.2` / platform `8.2.0` to minimum `>=8.4` / platform `8.4.0`, reconciling the lock and checking actual installed runtime. Do not upgrade PHP globally or claim 8.5 compatibility was tested unless it actually was.

Database choices are final: **SQLite locally, PostgreSQL in production**. SQLite belongs under ignored `var/`, outside `public/`; return its exact absolute path and demonstrate the file and migrated tables exist. No SQLite file or persistence implementation existed at the handoff: this is missing implementation, not a hidden database to locate.

Jev uses **one production key** for application and authorized live evaluation, no separate test key/account/sandbox workflow. No new paid live calls are authorized by this prompt. Default tests/acceptance must use mocks/fakes and deny unexpected network. Real cost evaluation is a separate bounded task; prior 108-call budgets are exhausted.

## Read before implementation

Inspect actual files rather than assuming this snapshot remains exact. Read [documentation map](../README.md), [context](../context.md), [technical plan](../technical-plan.md), [data model](../architecture/data.md), [decisions](../architecture/decisions.md), [taxonomy v1](../content/taxonomy-v1.md), [testing strategy](../testing.md), [verification/findings](../verification.md), and [stage-1 results](../evaluation/stage-1-results.md). Inspect manifest/lock, service/test configuration, existing implementation, and tests.

Read the [answer authoring guide](../content/answer-generation-guide.md) **in full** before writing/editing answer content; consult [humor research](../research/humor-mechanisms.md). Existing research examples are `hold` drafts with known failed probes, not approved production seed/import data. Do not silently promote them, alter frozen corpora/rubric, or modify original live reports.

## Existing implementation and evidence

Implemented: Symfony 7.4/Twig home skeleton; taxonomy/rubric/corpus 1.0.0 (9 intents, 7 topics; 54 development + 27 holdout); typed classifier boundary; Jev HttpClient adapter/strict normalizer/DI; CLI evaluation. Jev performs **one POST with two Choice questions**, no retries/redirects/confidence threshold. Defaults **600 ms idle / 800 ms max_duration**, model pin `jev-1.13.0`. Preserve these unless a separate concrete requirement calls for a change.

Latest audit PHPUnit: **156 tests, 561 assertions, pass**, PHP 8.4.10. Composer, YAML/DI, offline replay, local Markdown/JSON/shell/Mermaid checks passed in that audit; these are historical evidence, not this session's results. No database, migrations/import, selector, verdict flow, POST/fetch, or permalink was implemented then. Check current files before acting.

## Required backend

### Runtime and persistence foundation

Correct the PHP manifest/platform finding and reconcile the lock with a targeted dependency operation. Install only needed DBAL/Migrations and other directly required Symfony-compatible packages; verify official PHP/Symfony compatibility before choosing versions. Avoid unrelated dependency churn, Flex recipe assumptions, or runtime/browser tooling.

Configure database connections, domain models, repositories, and actual migration commands. Create the local SQLite file by applying migrations, not by touching an empty placeholder. Before migration/reset, check the intended local/test database path and preserve any existing data; destructive reset is not routine setup. Keep test databases isolated from development and production. Explicitly enable and test SQLite FK enforcement on each connection.

Implement:

- **ANSWER:** own UUID PK, text, locale, enabled, UTC created_at.
- **ANSWER_ELIGIBILITY:** own UUID PK, answer FK, intent, topic, humor_mechanism, UTC created_at; unique `(answer_id, intent, topic)` and lookup index `(intent, topic, answer_id)`. Store explicit pairs, never a Cartesian product of independent sets. Mechanism is eligibility metadata, excluded from selection weights/filtering and uniqueness.
- **VERDICT:** the confirmed fields/FK/JSON above, immutable snapshots, unpredictable UUIDv4 public ID.

`ANY` is eligibility-only, never add it to classifier Intent/Topic or Jev criteria. `OTHER` is a valid classified intent. No topic ANY. For provisional import mechanism validation, use existing documented proposed strings and record the allowlist as an implementation choice; do not claim independently approved editorial quality or redesign the humor research.

Support SQLite and PostgreSQL deliberately: portable UUID/time/JSON representations and platform-specific migration SQL where required. Do not claim PostgreSQL was tested from SQLite results. Use an available isolated local PostgreSQL test instance if present; do not install/start global services or touch production to obtain one. If unavailable, finish portable implementation/SQLite checks and identify PostgreSQL acceptance as an explicit evidence gap.

### Catalog import and content boundaries

Define a small documented versioned content format and atomic, idempotent CLI import. Validate UUIDs/stable IDs, creation times, text, `en`, allowed intent/topic/mechanism, duplicate triples, and at least one enabled `(ANY, GENERAL)` answer. Reimport must not rewrite creation times or create duplicate candidates. One invalid record must not leave a partial catalog. Respect existing verdict FKs/snapshots when catalog rows change or are removed.

Use clearly separated **synthetic development/test catalog fixtures** to exercise the backend now, including a universal pool. They are not a production-approved answer library. Do not import research hold drafts as approved content or introduce hidden runtime exclusions to rescue them. Keep fixture loading explicit and distinct from production setup. Human editorial content acceptance remains outstanding; do not let it block testing the backend or falsely claim it has occurred.

### Selection and answer creation

Select uniformly among unique enabled English answer IDs in the **first nonempty** pool: `(intent, topic)` → `(intent, GENERAL)` → `(ANY, GENERAL)`. Avoid duplicate GENERAL lookup; do not merge tiers. On classification failure, go directly to `(ANY, GENERAL)` and retain the failure reason. Valid OTHER/GENERAL follows normal tiers. Empty universal pool is a controlled failure, never fabricated content.

Use a small injectable randomness boundary for deterministic first/last-index tests and `random_int` in production. No weighting/ranking, mechanism filters, DB-wide random sort, caching, or speculative framework.

Coordinate input validation, classification, selection, and verdict persistence. The external call must finish **before** the short DB transaction. Keep pool selection and snapshots consistent; successful response follows commit. DB write failure returns a controlled error with no false permalink. Catalog changes never cause saved results to rerun selection/classification.

### HTTP and saved-result reading

Implement the documented `POST /answer` contract as this task's initial backend contract. JSON object with only string `question`, trim, 1–500 Unicode code points, body ≤8 KiB; reject malformed JSON/UTF-8, NUL, arrays, unknown fields. Validate before Jev. Enforce same-origin checks and a configurable application rate limit with an explicitly configured trusted-proxy policy; do not invent production-limit guarantees or weaken them to complete tests.

Success: **201**, server-built ID/question/answer/url, `Location: /a/{publicId}`, `Cache-Control: no-store`, only after persistence. Stable controlled errors follow the documented 400/403/413/415/422/429/503 cases; 429 has Retry-After. A Jev failure with a usable universal pool still creates a persisted fallback result. No success if persistence/content fails. Avoid exposing trace/DSN/key/raw provider errors.

Implement `GET /a/{publicId}` (and HEAD) reading only stored snapshots; invalid/unknown ID→404. Provide minimal safely escaped SSR HTML, canonical URL and textual OG from trusted configuration, noindex/no public feed. Never call Jev/random when reading. Keep decision_context internal. No polished frontend, POST-fetch UI, dynamic OG image, About/FAQ build-out, or deployment in this task.

## Test-first execution and acceptance

Follow root hard principles. Before each behavior change, write a focused test that expresses the required behavior or reproduces failure, run it, and verify it fails for the relevant reason. Then implement the smallest passing change. Do not replace this with tests written only afterward or manufacture tests that repeat configuration/prose.

Required meaningful coverage: explicit eligibility pairs; enabled/locale filtering; all tiers/direct universal failure; valid OTHER vs failure; deduplication/uniform random boundary; empty catalog; import validation/idempotency/atomicity; real SQLite migrations/uniqueness/FKs; Unicode/UTC; snapshot preservation after edit/disable/delete and SET NULL; selected metadata vs classifier labels; persistence rollback/failed commit; POST statuses/headers/origin/rate/body/validation; zero provider calls for invalid input and permalink reads; secret-free production errors. Test PostgreSQL where actually available and record what ran.

Preserve all existing classifier/evaluation/skeleton tests. Run relevant focused checks during changes, then the full offline suite and manifest/platform/configuration checks. Verify fresh-database migrations and a real local SQLite-backed import→answer creation→stored-result read with a fake classifier, without live API calls. Report synthetic fixture usage explicitly.

Update README/setup commands, data/decisions, testing, technical-plan status, and verification with actual implementation/results and remaining gaps. Keep user-approved decisions distinct from unverified production/human-quality claims. Validate changed documentation/links/diagrams as needed. Do not repeat live history as a newly dated measurement.

Finish the authorized backend autonomously; do not stop at the first migration or a partial scaffold. Ask only about consequential unresolved product choices not covered here, while continuing independent work. Do not ask again about the four confirmed decisions. Return a concise Ukrainian report with the exact SQLite path, migrations/tables/models/endpoints created, actual test results, setup/reproduction commands, PostgreSQL/production/content gaps, and next frontend stage with its confirmed dark-theme requirement. Leave Git delivery to Serhii.
