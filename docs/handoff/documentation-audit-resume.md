# Documentation audit: resume handoff

**Status: historical pause snapshot, superseded by the completed audit in this same chat.** Translation and offline/documentation validation below were subsequently completed; the Composer correction remains deferred to a new prompt. See the [completed verification event](../verification.md#documentation-audit-verification-event). Statements about pending translation/checks below describe the earlier pause, not current state.

Paused on 2026-10-11 at Serhii's explicit request to stop before session limits. Work directory: `/Users/serhii/OwnProjects/we-know`. Communication Ukrainian; documentation English. Read the [original audit task](documentation-audit-prompt.md) in full and root [AGENTS.md](../../AGENTS.md). This is partial work, not a completed audit or authorization for product edits/live calls/Git delivery. Work directly, without delegation; prefix shell commands with `rtk`, normally `rtk proxy`. Claude files and personal configuration/backups remain read-only/outside scope. Do not inspect secrets or `.env.local`.

## Latest user clarifications

Personal project and personal account; do not import WHMCS/work reviewer-role defaults as this project's role. Locally installed PHP 8.4 for development and minimum, planned PHP 8.5. SQLite locally and PostgreSQL in production are definite. Jev uses the production key with no separate test key/sandbox workflow; actual expenses should eventually be evaluated. No new live request budget was defined.

The user identified Composer `require.php >=8.2` / platform `8.2.0` as an error and requested findings for correction under a new prompt. Keep manifest/lock/source read-only in this documentation task. This is a pending fix, not an accepted compatibility baseline.

## Changes completed at the pause

Created root AGENTS with personal context, language/scope/evidence/secrets/Claude boundaries, all four mandatory principles (including a failing focused test before behavior changes), concise navigation, and confirmed runtime/database/key policies.

Translated/reconciled: root README, docs map, context, technical plan, C4/data/ADRs, Jev/humor research, authoring guide, taxonomy, stage-1 examples, evaluation README/results. Preserved humor examples, smoke questions/labels, JSON examples, citations, measurements/identifiers. Annotated the already English original handoff with later user clarifications. Fixture README was already English and unchanged.

`docs/testing.md` remains Ukrainian and untouched. `docs/verification.md` remains mostly Ukrainian; only English audit findings/verification-event sections were appended, providing destinations for new links. Full translation and final validation are incomplete.

## Checks executed at the pause

PHPUnit **156 tests / 561 assertions pass**, PHP 8.4.10 / PHPUnit 11.5.57. Composer strict validation/platform checks pass; YAML lint passes 6 files; default-environment container lint passes. No live requests, browser/server smoke, Git commands, dependencies, source edits, personal/Claude writes.

SHA-256 inventory comparison at pause: source/config/test/corpus/report/fixture files unchanged. The only non-Markdown difference was `.gitignore`, changed concurrently outside this audit; it was not edited and must be preserved. Runtime caches are excluded. Do not claim a globally unchanged working tree or overwrite concurrent work.

Original Markdown copies and hash inventory: `/private/tmp/we-know-documentation-audit-before/` (temporary, may disappear). `manifest.json` excludes secrets/env files, Claude, Git, vendor/runtime. Only Markdown was copied. Existing Mermaid/jsdom packages: `/private/tmp/we-know-doc-validation/node_modules`; this audit installed nothing.

## Work identified at the pause (subsequently completed)

1. Translate `docs/testing.md`, preserving all scenario details/history while reconciling current/future status. Existing suites: Application, Unit, Integration. No live PHPUnit profile exists; live uses CLI `app:jev:evaluate --live --limit=...` and production key. 400 ms is historical, current policy 600/800 ms. Table currently mixes existing tests with future validation/selector/persistence/E2E. Distinguish current accuracy/P50/P95 reports from planned F1/P99/threshold/coverage/application-fallback metrics. HTTP contract remains proposed. Separate stale live-PHPUnit/test-key proposals from current workflow rather than implying they exist.
2. Translate historical `docs/verification.md`, retaining bootstrap 5/16, stage-1 156/561, historical counts and supplied Git baseline as history; perform no Git verification. Preserve the separate audit event and record only actual current checks. PHP minimum 8.4, planned 8.5, Composer correction pending; historical PHP 8.2 gap is no longer a deployment target.
3. Validate every local Markdown link/translated heading anchor, fence, JSON block, whitespace, and edited Mermaid syntax (5 C4 + 1 ER), using existing temporary parser if available. Recount current files/links. No browser/dependency installation. External citations are dated research, not newly verified sources.
4. Run relevant offline CLI replay (six default fixtures; optionally bounded production replay) and verify metrics/policy without live traffic. Do not rerun live measurements. Success requires token usage; failed usage unknown. Adapter accepts exactly HTTP 200, no retries/redirects. Offline limits follow fixture-map order, live limits corpus order.
5. Compare preserved examples/smoke rows/JSON/citations to originals. Recheck hashes for source, manifest/lock, frozen corpus, fixtures, original live JSON reports. Guide JSON retains historical `taxonomy_status: proposal` with explicit qualification; do not treat it as active classifier taxonomy. Preserve human-evaluation gaps and concurrent `.gitignore` changes.
6. Mark this pause record superseded on completion, update docs map/status, and give concise Ukrainian report with absolute links to AGENTS, key docs, findings/verification. Leave delivery and Composer correction to Serhii/new prompt.

## Reconciled findings and retained evidence

English docs vs stale Ukrainian rule; personal vs work role; definite PostgreSQL vs tentative choice; production key vs separate test key; current 600/800 vs historical 300/400; frozen taxonomy v1 vs editorial proposals. Smoke corpus preserves pre-v1 editorial assignments (including OTHER probes), not classifier gold. First live report notes policy metadata appended after run. Selector/database/import/VERDICT/POST/fetch/permalink/admin remain unimplemented; decision_context and final mechanism codes proposed.

Live history: repeated 54-question development batches, 108 requests total; 41 successes/13 timeouts at 300/400, 54 successes/0 failures at 600/800. Rubric/labels unchanged; holdout never called. Known tokens 96,454 input / 14,655 output; approximately $0.00405 at the historically published input rate is arithmetic, not billing. Timeout usage, actual charges/retention, human labels/humor, production sustained/concurrent performance, PHP 8.5 remain unverified.
