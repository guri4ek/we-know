# Offline seed v2

Local preliminary editorial seed, created 2026-10-12; handoff verified 2026-10-13. **24 answers, 24 eligibilities, 9 populated classification pools and 173 empty**, plus two ANY/GENERAL answers. These are completed English texts screened by the author, not human-approved or production-ready content. Human scores remain null.

- [catalog.json](catalog.json): the complete replacement import, format 2 / active taxonomy 2.0.0. Stable UUIDs and UTC creation timestamps.
- [editorial.md](editorial.md): matrices, screening method, weakest-pair judgments, rejected texts, pilot provenance and coverage.
- [editorial.json](editorial.json): exact original quotations/source URLs, separate form/context/clarity cells, author scores and null human scores.
- [database-verification.json](database-verification.json): actual SQLite checks, catalog hash and stored counts.
- [Artifact verification](artifact-verification.json): exact-source/category/count/link validation and preservation evidence.

The created database is `/Users/serhii/OwnProjects/we-know/var/answer-seed-v2-20261012.sqlite`, outside `public/`. It contains only this seed catalog plus **five offline-double demonstration verdicts**. The existing `var/we-know.sqlite` was not modified. No environment default was switched. Use an explicit database target for any later application inspection; changing a target does not authorize live calls.

[verify-seed-v2.php](../../../tools/verify-seed-v2.php) reproduces the database demonstration using the existing migration and import command. It requires a **new** absolute `var/answer-seed-v2-<name>.sqlite` path and refuses an existing file. It loads no Dotenv/credentials and calls no provider. Example, using a new name:

```sh
rtk proxy php tools/verify-seed-v2.php /Users/serhii/OwnProjects/we-know/var/answer-seed-v2-recheck.sqlite
```

The script checks import validation/rollback, repeat import with unchanged identities/times, all fallback tiers, successful OTHER versus failure, each selectable index, mechanism independence, immutable snapshots after edit/disable/delete, foreign keys and reconnect durability. Temporary mutation probes roll back; the final catalog exactly matches the complete JSON. The production random source uses `random_int(0, count - 1)`; deterministic checks establish equal index access and absence of mechanism weighting, not a statistical distribution result.

**Never run the synthetic smoke importer on this database.** Import is full replacement, not merge. The unreleased initial migration was updated directly for v2; an existing database marked with its earlier schema is not automatically upgraded. This development task deliberately created a fresh isolated file rather than migrating existing data.

Ten pilot survivors were retained unchanged; B2 was excluded; none was revised or declared human-approved. Fourteen new texts passed. The 30–50 target was not filled because eleven evaluated candidates failed portability, implications or novelty. Unseen-pool fit, medical/financial tone, criterion-only choices and repetition fatigue need human review. PostgreSQL acceptance and the later [100-question live trial](../../evaluation/corpus-v2/README.md) remain unexecuted.
