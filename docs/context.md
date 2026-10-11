# Context and requirements provenance

## Sources

- [Original conversation “Comparison of similar websites”](https://chatgpt.com/c/6aca8ea5-04a0-83eb-8d1d-f80b57db4601), read through an available tab on 2026-10-11: initial description, comparable products, Jev, classification refinements, mode choice, NLP, humor mechanisms, speed, and post-irony.
- [Share link from Serhii](https://chatgpt.com/share/6acb479e-9110-83eb-8773-c0a199dd4297), supplied after the private session was read; retrieved on 2026-10-11 over public HTTP without a browser. The snapshot ends with humor frameworks, speed, and post-irony; primary-source verification was still required at that point.
- Project bootstrap session on 2026-10-11: Symfony, no SPA, vanilla JS POST fetch on the home page, initially likely SQLite locally / PostgreSQL in production, minimal bootstrap rather than full implementation, English product, Git initially reserved for the owner.
- Later refinements in that session: the user authorized Git and subagents, assigned a separate agent commit/push and documentation work, and specified UUID PK/created_at, humor mechanism on eligibility, removal of revision fields, and development migration rules. Product publication was not requested.
- Documentation-audit clarifications on 2026-10-11: this is a **personal project and personal account**, not a WHMCS/work-account review task. Development uses locally installed **PHP 8.4**, with a planned move to **8.5**; the intended minimum is **8.4**. **SQLite locally / PostgreSQL in production** is definite. Jev uses the **production key**, with no separate test key or sandbox workflow; real costs are to be assessed through authorized live usage. The existing Composer 8.2 requirements/platform are an error to fix under a separate prompt; see [audit findings](verification.md#documentation-audit-findings).

This is a substantive context transfer, not a verbatim export. Earlier ChatGPT claims about comparable products, papers, prices, and speed are not independently confirmed facts. Historical permissions do not authorize current work.

## Product

The working public name is **Instant Answer**, the mentioned domain is `instant-answer.net`, and the local/repository name is **we-know**. Domain ownership and final branding have not been checked.

Slogan: **We don't think. We know.** The user's philosophy: “the highest-quality implementation of the most pointless product.”

This is post-irony about AI certainty: a restrained, serious interface gives a short, categorical, absurd answer. The initial analogy was Magic 8-Ball. Speed and sharing a specific question/answer pair through a URL matter. `0 questions understood` and `100% confidence` are satirical copy, not technical telemetry.

## Evolution and final decisions

| Step | Content | Current status |
| --- | --- | --- |
| Initial idea | Prepared random answers, without real AI | Refined: AI may classify, but may not generate the text |
| First Jev discussion | Pick the best answer among prepared candidates | Rejected by the user's subsequent clarification |
| User clarification | Categorize questions and answers; simple random within a category | Accepted core mechanism |
| Both kinds of absurdity | Chaos and Contextual as possible strategies | User subsequently chose **Contextual Absurdity only at launch** |
| NLP / regex instead of AI | Assistant proposed a local MVP classifier | User chose **Jev first**, NLP later |
| Configuration | Ability to choose the answer-search model/service | Provide a replaceable adapter boundary; do not implement multiple drivers now |
| Normalization and selection | User deferred detailed design until planning | A proposal is documented, with no migrations yet |
| Humor mechanisms | Metaphor, exaggeration, oxymoron, templates | Accepted: store `humor_mechanism` on eligibility during content preparation/generation; selector ignores it. An automatic generator is not approved |
| Framework | Pure PHP, Laravel, Symfony were considered | User chose **Symfony** |
| Language | Concepts were mostly English | User explicitly confirmed **English** for the first version; documentation is now English and communication Ukrainian |
| Identifiers and time | UUID PK and created_at | Accepted for every table, including eligibility; answer/intent/topic remains unique |
| Content versions | ANSWER.revision and VERDICT.answer_revision | Removed by user decision; answer text snapshot remains |
| Selection explanation | Separate diagnostic VERDICT fields questioned | **Proposal:** nullable eligibility FK + short historical decision_context JSON; final structure is not approved |
| Pre-launch migrations | Edit development migrations and rebuild disposable local/test databases | Accepted; shared/persistent data requires immutable incremental migrations. No reset/deployment is being performed |
| Runtime/database clarification | Personal development environment | PHP 8.4 → planned 8.5; minimum 8.4; SQLite local / PostgreSQL production accepted, not alternative candidates |

## Contextual Absurdity mechanics

Jev determines the expected answer form (`intent`) and topic (`topic`), in one request. In the target application PHP selects a random prepared phrase from an eligible pool. The model does not see the full library and does not choose the funniest phrase. The selector remains unimplemented.

The initial taxonomy proposal became the stage-1 [baseline v1](content/taxonomy-v1.md), including ambiguity rules. Independent human label review remains outstanding:

- Intent: `YES_NO`, `WHEN`, `WHY`, `HOW`, `WHO`, `WHAT`, `HOW_MANY`, `CHOICE`, `OTHER`.
- Topic: `TECH`, `WORK`, `MONEY`, `LOVE`, `HEALTH`, `LIFE`, `GENERAL`.
- `ANY` is editorial eligibility for a universal answer, not a classifier response.

Target fallback: exact `intent + topic` → `intent + GENERAL` → `ANY + GENERAL`. A temporal question should receive an absurd date/temporal response rather than a random object. This distinguishes contextual absurdity from chaos. If classification is unavailable, the future policy goes directly to universal fallback: service degradation, not hidden Chaos activation.

A saved permalink must not rerun random selection or Jev. Asking a new question again may yield another result; opening an existing URL must not.

## Tone and UX: proposals supporting the concept

One request and one result, without chat history, simulated typing, or artificial delays. Serious typography, minimal decorative noise. The result could look like a final numbered decision. Open Graph previews should support sharing.

The source session proposed `The world's first uncertainty-free answer engine.`, pseudo-bureaucratic statuses, “certificates,” and an absurd changelog. These are creative material, not mandatory screens. About/FAQ should explain satirical entertainment. Technical documentation and real errors stay truthful.

## Comparable products mentioned in the session

These are research leads from a previous ChatGPT response; their properties/current state were not rechecked for this transfer.

| Reference from the session | What was discussed |
| --- | --- |
| [Ask Me Nothing](https://dev.to/rajkumarpp/ask-me-nothing-the-most-useless-ai-youll-ever-use-15ip) | Random absurd answers and chatbot parody |
| [ChadGPT](https://meme.app/chat/chadgpt) | An overconfident parody character |
| [Yes or No Oracle](https://pageonaut.com/tools/yes-no-oracle) | Magic 8-Ball and sharing results |
| [ChatTJB](https://chattjb.org/) | AI satire with human-written answers |

Product hypothesis: differentiate through presentation quality, fitting absurdity, and immutable verdicts rather than randomness itself. The main risk is rapid loss of interest after a few repetitive answers.

## Humor: content preparation and initial research questions

This subsection preserves the initial discussion. The separate 2026-10-11 source investigation is described next.

The session mentioned GTVH (Raskin/Attardo), JAPE, Reverse-Engineering Satire, and Benign Violation Theory. At that point these were directions for primary-source research, not ready Composer libraries or a verified scientific basis for the plan.

The assistant's working author heuristic: expected form + logical violation + deadpan delivery. It is not a mathematical formula for humor quality.

Candidates for `ANSWER_ELIGIBILITY.humor_mechanism`: metaphor, hyperbole, oxymoron, paradox, false causality, false authority, reversal, bureaucratic absurdity. Do not confuse mechanisms with question categories. The field is prepared during answer authoring/generation; target random selection ignores it. One controlled string mechanism per eligibility is proposed, preserving unique `(answer_id, intent, topic)`. Multiple mechanisms are a future decision to avoid multiplying candidate rows and biasing random selection.

The field is retained for possible statistics; likes, votes, or owner evaluation may come later, but no corresponding tables/routes are accepted implementation. Start with prepared phrases; investigate controlled templates such as `On {day}, after {authority} approves {event}.` separately.

## Separate humor research — 2026-10-11

Provenance: a new direct request from Serhii after repository connection/push permitted local we-know research and documentation as an exception to the reviewer-only default then supplied. That session used no subagents/browser, business/database changes, personal configuration, Claude writes, or commit/push. Its scope was separate from earlier Git delivery. The later personal-project clarification is recorded above.

The orchestrator-provided `/private/tmp/we-know-humor-source.md` was read in full: extracts of visible user/assistant final messages 77, 96, 98, and 116 from the [public share](https://chatgpt.com/share/6acb479e-9110-83eb-8773-c0a199dd4297), retrieved by the orchestrator over HTTP on 2026-10-11. Raw transcript/HTML was not copied into the repository. This establishes concept provenance, not scientific evidence.

Outputs: [evidence-based mechanism research](research/humor-mechanisms.md) and a [self-contained Codex guide](content/answer-generation-guide.md). Primary PDFs for JAPE, Reverse-Engineering Satire (AAAI 2019), McGraw/Warren 2010, and an author's GTVH account were checked. GTVH access was limited to the 1991 bibliographic record and the abstract of an author's 2020 chapter; full publisher texts were unavailable. Links, access scope, and transfer limits are recorded in the research.

**Editorial proposals, not architectural decisions:** five primary codes `bureaucratic_absurdity`, `false_causality`, `reversal`, `metaphor`, `hyperbole`; auxiliary `false_authority`; `oxymoron`/`paradox` reserved. Code spellings preserve earlier candidates. The guide offers controlled slots only for offline completed phrases, screening workflow, editorial rubric, draft JSON, and a smoke corpus covering all nine intents. Examples were authored for that task; no human humor evaluation occurred. Intent/topic was a proposal during that research and subsequently became the implemented [taxonomy v1](content/taxonomy-v1.md); editorial eligibility and mechanism codes remain proposed.

Accepted boundaries were preserved: Jev classifies, PHP selects uniformly from the first eligible pool, and the mechanism is prepared on `ANSWER_ELIGIBILITY` and ignored by the selector. One primary mechanism per row is proposed; overlap does not create duplicate candidates. Draft format is not a schema/import contract; UUID PK/created_at, no revision fields, and migration rules were unchanged. Runtime templates, Chaos/NLP, likes/votes, and owner-review analytics remain deferred.

Research conclusion: cross-check questions against every phrase in a pool, since a reply may fit just one question. WHAT/CHOICE boundaries, HOW_MANY units, and thematic fallback fit remain content questions. Humor, portability to unseen questions, and repetition fatigue require evaluation. Future likes need exposure/selection-bias analysis and do not justify altering random selection now.

## Speed and Jev: initial state

The earlier session quoted 140 ms P50, prices, and specific model versions. Those figures are not transferred as guarantees or a current budget. The project needs measurements from its production region and tariff verification before relying on them.

Initial proposed targets: backend without external classification ≤20 ms, POST P50 ≤300 ms / P95 ≤700 ms, permalink server response ≤100 ms. These are measurement hypotheses, not achieved SLAs.

The [official TypeSafe documentation](https://docs.typesafe.ai/introduction/quickstart), checked on 2026-10-11 during bootstrap research, describes keyed HTTP with `state`, `model`, and multiple `questions` in one call, supporting an intent/topic adapter. **At bootstrap only**, account/key/free-tier access and question quality had not been checked, and no paid calls had occurred. Stage 1 below supersedes that access state; billing remains unverified.

## Scoped stage 1 — 2026-10-11

A new direct request authorized implementation, Composer, local documentation/tests for taxonomy/content and a Jev spike, without Git/deployment/later stages. After offline checks Serhii configured the project JEV_API_KEY and explicitly authorized up to 54 development live calls; a separate test credential was rejected as unnecessary for one small account. The key was moved from versioned `.env` to already ignored `.env.local` without display/logging.

Implemented: typed classification boundary, Symfony HttpClient/DI, strict normalizer, versioned corpus (54 development/27 holdout), CLI evaluation, and deterministic offline tests. Live: 54 calls at 0.30/0.40 s yielded 41 successes and 13 timeouts; a newly authorized 54 calls at 0.60/0.80 s yielded 54 successes and no failures. All valid responses resolved to `jev-1.13.0`. Details and semantic mismatches: [report](evaluation/stage-1-results.md). This supersedes “no live calls” but is not a production SLA or independently verified quality. Holdout was not called; the rubric was not changed after development results.

Both separate humor research documents were read and preserved during stage 1; new [form illustrations](content/stage-1-answer-examples.md) were written separately with hold status. Database/selector/import/VERDICT/POST/permalink remain unimplemented; decision_context was not approved. Claude material and personal configuration/backups were unchanged.

After comparing both live batches Serhii chose 600/800 ms as current timeout/max_duration defaults. The original 300/400 ms and report remain historical comparison evidence; rubric/labels were unchanged. The later audit translates documentation without rerunning live measurements or regenerating research examples.
