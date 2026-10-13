# We Know / Instant Answer

**We don't think. We know.**

An English satirical service: question → classification → a randomly selected prepared absurd answer from an eligible category → a permanent link. This is Serhii's personal project and personal account.

Repository: https://github.com/guri4ek/we-know

## Current status

As of 2026-10-13: Symfony 7.4/Twig, typed Jev classification/evaluation, and the complete scoped backend are implemented. Doctrine DBAL + Migrations (no ORM) persist the catalog and immutable verdicts. Atomic CLI import, first-pool uniform selection, `POST /answer`, and `GET`/`HEAD /a/{publicId}` are offline tested. Home remains a static placeholder; the later frontend must use a **dark theme**. Historical live evidence remains in [stage-1 results](docs/evaluation/stage-1-results.md).

Active [taxonomy/rubric v2](docs/content/taxonomy-v2.md): 14 intents / 13 topics. The [offline seed](docs/content/seed-v2/README.md) contains 24 preliminary answers / 24 eligibilities in a new isolated SQLite database. [Editorial matrices](docs/content/seed-v2/editorial.md) and [database evidence](docs/content/seed-v2/database-verification.json) distinguish author screening from human approval. The [100-question live trial](docs/evaluation/corpus-v2/README.md) is frozen and prepared, **not run**.

The local database is `/Users/serhii/OwnProjects/we-know/var/we-know.sqlite`, outside `public/` and ignored. It contains preserved **synthetic development content** and an older development schema; it was not changed or reset. The v2 demonstration file is `var/answer-seed-v2-20261012.sqlite`; see its explicit-target setup before running current backend flows. PostgreSQL remains the definite production engine; no local PostgreSQL acceptance run was available. See [backend setup and contract](docs/backend.md).
Start with the root [AGENTS.md](AGENTS.md), [documentation map](docs/README.md), [source-session context](docs/context.md), [technical plan](docs/technical-plan.md), and [C4 model](docs/architecture/c4.md).

Before creating or editing answers, read the [offline authoring guide](docs/content/answer-generation-guide.md). Its examples are editorial drafts, not a ready seed; the [humor research](docs/research/humor-mechanisms.md) separates primary sources from practical hypotheses.

The [data model](docs/architecture/data.md) documents the implemented UUID/UTC schema and diagnostic JSON. Catalog edits and removal preserve historical text and decision snapshots; eligibility deletion sets the verdict FK to NULL. The [backend handoff](docs/handoff/backend-implementation-prompt.md) records the scope used for implementation. Deployment, human content acceptance, and frontend implementation remain separate stages.

## Local setup

Development and minimum runtime are **PHP 8.4**; Composer now requires `>=8.4` and resolves against `8.4.0`. Actual checks used installed PHP 8.4.10. The planned PHP 8.5 transition has not been tested. Composer 2 and PDO are required, plus `pdo_sqlite` locally or `pdo_pgsql` in production. Composer checks the remaining extensions.

```sh
rtk proxy composer install --prefer-dist --no-plugins --no-scripts
rtk proxy php bin/console about
rtk proxy php vendor/bin/phpunit
# New target only; refuses an existing file:
rtk proxy php tools/verify-seed-v2.php /Users/serhii/OwnProjects/we-know/var/answer-seed-v2-recheck.sqlite
rtk proxy env APP_ENV=test APP_DEBUG=0 JEV_API_KEY= php bin/console app:jev:evaluate
rtk proxy php -S 127.0.0.1:8000 -t public public/index.php
```

Open http://127.0.0.1:8000. The built-in PHP server is for development only. When static assets are added, use a separate development router or Symfony CLI so asset requests do not reach the front controller.

`.env` contains local defaults only. Secrets belong in `.env.local` or environment variables; `APP_SECRET` is currently empty because sessions, CSRF, and signed URLs are not wired. Set a random secret before adding those features. Production requires `APP_ENV=prod`, `APP_DEBUG=0`, a web server with document root `public/`, and a separate deployment preparation stage.

Runtime packages include FrameworkBundle, Console, Dotenv, YAML, TwigBundle, HttpClient, RateLimiter and Lock 7.4, Doctrine DBAL 4.5 and Migrations 3.9 (without ORM). Development packages include PHPUnit 11.5, BrowserKit, and CssSelector. Bootstrap/configuration is explicit; Flex, Runtime, automatic recipes, and Composer scripts are not enabled. New packages will need explicit configuration.

Jev: `JEV_MODEL` in `.env` (default pin `jev-1.13.0`); one **production `JEV_API_KEY`** in `.env.local`/environment for application calls and authorized live evaluation. No separate test key or sandbox workflow is required. `JEV_TIMEOUT=0.60` and `JEV_MAX_DURATION=0.80` are configurable in seconds. One POST carries intent/topic; no retries, redirects, or confidence threshold. Default replay and tests need no key/network. Live requires `--live`, an explicit `--limit`, and authorization for that run; [report format and commands](docs/evaluation/README.md) also explain usage and billing limits.

Backend configuration, controlled errors, proxy policy, import semantics and offline reproduction: [backend guide](docs/backend.md). Checks and limitations: [verification.md](docs/verification.md), with separate backend, audit and historical live events.
