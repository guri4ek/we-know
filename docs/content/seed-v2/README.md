# Offline seed v2

Current development seed, **2026-10-15: 1,055 answers / 1,055 eligibilities**, including all 24 earlier answers unchanged and 1,031 accepted author additions. Human scores remain null. Author screening permits these drafts in the local development seed; it is not human approval or production certification.

[**Complete canonical catalog**](catalog.json) · [Current editorial batch, coverage and limitations](batch-20261015/README.md) · [Current database verification](batch-20261015/final-verification.json)

The canonical format remains integer version 2, active taxonomy/rubric 2.0.0. It contains full English texts, stable UUIDs and UTC creation times. Humor metadata belongs to eligibility and does not affect selection. Import replaces the entire catalog; partial batches would remove omitted content.

## Current local workflow

The disposable default target is `var/we-know.sqlite`. It now has the current schema, this complete editorial catalog, and zero verdicts. It contains no synthetic smoke content. Commands require `APP_ENV=dev` and the exact default SQLite target:

```sh
rtk proxy composer db:reset
rtk proxy composer db:migrate
rtk proxy composer db:seed
# Equivalent complete sequence, stopping immediately on failure:
rtk proxy composer db:fresh
rtk proxy php tools/verify-local-database.php
```

Reset deletes only that database and its regular SQLite sidecars, including migration state. Migrate uses the existing migration infrastructure; seed uses the existing importer and `docs/content/seed-v2/catalog.json`. See [backend setup](../../backend.md#development-database-and-offline-reproduction). Run fresh after editing an unreleased table-creation migration; no incremental data upgrade is provided.

## Preserved historical evidence

The 2026-10-12–13 pilot contained **24 answers / 24 eligibilities**, nine populated classification pools and 173 empty pools. Its [editorial prose](editorial.md), [matrices](editorial.json), [database report](database-verification.json) and [artifact report](artifact-verification.json) remain unchanged historical evidence, not measurements of the expanded catalog. The old default database was untouched during that earlier task; the separately authorized 2026-10-15 task recreated it.

The original demonstration `var/answer-seed-v2-20261012.sqlite` remains preserved with its 24-answer catalog and five offline-double verdicts. Other pre-existing demonstration files were also preserved. Never load a synthetic fixture into an editorial demonstration.

The helper [verify-seed-v2.php](../../../tools/verify-seed-v2.php) can create a **new** named `var/answer-seed-v2-<name>.sqlite` demonstration of the current catalog; it refuses an existing target and adds five offline-double verdicts. It loads no credentials and calls no provider. Its METHOD/SPORTS case now demonstrates intent fallback because METHOD/TECH gained exact content. The default development lifecycle does not call this helper or create demonstration verdicts.

```sh
rtk proxy php tools/verify-seed-v2.php /Users/serhii/OwnProjects/we-know/var/answer-seed-v2-recheck.sqlite
```

PostgreSQL, the separately planned live classification trial, and human humor/portability approval remain unverified.
