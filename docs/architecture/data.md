# Data: implemented backend model

Doctrine DBAL + Migrations **without ORM** now implement this model. SQLite is local; PostgreSQL is the definite production engine, with explicit platform DDL but no available server acceptance run. See [backend setup/import contract](../backend.md) and the [verification event](../verification.md#backend-implementation-verification-event).

The UUID/UTC, eligibility metadata, simplified VERDICT, ON DELETE SET NULL and diagnostic requirements were explicitly accepted by Serhii. Import mechanism codes are a provisional implementation choice, not approved editorial quality.

## Model

```mermaid
erDiagram
    ANSWER ||--o{ ANSWER_ELIGIBILITY : "has allowed pairs"
    ANSWER_ELIGIBILITY o|--o{ VERDICT : "selected eligibility, optional"
    ANSWER {
        uuid id PK
        text text
        string locale
        boolean enabled
        datetime created_at
    }
    ANSWER_ELIGIBILITY {
        uuid id PK
        uuid answer_id FK
        string intent
        string topic
        string humor_mechanism
        datetime created_at
    }
    VERDICT {
        uuid id PK
        text question_snapshot
        text answer_snapshot
        uuid answer_eligibility_id FK
        json decision_context
        datetime created_at
    }
```

Physical tables are lowercase `answer`, `answer_eligibility`, and `verdict`; Doctrine also creates `doctrine_migration_versions`. UUIDs are VARCHAR(36) with explicit nonnull primary keys, UTC times are fixed-format VARCHAR(20), boolean storage branches by platform, and diagnostic JSON is SQLite TEXT with `json_valid` / PostgreSQL JSONB. **Every table has its own UUID primary key and UTC `created_at`.** `ANSWER_ELIGIBILITY` also has unique `(answer_id, intent, topic)` and index `(intent, topic, answer_id)`; these are not a composite PK. Enabled/locale checks join ANSWER. This is sufficient for a small catalog; add other indexes based on query plans. `ANSWER.revision` and `VERDICT.answer_revision` were removed by user decision.

## Why pairs are stored

Independent intent and topic sets would create an unwanted Cartesian product. An answer allowed for `(EXPLAIN, WORK_STUDY)` and `(WHEN, TECH)` must not automatically be eligible for `(EXPLAIN, TECH)`. Each eligibility row is an explicit author-assigned pair; enabled seed records do not imply human approval. Classification categories are controlled string-backed enums with taxonomy versions; a separate CRUD dictionary is unnecessary before administration.

`ANY` means universal answer form; `OTHER` means a successful classification with no recoverable requested outcome. They differ. `GENERAL` is the general topic. Do not use comma-separated categories or JSON as the primary selection index.

`humor_mechanism` is populated during catalog preparation/generation. It describes phrase eligibility for a particular pair, not a question category returned by Jev. The selector ignores it: no pool filtering or weighting. It is retained for possible statistics; likes, votes, or owner evaluation remain possibilities, with no corresponding tables/routes/implementation.

Implementation choice: one controlled string per eligibility, validated against the provisional allowlist. Initial candidates: `metaphor`, `hyperbole`, `oxymoron`, `paradox`, `false_causality`, `false_authority`, `reversal`, `bureaucratic_absurdity`; editorial approval of these codes remains outstanding. Do not add the mechanism to uniqueness: multiple rows for the same `(answer_id, intent, topic)` risk biasing random selection. Multiple mechanisms for one eligibility are a separate future decision. Do not add runtime templates or weights now.

## Immutable verdict

`VERDICT.id` is an unpredictable UUIDv4 primary key. The public URL contains no question. Store final text rather than just a FK: editing/disabling ANSWER must not change an issued permalink. Accepted nullable `answer_eligibility_id` points to the actual selected row, through which the original ANSWER is available. Accepted FK policy: `ON DELETE SET NULL`; deleting eligibility must not delete the verdict or snapshots. Fallback alone does not make the FK null: selection from `(ANY, GENERAL)` points to that row.

### Accepted short `decision_context`

The user questioned separate VERDICT columns for `intent`, `topic`, `classification_status`, `fallback_reason`, `model_version`, `taxonomy_version`, `rubric_version`, `selection_version`, and `locale`. They are no longer an accepted column set. The requirement is to explain why an answer was chosen and what participated in selection. One small event JSON snapshot plus an eligibility FK has now been accepted.

The FK shows the current catalog, not the historical reason: categories/mechanisms can be edited and rows deleted. JSON deliberately records selection-time facts. Classifier categories and the actual selected pool may differ due to fallback. Synthetic example of successful classification with an empty exact pool:

```json
{
  "classifier": {
    "provider": "typesafe",
    "requested_model": "jev-1.13.0",
    "resolved_model": "jev-1.13.0",
    "intent": "WHEN",
    "topic": "TECH"
  },
  "selection": {
    "tier": "intent_general",
    "reason": "exact_pool_empty",
    "pool": {"intent": "WHEN", "topic": "GENERAL"},
    "candidate_count": 8,
    "algorithm": "uniform_random",
    "humor_mechanism": "false_authority"
  }
}
```

Accepted diagnostic minimum (confirmed after the audit):

- `classifier`: provider only when actually called; requested/resolved model, validated intent/topic if received. A resolved model exists only after a valid response; requested model must never masquerade as a resolved one. On failure record normalized `failure_reason` (e.g. `timeout`), without fabricated successful labels. If a call is skipped, record the skip reason rather than inventing a provider/model call.
- `selection`: actual tier (`exact`, `intent_general`, `universal`), pool intent/topic snapshot, unique candidate count before random selection, `algorithm: uniform_random`. Include `reason` only to explain fallback, e.g. `exact_pool_empty`, `intent_pools_empty`, `classification_failed`.
- Selection `humor_mechanism`: a snapshot of the selected eligibility's mechanism for future statistics. It did not participate in selection. Renaming the catalog mechanism does not rewrite the event.

A Jev failure sends the selector directly to `(ANY, GENERAL)`; an internal application fallback context `OTHER/GENERAL`, if used, is not recorded as Jev's response. Successful `OTHER/GENERAL` is a genuine result and follows normal tiers. Selection belongs to the application/domain; the adapter remains classification-only. Configuration failure records `skipped_reason: configuration`, `failure_reason: configuration_error`, and requested model without inventing a provider call. Other failures record provider/requested model/failure_reason and omit resolved model and labels.

This is a diagnostic explanation, not exact random replay. Do not store the full candidate list, raw provider response, secrets, IP, or invented revision fields. Add confidence only if it affects a decision; full rubric/taxonomy/request versions are not mandatory MVP verdict fields and may remain in evaluation reports. A separate VERDICT locale is unnecessary for an English snapshot. JSON does not replace normalized eligibility queries and is not public API output by default. Use consistent documented serialization names for this accepted structure and verify them in persistence tests.

A transaction starts after external classification: eligible-pool reading, eligibility selection, and text/diagnostic snapshot insertion must be consistent within one flow. Catalog import is atomic. HTTP 201 follows commit. Write failure → 503 without a broken URL.

Immutability does not mean indefinite retention. Retention, requested deletion, and indexing policy need decisions before public release. Deleted IDs must not be reused.

## SQLite and PostgreSQL

Doctrine DBAL + Migrations **without ORM** is accepted by the user. [DBAL supports SQLite/PostgreSQL platforms](https://www.doctrine-project.org/projects/doctrine-dbal/en/current/reference/platforms.html), but does not make types, locking, or DDL identical.

- Store UUIDs as portable strings and time as UTC with consistent precision; avoid native enum/JSONB/array dependence in base queries.
- Enable SQLite foreign keys for each connection. Prove enforcement with a test, not configuration alone.
- Keep SQLite under `var/`, outside `public/`, without a network filesystem. Consider busy timeout/WAL for development after checking test modes. SQLite is not evidence of production concurrency.
- Run migrations on empty SQLite and real PostgreSQL; branch SQL by platform where necessary. Do not automatically transfer SQLite-generated SQL to PostgreSQL.
- For production verify query plans, Unicode/length constraints, uniqueness/FKs, transaction errors, and concurrent inserts.
- The local development database need not migrate to production as a file. Transfer the catalog through controlled idempotent import with stable IDs; verdict transfer, if needed, requires a separate verified procedure.

### Migration lifecycle

Accepted by user: **before production**, while data is local/test and disposable, development migrations may be edited in place and databases rebuilt. Every refinement to an unreleased schema need not create another migration. The unreleased initial migration is `Version20261011000100`; this backend session created and migrated the local database while preserving its generated synthetic rows during schema finalization.

Once shared/persistent data needs preservation, including production, applied migrations become immutable: every change is a new incremental migration with upgrade/data-preservation checks. This documentation does not authorize deployment.

## Editorial import

Implemented: versioned content file and atomic CLI import, no web admin. Exact full-catalog replacement semantics and fixture boundaries are in the [backend guide](../backend.md#catalog-format-v2). Validate UUIDs and stable answer/eligibility IDs, `created_at` (reimport does not rewrite creation time), nonempty text, locale `en`, allowed intent/topic and `humor_mechanism`, no duplicate triples, and at least one active `(ANY, GENERAL)` answer. One invalid entry must not leave a partially updated catalog.

The MVP should cover each supported intent with several different GENERAL answers. Quantity, editorial quality, and repetition are content-stage criteria, not a claim that a library exists.

## Active v2 development schema

The initial unreleased migration now constrains 14 classifier intents plus editorial ANY, and 13 topics, from [taxonomy v2](../content/taxonomy-v2.md). Catalog format 2 binds those semantics; it adds no row fields or tables. The existing development file was preserved; only fresh isolated SQLite databases were migrated for this task. Historical verdict snapshots remain unchanged. [Seed verification](../content/seed-v2/database-verification.json) is SQLite evidence only.
