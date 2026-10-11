# New chat task: documentation audit, English documentation, and project AGENTS.md

> Historical handoff, read and executed by the documentation-audit chat. Later user clarifications take precedence: this is a personal project/personal account; PHP 8.4 development and minimum, planned PHP 8.5; SQLite local and PostgreSQL production are definite; Jev uses the production key with no separate test key/sandbox workflow. The existing Composer 8.2 baseline is an error reserved for a separate correction prompt. The historical state and permissions below are retained as provenance, not current defaults or standing authorization. See the [audit findings](../verification.md#documentation-audit-findings), [verification event](../verification.md#documentation-audit-verification-event), and root [AGENTS.md](../../AGENTS.md).

## User request and authorization

Work in `/Users/serhii/OwnProjects/we-know`.

Serhii explicitly requested a new, separate Codex chat to audit this project's documentation, reconcile its implementation and testing status, translate the documentation into English, and create a root `AGENTS.md` as the project-wide entry point. This file transfers that request and the previous chat's context; it is not evidence that a new chat has already been created.

Communicate with Serhii in **Ukrainian**. Write project documentation and `AGENTS.md` in **English**.

This task authorizes scoped local documentation and project-instruction maintenance, plus relevant offline validation, as an exception to the personal reviewer-only default. It does not authorize product implementation, new Composer dependencies, Git delivery, deployment, live API calls, or later implementation stages. Finish the available documentation work without stopping for routine confirmations.

Read the applicable personal and project instructions, including `/Users/serhii/.codex/RTK.md`. Run every shell command through `rtk`, normally `rtk proxy ...`. Do not execute Git commands. Do not create/switch branches or worktrees, commit, stage, push, or manage PRs. Do not control the browser. Do not delegate this task.

Claude-specific files and backups are strictly read-only: do not create, translate, edit, rename, delete, or indirectly modify `CLAUDE.md`, `CLAUDE.local.md`, `.claude/**`, `/Users/serhii/.claude/**`, or any Claude backup. Do not change personal Codex configuration, skills, or backups. Do not inspect or disclose secrets; `.env.local` contains a working project credential and is not documentation to translate.

## Required outcome

1. Audit the actual repository files and documentation. Identify stale or contradictory statements, missing context, broken navigation, and unsupported completion claims. Treat the previous chat's results below as historical evidence to verify, not as a substitute for inspecting the current state.
2. Reconcile the current status: implemented, tested, measured live, proposed, deferred, not implemented, and unverified must be clearly distinguishable. Keep historical measurements separate from present behavior and current defaults.
3. Explain testing accurately: which suites and scenarios exist, which checks were actually run, their results and limitations, which tests are only planned, and which behavior remains unverified. Describe mock evidence, live evidence, and human/editorial evaluation separately. Record checks performed by this chat as a new verification event rather than replacing historical measurements.
4. Translate project documentation into English, including root `README.md`, Markdown under `docs/`, and documentation such as `tests/Fixtures/Jev/README.md`. Preserve the meaning, decisions, evidence qualifications, citations, identifiers, and relevant history. Repair links and heading anchors affected by translation, including any Mermaid labels that need translation while preserving diagram structure. Do not translate immutable corpus questions/labels, fixture data, recorded numeric results, API enums, executable commands, or source code merely for language consistency. Do not alter the frozen JSON corpora, rubric implementation, or recorded live reports as part of translation.
5. Create a single root **`AGENTS.md`**, using the conventional uppercase filename. It must contain firm, enduring rules at the highest level of abstraction and provide concise navigation into the project documentation. It must not become a technical runbook, implementation plan, stage checklist, command catalogue, historical narrative, test-results dump, or duplicated architecture document. Keep volatile technical details in the appropriate documents and link to them.
6. Validate the edited documents, navigation, code blocks, and any changed diagram syntax. Use existing offline tests when needed to verify current documented behavior; do not invent tests that only mirror prose or implementation. If a check cannot be performed, state the gap instead of claiming it passed.
7. Deliver a concise Ukrainian report: changes made, contradictions resolved, validation actually performed, results, and remaining uncertainty. Provide absolute clickable links to the root `AGENTS.md` and the principal updated documentation.

Existing work from other chats may be uncommitted. Preserve it. The humor research chat has finished; translation of its documents is now part of this explicit request, but replacing its research conclusions, regenerating its examples, or silently changing their status is not authorized. Inspect current content and make surgical edits rather than resetting files to an older snapshot.

## Mandatory four principles for AGENTS.md

Include these four principles in English as hard project-wide rules. They are the principles Serhii supplied under the name of the Karpathy file; do not invent a missing source path or claim to have verified a verbatim quotation.

1. **Think before code.** Analyze the task and the evidence before acting. Make assumptions explicit, resolve consequential ambiguity, and understand the intended outcome rather than blindly executing instructions.
2. **Simplicity first.** Choose the smallest sufficient solution. Avoid speculative abstraction, unnecessary complexity, features, dependencies, or code outside the authorized need.
3. **Surgical edits.** Change only what the task requires. Preserve adjacent behavior, unrelated work, and existing conventions; do not turn a focused change into a broad cleanup.
4. **Goal-oriented execution — test first.** Before implementing a behavior change or fixing a bug, first write a focused test that reproduces the problem or expresses the desired behavior, and verify that it fails for the relevant reason. Implement the smallest change that makes it pass, then verify the outcome. Do not dilute this rule into merely running tests afterward. For documentation-only work, establish verifiable acceptance evidence and validate the artifacts without manufacturing a product-code change just to satisfy the test-first rule.

Also capture durable high-level boundaries: Ukrainian user communication / English documentation; explicit authorized scope; evidence-based claims; preservation of other work; protection of secrets and read-only Claude material. Do not bake this chat's transient task or historic permissions into permanent project authorization.

## Documents to inspect

Read the current versions, starting from the documentation map:

- `README.md` and `docs/README.md`.
- `docs/context.md` and `docs/technical-plan.md`.
- `docs/architecture/c4.md`, `docs/architecture/data.md`, and `docs/architecture/decisions.md`.
- `docs/research/jev.md` and `docs/research/humor-mechanisms.md`.
- `docs/content/answer-generation-guide.md`, `docs/content/taxonomy-v1.md`, and `docs/content/stage-1-answer-examples.md`.
- `docs/testing.md` and `docs/verification.md`.
- `docs/evaluation/README.md`, `docs/evaluation/stage-1-results.md`, and both live reports.
- Existing corpus/fixture documentation and any other project documentation found during the audit.

Compare documented claims with `composer.json`, `composer.lock`, the service and test configuration, `phpunit.xml.dist`, relevant `src/` implementation, and actual tests. Source/configuration is evidence and remains read-only in this task.

## Product and architectural context

We Know / Instant Answer is an English satirical service with the slogan **We don't think. We know.** Target behavior: question → classification → uniform selection of a prepared absurd answer from the first eligible pool → immutable shareable result. It is not a chatbot or a runtime answer generator.

- Symfony 7.4 LTS, PHP dependency baseline 8.2; the last actual runtime used was PHP 8.4.10. SSR/Twig; a future vanilla JavaScript POST fetch is planned.
- Jev classifies intent and topic in **one HTTP request containing two Choice questions**. It never receives the phrase library or generates the final answer.
- A future PHP selector chooses uniformly from eligible prepared answers. It ignores `humor_mechanism`, which belongs to `ANSWER_ELIGIBILITY` metadata.
- `ANY` is answer eligibility only, never a classifier result. A malformed response or unknown category is a typed failure, not a fabricated successful OTHER/GENERAL prediction.
- Future selection tiers are `(intent, topic)` → `(intent, GENERAL)` → `(ANY, GENERAL)`; classification failure goes directly to the universal tier in the future application policy. That policy is not implemented by the adapter.
- UUID PK / `created_at` and eligibility uniqueness are accepted requirements for future persistence. The proposed `VERDICT.decision_context` structure has **not** been finalized or implemented.

## Last verified implementation state: 2026-10-11

The previous chat implemented scoped stage 1 only:

- Typed classification enums, success/failure objects, and `QuestionClassifier`.
- `JevClassifier`, strict response normalizer, Symfony DI, dedicated plain HttpClient, no automatic retries or redirects, and no confidence threshold.
- Taxonomy/rubric/corpus version **1.0.0**, with 9 intents and 7 topics. Rules cover duration, explicit alternatives, salary/career, technology/work, multiple questions, statements, typos, and instruction attacks.
- **81 synthetic English questions:** 54 development + 27 holdout. Labels are author-written and not independently human-reviewed. Holdout was not used to tune the rubric and was not called live.
- `app:jev:evaluate`, offline fixture replay through the real adapter or explicitly opted-in bounded live requests. Reports include model/rubric/corpus versions, counts and denominators, intent/topic/joint accuracy, confusion matrices, technical failures, latency P50/P95, and known token usage.
- Separate stage-1 answer examples with `hold` status and known failed probes, written after reading the finished humor guide. They are not imported content or approved production eligibility.
- Added packages: HttpClient 7.4.20, http-client-contracts 3.7.3, and polyfill-php83 1.43.0. Check the current lock rather than assuming these remain unchanged.

Not implemented: database, migrations, import, selector, verdict persistence, POST `/answer`, frontend fetch, permalink, administration, circuit breaker, queues, Redis, or runtime answer generation. The existing public route remains a GET/HEAD skeleton with no functional submit.

### Current accepted timeout policy

Serhii explicitly chose **600 ms idle timeout / 800 ms max_duration** after comparing two live runs. Current defaults are `JEV_TIMEOUT=0.60` and `JEV_MAX_DURATION=0.80`; initial 300/400 ms values are historical comparison evidence, not current defaults. Model default is pinned to `jev-1.13.0` and is configurable.

The project uses **one JEV_API_KEY**. Serhii rejected requiring a separate test key/account for this small project. The key was moved from versioned `.env` to already ignored `.env.local`; it was not printed or copied into reports. Default tests/replay do not need credentials or external network access.

### Actual prior checks

- Default PHPUnit: **156 tests, 561 assertions, pass**, including all 5 skeleton tests, on PHP 8.4.10.
- Offline adapter tests: success, malformed/missing/invalid responses and numbers, enum rejection, distributions/usage, HTTP 401/403/422/429/529/500/502/503, redirect, timeout and transport errors, payload/endpoint/headers, exactly two Choice questions per call, no retry, configurable budgets, DI wiring, and deny-network test services. No flaky sleep-based tests.
- Deterministic metric fixtures verify accuracy denominators, confusion matrices, failures, P50/P95 nearest-rank computation, and known versus unavailable token usage. CLI preflight and bounded offline replay are tested.
- Composer manifest/platform checks, YAML/DI validation, 27 PHP syntax checks, Markdown links/fences/JSON blocks, and report consistency passed.
- The final documentation validation before this handoff checked **17 Markdown files including fixture README and 94 local links**. Those counts are historical; this handoff itself adds another document. Recount after translation rather than silently treating old counts as current.
- Production-mode offline CLI replay passed with current 600/800 ms settings. No new browser/server smoke was run during stage 1; existing functional tests cover GET/HEAD/404/405.

### Recorded live evidence — do not rerun without new authorization

Serhii authorized two sequential development batches of 54 calls each, **108 requests total**, with no retries. Each request classified both dimensions. The rubric and labels were not changed between batches.

Reports:

- `docs/evaluation/live-development-v1.json`: initial 300/400 ms; 54 attempted, 41 valid successes, 13 timeouts; intent/topic 38/54, joint 35/54 over all attempts; P50/P95 249.8/304.6 ms; known input/output tokens 41,628/6,325 from 41 successes. HTTP-policy metadata was appended after the run, explicitly noted in the report.
- `docs/evaluation/live-development-v1-600-800.json`: 600/800 ms; **54/54 valid successes**, 0 technical failures; intent/topic **52/54 (96.3%)**, joint **50/54 (92.6%)**; P50/P95 **238.4/408.9 ms**; tokens **54,826 input / 8,330 output**. Resolved model was `jev-1.13.0`.

These are repeated measurements of the same 54 development questions, not 108 independent samples. Technical failure rate is not empty-catalog fallback rate. The first batch's latency includes interrupted timeout attempts; the comparison is not a controlled causal experiment or production SLA. Usage for the 13 timeouts is unknown, and those calls may have been billed.

Outstanding gaps: live holdout, independent human labels and humor assessment, real PHP 8.2 runtime, production region/concurrency and sustained latency/failure rates, account billing/retention details. HTTP error cases were mocked rather than deliberately triggered live. A separate sandbox endpoint/test-key scheme was not found in the checked public TypeSafe documentation; this is not proof that no private option exists.

## Validation and final handoff expectations

Use a proportionate set of offline checks to support the documentation audit. Suitable existing commands include `rtk proxy php vendor/bin/phpunit`, Composer validation/platform checks, YAML/container lint, and offline `app:jev:evaluate`. Choose only the checks relevant to the claims you need to establish. Any results quoted as performed by the new chat must have actually been run there.

Validate local Markdown links and translated heading anchors, JSON code blocks, fences, and any edited Mermaid syntax without touching Claude files or generating product changes. Do not create new measured claims by copying old numbers into a newly dated run. Preserve the two original live reports, corpus versions, sample IDs, labels, and fixture behavior.

Finish the documentation audit, English translation, and root `AGENTS.md`; do not continue into stage 2 or perform live evaluation. Report remaining gaps plainly and leave Git delivery to Serhii's separate infrastructure.
