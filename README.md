# We Know / Instant Answer

**We don't think. We know.**

An English satirical service: question → classification → a randomly selected prepared absurd answer from an eligible category → a permanent link. This is Serhii's personal project and personal account.

Repository: https://github.com/guri4ek/we-know

## Current status

As of 2026-10-11: Symfony 7.4 LTS, Twig, and `GET /` with a static placeholder. Stage 1 includes taxonomy/rubric v1, 81 synthetic questions, a typed Jev adapter, offline tests, and a CLI evaluation runner. Historical live development runs and their limitations are in the [stage-1 results](docs/evaluation/stage-1-results.md). The selector, database, POST fetch, and permalink are not implemented.

Start with the root [AGENTS.md](AGENTS.md), [documentation map](docs/README.md), [source-session context](docs/context.md), [technical plan](docs/technical-plan.md), and [C4 model](docs/architecture/c4.md).

Before creating or editing answers, read the [offline authoring guide](docs/content/answer-generation-guide.md). Its examples are editorial drafts, not a ready seed; the [humor research](docs/research/humor-mechanisms.md) separates primary sources from practical hypotheses.

The [data model](docs/architecture/data.md) incorporates UUID PK/created_at in every table and `humor_mechanism` on eligibility without affecting random selection. A simplified VERDICT with diagnostic JSON remains a proposal. Development migrations may be edited for disposable databases before production; persistent data will require incremental migrations. No database or migrations exist yet.

## Local setup

Development uses **locally installed PHP 8.4**, with a planned transition to **PHP 8.5**, and Composer 2. The verified runtime is PHP 8.4.10. The current manifest still allows PHP 8.2+ and pins dependency resolution to 8.2.0; these settings are an error: the intended minimum is PHP 8.4. Correction is recorded in the [audit findings](docs/verification.md#documentation-audit-findings) for a separate prompt. The PHP 8.5 transition has not been performed or tested.

Direct extension requirements: ctype and iconv; Composer checks the complete dependency requirements. **SQLite locally and PostgreSQL in production are definite choices**; persistence is still future work. It will require `pdo_sqlite` locally and `pdo_pgsql` for PostgreSQL.

```sh
rtk proxy composer install --prefer-dist --no-plugins --no-scripts
rtk proxy php bin/console about
rtk proxy php vendor/bin/phpunit
rtk proxy php bin/console app:jev:evaluate
rtk proxy php -S 127.0.0.1:8000 -t public public/index.php
```

Open http://127.0.0.1:8000. The built-in PHP server is for development only. When static assets are added, use a separate development router or Symfony CLI so asset requests do not reach the front controller.

`.env` contains local defaults only. Secrets belong in `.env.local` or environment variables; `APP_SECRET` is currently empty because sessions, CSRF, and signed URLs are not wired. Set a random secret before adding those features. Production requires `APP_ENV=prod`, `APP_DEBUG=0`, a web server with document root `public/`, and a separate deployment preparation stage.

Runtime packages: FrameworkBundle, Console, Dotenv, YAML, TwigBundle, and HttpClient 7.4. Development packages include PHPUnit 11.5, BrowserKit, and CssSelector. Bootstrap/configuration is explicit; Flex, Runtime, automatic recipes, and Composer scripts are not enabled. New packages will need explicit configuration.

Jev: `JEV_MODEL` in `.env` (default pin `jev-1.13.0`); one **production `JEV_API_KEY`** in `.env.local`/environment for application calls and authorized live evaluation. No separate test key or sandbox workflow is required. `JEV_TIMEOUT=0.60` and `JEV_MAX_DURATION=0.80` are configurable in seconds. One POST carries intent/topic; no retries, redirects, or confidence threshold. Default replay and tests need no key/network. Live requires `--live`, an explicit `--limit`, and authorization for that run; [report format and commands](docs/evaluation/README.md) also explain usage and billing limits.

Checks and limitations: [verification.md](docs/verification.md), including a separate documentation-audit event.
