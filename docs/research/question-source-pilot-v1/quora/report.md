# Quora question source pilot

Retrieved 2026-10-11. This bounded source check supports We Know taxonomy research. It makes no Intent or Topic assignments and does not create answer content.

## Result

One short, verbatim candidate appears in `questions.jsonl`. The BEIR Quora dataset viewer displays exact text and an item ID, but no original Quora post URL, publication date, category, or popularity. The item cannot be independently checked against its original post or treated as fresh. Its presence in a duplicate-retrieval corpus says nothing about popularity.

The viewer lists 523K corpus rows and identifies Quora as a duplicate-question retrieval task. BEIR says it prepares public datasets but does not claim users have permission to use them; users remain responsible for permission and attribution. Hugging Face displays `cc-by-sa-4.0`, but that metadata alone does not establish that BEIR or the hosting account can grant downstream rights in original Quora user text. Treat this item as a research lead, not cleared material for redistribution or production use. Clarify original source terms before scaling or reusing text.

## Access and searches

Direct Quora pages were unavailable in the research browser because `quora.com` is blocked by its robots policy. No access restriction was bypassed. This prevented validation of original page context, date, category, author status, and engagement counts.

Searches run:

- `site:quora.com robots.txt Quora Disallow /`
- `Quora Question Pairs dataset original source license redistribution`
- Opened the Hugging Face dataset card and BEIR GitHub repository.

Sources:

- [BEIR Quora dataset card and viewer](https://huggingface.co/datasets/BeIR/quora): preview text, item ID, corpus count, task, and displayed license metadata.
- [BEIR repository](https://github.com/beir-cellar/beir): dataset listing and disclaimer that preparing a public dataset does not establish user permission.

## Suitability and limits

QQP was assembled for duplicate-question detection, not sampled to estimate topic prevalence, current user demand, or popularity. Its examples cannot support frequency weighting or claims about what Quora users currently ask. The sources reviewed do not establish a refresh cadence or collection window. Questions are presented as Quora examples, but this row lacks first-party provenance, so its human authorship is not independently verified here.

The corpus may help discover phrasing variants after reuse terms are checked. It cannot establish a current popularity ranking or supply missing per-post metadata. Historical questions can still support exploratory taxonomy work: timestamps and engagement metrics are not prerequisites for that narrower purpose. Any wider sample needs stable record provenance, deduplication, and checks for duplicate-pair sampling bias. Missing fields must remain unknown.

No taxonomy labels were assigned. The item remains `candidate`: text is visible, while first-party provenance and reuse suitability are unresolved.
