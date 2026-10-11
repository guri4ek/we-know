# Project documentation

As of 2026-10-11. User communication is Ukrainian; documentation, product UI, and the initial answer library are English. This is a personal project and personal account; project-wide instructions start in [AGENTS.md](../AGENTS.md).

| Document | Purpose |
| --- | --- |
| [Context](context.md) | Transferred decisions, concept evolution, comparable products, deferred ideas, and sources |
| [Technical plan](technical-plan.md) | MVP scope, proposed HTTP contract, stages, and exit criteria |
| [C4](architecture/c4.md) | Target context, containers, components, and request sequence |
| [Data](architecture/data.md) | Proposed normalization and immutable results; accepted SQLite/PostgreSQL choices |
| [Decisions](architecture/decisions.md) | Accepted decisions, proposals, and open questions |
| [Jev research](research/jev.md) | Historically checked API contract, models, limits, confidence, and uncertainties |
| [Humor mechanisms](research/humor-mechanisms.md) | Primary sources, evidence limits, practical mechanisms, and open hypotheses |
| [Answer authoring guide](content/answer-generation-guide.md) | Offline workflow for Codex, proposed codes, editorial rubric, smoke corpus, and draft JSON |
| [Taxonomy v1](content/taxonomy-v1.md) | Active intent/topic, ambiguity rules, ANY boundaries, and rubric version |
| [Stage-1 examples](content/stage-1-answer-examples.md) | Separate hold drafts illustrating answer forms and negative probes |
| [Jev evaluation](evaluation/README.md) | Development/holdout corpus, offline/live runner, and metric denominators |
| [Stage-1 results](evaluation/stage-1-results.md) | Historical development live baseline and outstanding checks |
| [Testing strategy](testing.md) | Existing suites and planned database, HTTP/JS, quality, and performance tests |
| [Verification](verification.md) | Actual implementation and distinct historical/current verification events |
| [Fixture provenance](../tests/Fixtures/Jev/README.md) | Synthetic offline responses and deny-network test behavior |
| [Audit resume handoff](handoff/documentation-audit-resume.md) | Historical pause snapshot, superseded by the completed verification event |
| [Documentation-audit handoff](handoff/documentation-audit-prompt.md) | Historical task scope and previous-chat evidence; later user clarifications take precedence |

## Continuing work

**Read [answer-generation-guide.md](content/answer-generation-guide.md) in full before creating or editing answer texts.** Generation there means offline library authoring with human editing. Examples are not a ready seed, and mechanism metadata does not affect the selector.

Implemented scope: the skeleton, taxonomy/corpus, Jev adapter, and stage-1 CLI runner. Two development live batches are recorded separately. Documentation does not authorize subsequent implementation. Distinguish classification components from the future selector/database/HTTP flow.

Distinguish **user decisions**, **architectural proposals**, and **verified implementation facts**. C4 depicts the target MVP, not completed code. Update document status alongside implementation changes. Development is PHP 8.4 with a planned move to 8.5; Composer's current 8.2 minimum/platform is an error tracked in the [audit findings](verification.md#documentation-audit-findings) for separate correction. SQLite locally and PostgreSQL in production are accepted choices, while persistence is unimplemented.

Accepted schema requirements: UUID PK/created_at in every table; eligibility has its own UUID, unique answer/intent/topic, and selector-ignored `humor_mechanism`; revision fields have been removed. A simplified VERDICT with nullable eligibility FK and `decision_context` remains a **proposal**. Development migrations may be edited and disposable databases rebuilt before production; shared/persistent data requires immutable incremental migrations. Details: [data](architecture/data.md) and [ADR-014–017](architecture/decisions.md).

Repository: https://github.com/guri4ek/we-know. Bootstrap delivery, the separate humor research, and stage-1 implementation had different explicit permissions. Their history is preserved in [context](context.md) and [verification](verification.md); it is not standing authorization. The documentation audit changes local documentation and project instructions only. Personal Codex/Claude configuration and backups are outside this work.
