# Synthetic backend catalog

`development-v1.json` contains five plain synthetic answers and six explicit eligibility pairs, including a universal pool. Stable UUIDs and UTC timestamps are fixture identifiers, not editorial provenance. This file is separate from the frozen Jev corpus and research hold drafts. Its mechanism labels exercise import/diagnostics only; they do not assert humor or pool quality.

Load explicitly with `rtk proxy php bin/console app:catalog:import --development-fixture` after migrating the development SQLite database. The flag replaces the catalog and is prohibited in production. Generic fixture-directory imports are refused without this flag. Tests load their own isolated databases through the importer.

`rtk proxy php tests/Fixtures/Catalog/smoke.php` imports this catalog into `var/we-know.sqlite`, creates a synthetic verdict using a fake timeout classifier, and verifies the stored snapshot through a separate connection. It reads no env secrets and makes no HTTP call; each run adds one verdict. Do not run it against an editorial catalog, because import is full replacement.

Format, configuration and remaining acceptance: [backend guide](../../../docs/backend.md). Real answer authoring must follow the [guide](../../../docs/content/answer-generation-guide.md) and separate human review.
