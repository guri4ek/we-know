# We Know / Instant Answer

**We don't think. We know.**

An English satirical service: question → classification → a randomly selected prepared absurd answer from an eligible category → a permanent link. This is Serhii's personal project and personal account.

Repository: https://github.com/guri4ek/we-know

## Current status

As of 2026-10-15: Symfony 7.4/Twig, typed Jev classification/evaluation, the complete scoped backend, and the **dark-theme frontend** are implemented locally. The English UI includes the question form, inline verdict, permanent saved answer, link copying, About/FAQ, data disclosure and styled errors. Vanilla JS uses the existing JSON API without a build pipeline. Doctrine DBAL + Migrations (no ORM) persist the catalog and immutable verdicts. Current offline evidence is in [verification](docs/verification.md#frontend-implementation--2026-10-15); historical live evidence remains in [stage-1 results](docs/evaluation/stage-1-results.md).

Active [taxonomy/rubric v2](docs/content/taxonomy-v2.md): 14 intents / 13 topics. The [canonical editorial seed](docs/content/seed-v2/README.md) contains **1,055 answers / 1,055 eligibilities**: all 24 earlier answers unchanged plus 1,031 additions. [Current screening and coverage](docs/content/seed-v2/batch-20261015/README.md) distinguish author-accepted development drafts from human approval. The [100-question live trial](docs/evaluation/corpus-v2/README.md) remains prepared, **not run**.

The disposable local database is `var/we-know.sqlite`, outside `public/` and ignored. It was recreated on 2026-10-15 with the current schema and complete editorial catalog, **zero verdicts and no synthetic smoke content**. Existing `answer-seed-v2-*` demonstration databases remain preserved. PostgreSQL remains the production engine; it has not been verified locally. See [backend setup and contract](docs/backend.md).
Start with the root [AGENTS.md](AGENTS.md), [documentation map](docs/README.md), [source-session context](docs/context.md), [technical plan](docs/technical-plan.md), and [C4 model](docs/architecture/c4.md).

Before creating or editing answers, read the [offline authoring guide](docs/content/answer-generation-guide.md). Its examples are editorial drafts, not a ready seed; the [humor research](docs/research/humor-mechanisms.md) separates primary sources from practical hypotheses.

The [data model](docs/architecture/data.md) documents the implemented UUID/UTC schema and diagnostic JSON. Catalog edits and removal preserve historical text and decision snapshots; eligibility deletion sets the verdict FK to NULL. The [backend handoff](docs/handoff/backend-implementation-prompt.md) records its historical scope. [Frontend behavior](docs/frontend-design.md) is implemented locally; deployment, human content acceptance and the finalized launch privacy policy remain separate stages.

## Local setup

Development and minimum runtime are **PHP 8.4**; Composer now requires `>=8.4` and resolves against `8.4.0`. Actual checks used installed PHP 8.4.10. The planned PHP 8.5 transition has not been tested. Composer 2 and PDO are required, plus `pdo_sqlite` locally or `pdo_pgsql` in production. Composer checks the remaining extensions.

```sh
rtk proxy composer install --prefer-dist --no-plugins --no-scripts
rtk proxy php bin/console about
# Recreate disposable dev data: reset → migrate → canonical editorial seed:
rtk proxy composer db:fresh
rtk proxy php tools/verify-local-database.php
rtk proxy php vendor/bin/phpunit
# New target only; refuses an existing file:
rtk proxy php tools/verify-seed-v2.php /Users/serhii/OwnProjects/we-know/var/answer-seed-v2-recheck.sqlite
rtk proxy env APP_ENV=test APP_DEBUG=0 JEV_API_KEY= php bin/console app:jev:evaluate
rtk proxy php -S 127.0.0.1:8000 -t public public/router.php
```

The equivalent individual database commands are `rtk proxy composer db:reset`, `rtk proxy composer db:migrate`, and `rtk proxy composer db:seed`, in that order. They require `APP_ENV=dev` and the exact default local SQLite URL. Reset clears application data and migration state; migrate creates the current schema; seed imports `docs/content/seed-v2/catalog.json`. Fresh stops on the first failure. Stop local application processes before resetting. After editing an unreleased table-creation migration, run fresh; rerunning an already recorded migration does not rebuild its tables. No upgrade path or separate schema definition is used.

Open [http://127.0.0.1:8000](http://127.0.0.1:8000). The built-in PHP server is for development only; `public/router.php` serves assets directly and routes application requests through Symfony. The browser origin must match `APP_PUBLIC_ORIGIN` (the default is this exact address). Submitting through the development application uses the configured production Jev key; automated frontend checks use intercepted responses and isolated synthetic data instead. See [browser test instructions](docs/testing.md#frontend-browser-checks--2026-10-15).

`.env` contains local defaults only. Secrets belong in `.env.local` or environment variables; `APP_SECRET` is currently empty because sessions, CSRF, and signed URLs are not wired. Set a random secret before adding those features. Production requires `APP_ENV=prod`, `APP_DEBUG=0`, a web server with document root `public/`, and a separate deployment preparation stage.

Runtime packages include FrameworkBundle, Console, Dotenv, YAML, TwigBundle, HttpClient, RateLimiter and Lock 7.4, Doctrine DBAL 4.5 and Migrations 3.9 (without ORM). Development packages include PHPUnit 11.5, BrowserKit, and CssSelector. Bootstrap/configuration is explicit; Flex, Runtime and automatic recipes are not enabled. Composer scripts provide the local database lifecycle. New packages will need explicit configuration.

Jev: `JEV_MODEL` in `.env` (default pin `jev-1.13.0`); one **production `JEV_API_KEY`** in `.env.local`/environment for application calls and authorized live evaluation. No separate test key or sandbox workflow is required. `JEV_TIMEOUT=0.60` and `JEV_MAX_DURATION=0.80` are configurable in seconds. One POST carries intent/topic; no retries, redirects, or confidence threshold. Default replay and tests need no key/network. Live requires `--live`, an explicit `--limit`, and authorization for that run; [report format and commands](docs/evaluation/README.md) also explain usage and billing limits.

Backend configuration, controlled errors, proxy policy, import semantics and offline reproduction: [backend guide](docs/backend.md). Checks and limitations: [verification.md](docs/verification.md), with separate backend, audit and historical live events.
