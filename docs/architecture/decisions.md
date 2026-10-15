# Architecture decision register

2026-10-11. “Accepted” means an explicit user decision or a concrete limited bootstrap choice. “Proposal” needs assessment at the relevant stage and does not block research. Acceptance of a requirement is distinct from implementation.

| ID | Status | Decision and consequence |
| --- | --- | --- |
| ADR-001 | Accepted by user; implemented locally | Symfony, SSR, vanilla JS POST fetch; no SPA. Frontend implemented on 2026-10-15 |
| ADR-002 | Accepted for bootstrap; runtime requirement corrected by user | Symfony 7.4 LTS, Twig, Composer lock; explicit configuration without Flex/Runtime recipes. Bootstrap used a PHP 8.2 dependency baseline; development is PHP 8.4 with planned 8.5, minimum 8.4. Composer now requires >=8.4 and platform 8.4.0; the audit finding is resolved |
| ADR-003 | Accepted by user | Contextual Absurdity, Jev first; Chaos/NLP deferred |
| ADR-004 | Accepted by user | Jev classifies categories; PHP random within a category. The model neither generates nor ranks final phrases |
| ADR-005 | Accepted by user, clarified during audit | English UI/content/documentation; Ukrainian user communication |
| ADR-006 | Accepted by user, backend handoff confirmation | SQLite locally, PostgreSQL in production; **DBAL + Migrations without ORM**. Domain PHP models/repositories, not ORM entities. Verify both engines; implemented in the backend task; PostgreSQL server acceptance remains unverified |
| ADR-007 | Implemented under backend authorization | Immutable VERDICT snapshot, UUIDv4, GET without Jev/random |
| ADR-008 | Implemented under backend authorization | Eligibility as explicit intent/topic pairs, not two independent sets |
| ADR-009 | Stage-1 classification implemented | One Jev call; initial timeout 0.30 s / max_duration 0.40 s. After two live batches Serhii chose **0.60/0.80 s defaults**; no retry/redirect; model configurable/pinned. Adapter returns typed failure; direct universal application fallback is now implemented |
| ADR-010 | Implemented under backend authorization | Catalog through validated CLI import; no web admin or runtime template generation at launch |
| ADR-011 | Implemented locally | Public by URL, noindex, no feed; question storage/transmission notice before submit, with data disclosure on `/privacy`; launch policy remains open |
| ADR-012 | Accepted by user | Begin with minimal foundation, research, architecture, and test planning; do not implement the whole product at once |
| ADR-013 | Accepted by user | Unit/integration tests, including Jev; real API calls separate from offline suite |
| ADR-014 | Accepted by user | Before production, edit development migrations and rebuild disposable dev/test databases. Shared/persistent data requires new immutable incremental migrations; no reset/deployment performed here |
| ADR-015 | Accepted by user | Every table has its own UUID PK and `created_at`; eligibility retains separate unique `(answer_id, intent, topic)`. ANSWER.revision and VERDICT.answer_revision removed |
| ADR-016 | Direction accepted; provisional single-string implementation | `ANSWER_ELIGIBILITY.humor_mechanism` prepared during content authoring/generation, ignored by selector, retained for statistics. One controlled string per row proposed; multiple mechanisms, likes/votes, owner evaluation are future decisions |
| ADR-017 | Accepted by user and implemented | VERDICT: UUID id, question_snapshot, answer_snapshot, nullable answer_eligibility_id with **ON DELETE SET NULL**, decision_context JSON, UTC created_at; no revisions. Diagnostics: actual classifier provider, requested/resolved model, validated labels or failure; selected tier/reason/pool/count/algorithm/mechanism. Exclude raw responses/candidate lists and unused confidence |
| ADR-018 | Frozen stage-1 baseline | [Taxonomy/rubric 1.0.0](../content/taxonomy-v1.md): duration→WHEN, explicit selection→CHOICE, pay→MONEY, career/team organization→WORK, technical operation→TECH; multiple questions→first recoverable; Where→WHAT. Author labels without independent human validation |
| ADR-019 | Stage 1 implemented | Unknown enum/incomplete schema/invalid numbers→invalid_response; full probability distribution, sum tolerance 0.001, choice must be maximum (ties allowed). No confidence threshold; tolerance is local schema policy, not calibration |
| ADR-020 | User clarification, implemented single-key flow | One production JEV_API_KEY for application and authorized live evaluation, only `.env.local`/environment. No separate test key/sandbox workflow. Live requires --live and an explicit limit; no retries. Development/holdout separate; failures are not empty-catalog fallback |
| ADR-021 | User clarification during audit | Personal project/personal account; unrelated WHMCS/work reviewer defaults do not define the project role. Scope comes from explicit user requests. Real Jev costs should be assessed from authorized usage and billing evidence, not assumed from a low published price |

| ADR-022 | Accepted by user, backend implemented locally | Complete backend after Composer minimum/platform correction: catalog, migrations/import, selector, verdict persistence, POST /answer, saved-result reading. Local implementation/tests only; see [backend prompt](../handoff/backend-implementation-prompt.md) |
| ADR-023 | Accepted by user; implemented locally on 2026-10-15 | **Dark-theme frontend**, shared Twig shell/verdict, vanilla-JS form/fetch and copying, About/FAQ, data disclosure and styled HTML errors. No deployment or paid calls; final privacy policy remains a launch decision |

The provisional eight mechanism strings, full-catalog import semantics, strict Origin policy, and local fixed-window limit are documented implementation choices in the [backend guide](../backend.md). They do not establish human content or production acceptance.

## Open decisions and evidence gaps

1. **Taxonomy/content:** v2 is the sole active implementation. Independently review the 100 author annotations and preliminary seed, and run the separately authorized live trial. Historical baseline remains evidence only; humor quality is unaccepted.
2. **Jev access:** the production key works and pinned-model access is confirmed by two development live batches. Billing/balance, sustained limits, and account privacy terms are unverified. Do not search unrelated configuration for keys. Public documentation research found no separate sandbox; that does not rule out private options, and none is required for this project.
3. **Quality gate:** minimum intent/topic accuracy, fallback rate, and editorial humor criteria. Set criteria before evaluating against them; development results do not retroactively create a passed release gate.
4. **Brand/domain:** `we-know` is the repository; `Instant Answer` / `instant-answer.net` is the source concept. Domain ownership is unconfirmed.
5. **Production:** hosting, PostgreSQL version, region, concurrency, API budget, and backups. Database engine choice is settled.
6. **Data policy:** retention, result deletion, shared-page indexing, sensitive questions, and content moderation before public release.
7. **Decision context:** the short JSON and nullable FK with `ON DELETE SET NULL` are now accepted under ADR-017. SQLite persistence and serialization are tested; PostgreSQL acceptance remains outstanding; context explains selection, not exact random replay. Details/example: [data model](data.md).
8. **Humor mechanism:** approve controlled codes and one mechanism per eligibility. Statistics must not become hidden selector weights.
9. **PHP configuration:** Composer minimum/platform are corrected to PHP 8.4; verify the planned PHP 8.5 transition separately. See [audit findings](../verification.md#documentation-audit-findings).

## Deliberately deferred

Chaos driver, lexical/NLP classifier, weighted selection, embeddings/vector DB, generative templates, accounts, chat history, admin dashboard, likes/voting, owner-review analytics, A/B assignment, distributed cache, queues, dynamic OG images, worker runtime. Provider replaceability comes from an adapter boundary, not multiple unnecessary implementations.

## Historical work authorization

Bootstrap initially had an explicit local exception to the supplied reviewer profile; later requests authorized research, architecture, testing, subagents, and Git, including separate-agent foundation/documentation commit/push. Stage 1 and humor research had different scopes. These are historical permissions, not standing authorization or product deployment. [Verification](../verification.md) retains supplied baseline evidence; it was not rechecked through Git in this audit. Personal Codex/Claude configuration remains outside product work. Current project context is defined by the user's personal-project clarification and [AGENTS.md](../../AGENTS.md).

## Taxonomy and seed evolution — 2026-10-12–13

ADR-024 — **Accepted by user, implemented offline:** adopt 14 intents / 13 topics as taxonomy/rubric 2.0.0, superseding the research proposal's implementation deferral. Classify requested outcomes, preserve first-request/topic tie-break rules, keep ANY editorial-only and successful OTHER distinct from provider failure. No local semantic classifier.

The user explicitly clarified that the project is still in development without a frontend: evolve the active implementation directly, without parallel old-version support or migration machinery. Update the unreleased initial schema and synthetic fixtures; catalog integer version 2 binds the changed semantics with no extra fields. Existing development data remains untouched; the [isolated seed database](../content/seed-v2/README.md) uses a fresh file. Old eligibility is not mechanically converted.

The task authorized local code, tests, documentation, editorial artifacts and the isolated SQLite import. It excluded Git, deployment, production changes, external/paid calls, subagents and personal configuration. Twenty-four completed answers passed preliminary author screening; human approval and production PostgreSQL acceptance remain outstanding. The [100-question v2 trial](../evaluation/corpus-v2/README.md) is prepared but unexecuted.


## Development seed and database lifecycle — 2026-10-15

The user authorized substantial offline answer authoring, the complete canonical catalog, local database tooling/tests/documentation, and clearing `var/we-know.sqlite`. The completed batch retains all 24 existing answers and adds 1,031 author-screened drafts; human scores remain null. Counts, failed candidates and whole-pool limitations are in the [batch evidence](../content/seed-v2/batch-20261015/README.md).

The four Composer commands reset, migrate, seed, and fresh target only the default development SQLite database. Fresh runs reset → current migrations → canonical importer, stopping at the first failure. The initial schema was sufficient and was not changed. Existing demonstration databases and historical reports were preserved. This authorization did not include Git delivery, deployment, ranking, live services, personal configuration or Claude files; the accepted first-pool uniform selector remains unchanged.
