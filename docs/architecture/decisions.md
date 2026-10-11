# Architecture decision register

2026-10-11. “Accepted” means an explicit user decision or a concrete limited bootstrap choice. “Proposal” needs assessment at the relevant stage and does not block research. Acceptance of a requirement is distinct from implementation.

| ID | Status | Decision and consequence |
| --- | --- | --- |
| ADR-001 | Accepted by user | Symfony, SSR, vanilla JS POST fetch; no SPA. Fetch remains unimplemented |
| ADR-002 | Accepted for bootstrap; runtime requirement corrected by user | Symfony 7.4 LTS, Twig, Composer lock; explicit configuration without Flex/Runtime recipes. Bootstrap used a PHP 8.2 dependency baseline; development is PHP 8.4 with planned 8.5, minimum 8.4. Existing Composer 8.2 settings are an audit finding, pending correction |
| ADR-003 | Accepted by user | Contextual Absurdity, Jev first; Chaos/NLP deferred |
| ADR-004 | Accepted by user | Jev classifies categories; PHP random within a category. The model neither generates nor ranks final phrases |
| ADR-005 | Accepted by user, clarified during audit | English UI/content/documentation; Ukrainian user communication |
| ADR-006 | Database choices accepted; persistence tooling proposed | SQLite locally, PostgreSQL in production, definite user choices. DBAL + Migrations without ORM proposed; verify both platforms. Persistence is unimplemented |
| ADR-007 | Proposal, consistent with share URL requirement | Immutable VERDICT snapshot, UUIDv4, GET without Jev/random |
| ADR-008 | Proposal | Eligibility as explicit intent/topic pairs, not two independent sets |
| ADR-009 | Stage-1 classification implemented | One Jev call; initial timeout 0.30 s / max_duration 0.40 s. After two live batches Serhii chose **0.60/0.80 s defaults**; no retry/redirect; model configurable/pinned. Adapter returns typed failure; application fallback remains future work |
| ADR-010 | Proposal | Catalog through validated CLI import; no web admin or runtime template generation at launch |
| ADR-011 | Proposal | Public by URL, noindex, no feed; explain question storage/transmission before submit |
| ADR-012 | Accepted by user | Begin with minimal foundation, research, architecture, and test planning; do not implement the whole product at once |
| ADR-013 | Accepted by user | Unit/integration tests, including Jev; real API calls separate from offline suite |
| ADR-014 | Accepted by user | Before production, edit development migrations and rebuild disposable dev/test databases. Shared/persistent data requires new immutable incremental migrations; no reset/deployment performed here |
| ADR-015 | Accepted by user | Every table has its own UUID PK and `created_at`; eligibility retains separate unique `(answer_id, intent, topic)`. ANSWER.revision and VERDICT.answer_revision removed |
| ADR-016 | Direction accepted; representation proposed | `ANSWER_ELIGIBILITY.humor_mechanism` prepared during content authoring/generation, ignored by selector, retained for statistics. One controlled string per row proposed; multiple mechanisms, likes/votes, owner evaluation are future decisions |
| ADR-017 | Proposal, not approved | VERDICT: UUID id, question_snapshot, answer_snapshot, nullable answer_eligibility_id, decision_context JSON, created_at. JSON captures classification, actual selected pool/mechanism, and fallback reason at event time; previous separate diagnostic columns are no longer accepted |
| ADR-018 | Frozen stage-1 baseline | [Taxonomy/rubric 1.0.0](../content/taxonomy-v1.md): duration→WHEN, explicit selection→CHOICE, pay→MONEY, career/team organization→WORK, technical operation→TECH; multiple questions→first recoverable; Where→WHAT. Author labels without independent human validation |
| ADR-019 | Stage 1 implemented | Unknown enum/incomplete schema/invalid numbers→invalid_response; full probability distribution, sum tolerance 0.001, choice must be maximum (ties allowed). No confidence threshold; tolerance is local schema policy, not calibration |
| ADR-020 | User clarification, implemented single-key flow | One production JEV_API_KEY for application and authorized live evaluation, only `.env.local`/environment. No separate test key/sandbox workflow. Live requires --live and an explicit limit; no retries. Development/holdout separate; failures are not empty-catalog fallback |
| ADR-021 | User clarification during audit | Personal project/personal account; unrelated WHMCS/work reviewer defaults do not define the project role. Scope comes from explicit user requests. Real Jev costs should be assessed from authorized usage and billing evidence, not assumed from a low published price |

## Open decisions and evidence gaps

1. **Taxonomy/content:** baseline v1 is frozen; independently review labels, holdout quality, and content-pool fit. Taxonomy explains v1 refinements; humor codes remain editorial proposals.
2. **Jev access:** the production key works and pinned-model access is confirmed by two development live batches. Billing/balance, sustained limits, and account privacy terms are unverified. Do not search unrelated configuration for keys. Public documentation research found no separate sandbox; that does not rule out private options, and none is required for this project.
3. **Quality gate:** minimum intent/topic accuracy, fallback rate, and editorial humor criteria. Set criteria before evaluating against them; development results do not retroactively create a passed release gate.
4. **Brand/domain:** `we-know` is the repository; `Instant Answer` / `instant-answer.net` is the source concept. Domain ownership is unconfirmed.
5. **Production:** hosting, PostgreSQL version, region, concurrency, API budget, and backups. Database engine choice is settled.
6. **Data policy:** retention, result deletion, shared-page indexing, sensitive questions, and content moderation before public release.
7. **Decision context:** agree the proposed short JSON and nullable eligibility FK with `ON DELETE SET NULL`; this explains selection rather than reproducing random selection exactly. Details/example: [data model](data.md).
8. **Humor mechanism:** approve controlled codes and one mechanism per eligibility. Statistics must not become hidden selector weights.
9. **PHP configuration:** correct the erroneous Composer minimum/platform to PHP 8.4 under separate implementation authorization; verify the planned PHP 8.5 transition separately. See [audit findings](../verification.md#documentation-audit-findings).

## Deliberately deferred

Chaos driver, lexical/NLP classifier, weighted selection, embeddings/vector DB, generative templates, accounts, chat history, admin dashboard, likes/voting, owner-review analytics, A/B assignment, distributed cache, queues, dynamic OG images, worker runtime. Provider replaceability comes from an adapter boundary, not multiple unnecessary implementations.

## Historical work authorization

Bootstrap initially had an explicit local exception to the supplied reviewer profile; later requests authorized research, architecture, testing, subagents, and Git, including separate-agent foundation/documentation commit/push. Stage 1 and humor research had different scopes. These are historical permissions, not standing authorization or product deployment. [Verification](../verification.md) retains supplied baseline evidence; it was not rechecked through Git in this audit. Personal Codex/Claude configuration remains outside product work. Current project context is defined by the user's personal-project clarification and [AGENTS.md](../../AGENTS.md).
