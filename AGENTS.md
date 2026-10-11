# We Know project instructions

## Project context

This is Serhii's personal project, using his personal account. Work-account and WHMCS reviewer-role defaults must not be imported as this project's role or product constraints. Follow the user's explicit task scope and applicable personal instructions; this file does not grant standing authorization for implementation, Git delivery, deployment, or paid API calls.

Communicate with Serhii in Ukrainian. Write project documentation and instructions in English. The product and prepared answer library are English.

Development uses locally installed PHP 8.4, with a planned transition to PHP 8.5. The minimum supported PHP version is 8.4. SQLite is the local database; PostgreSQL is the definite production database. Jev uses one production key for application calls and authorized live evaluation; do not require a separate test key or sandbox workflow. Keep credentials private and live usage bounded by the authorized scope. Configuration and verification details belong in the linked documents.

## Hard principles

1. **Think before code.** Analyze the task and evidence before acting. Make assumptions explicit, resolve consequential ambiguity, and understand the intended outcome rather than blindly executing instructions.
2. **Simplicity first.** Choose the smallest sufficient solution. Avoid speculative abstraction, unnecessary complexity, features, dependencies, or code outside the authorized need.
3. **Surgical edits.** Change only what the task requires. Preserve adjacent behavior, unrelated work, and existing conventions; do not turn a focused change into a broad cleanup.
4. **Goal-oriented execution — test first.** Before implementing a behavior change or fixing a bug, first write a focused test that reproduces the problem or expresses the desired behavior, and verify that it fails for the relevant reason. Implement the smallest change that makes it pass, then verify the outcome. For documentation-only work, establish verifiable acceptance evidence and validate the artifacts without manufacturing a product-code change just to satisfy this rule.

## Boundaries and evidence

- Stay within explicit authorization. Plans, historical permissions, and documented future behavior do not authorize the next stage or external actions. Apply the relevant personal tooling instructions, including [RTK](../../.codex/RTK.md).
- Inspect current source and adjacent flows before making claims. Distinguish implemented behavior, offline tests, historical live measurements, editorial judgments, proposals, deferred work, and unverified assumptions. Record new verification separately from historical evidence.
- Preserve other contributors' work, frozen corpora, recorded reports, identifiers, and provenance. Change evidence artifacts only when explicitly requested and version them appropriately.
- Protect secrets. Do not inspect or disclose credentials, copy secret environment files, or put keys in documentation, commands, logs, fixtures, or reports.
- Claude-specific files and backups are strictly read-only, including indirect changes through generators, installers, and sync tools. Personal configuration and backups are outside product work unless separately authorized.
- Jev classifies intent/topic; it does not generate or rank final answers. Prepared answers are selected uniformly from the first eligible pool. Humor metadata belongs to eligibility and does not influence selection.

## Documentation entry points

Start with the [documentation map](docs/README.md) and [project README](README.md). Use [context](docs/context.md) and [decisions](docs/architecture/decisions.md) for requirements; the [technical plan](docs/technical-plan.md), [C4](docs/architecture/c4.md), and [data model](docs/architecture/data.md) describe the target architecture.

Read the [answer authoring guide](docs/content/answer-generation-guide.md) in full before writing or editing answer content. Consult the [taxonomy](docs/content/taxonomy-v1.md), [humor research](docs/research/humor-mechanisms.md), and [Jev research](docs/research/jev.md) for their respective evidence and limitations.

Use the [testing strategy](docs/testing.md), [evaluation guide](docs/evaluation/README.md), [stage-1 results](docs/evaluation/stage-1-results.md), and [verification record](docs/verification.md) to establish what has actually been checked.
