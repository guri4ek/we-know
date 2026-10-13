# Question-source discovery pilot v1

Historical pilot. Continue with the [simplified question collection](../questions-v1/README.md): 3,593 titles in plain text and a separate URL list.

Date: 2026-10-11. Status: exploratory source-access and provenance pilot, not a popularity ranking or a representative sample. Three user-requested Luna agents explored separate sources; the parent reviewed their artifacts. No question or answer was generated for this collection.

## Research direction

- Start from existing user-posted questions, preserving original wording and provenance. Public user posts do not independently prove human authorship.
- Reddit is excluded by the user's decision because API access will not be obtained.
- The existing answer-catalog import format is provisional and does not constrain this research. These files are not importable answer seeds.
- Discover topics, requested outcomes, and answer forms from the questions before mapping them to current Intent/Topic enums. No taxonomy labels were assigned in this pilot.
- Collection, later taxonomy analysis, and eventual answer authoring are distinct stages. No model training, paid API evaluation, database import, or collector implementation occurred.

## Artifacts

| Source | Records | Report | Question data |
| --- | --- | --- | --- |
| Ask MetaFilter | 3 page-verified; 7 search candidates | [Source report](metafilter/report.md) | [JSONL](metafilter/questions.jsonl) |
| Fluther / Answerbag | 2 page-verified; 1 search candidate | [Source report](alternatives/report.md) | [JSONL](alternatives/questions.jsonl) |
| Quora via BEIR preview | 1 dataset candidate | [Source report](quora/report.md) | [JSONL](quora/questions.jsonl) |

Total: 14 records, of which 5 were independently verified by the parent against individually opened question pages and 9 remain candidates. Parent review corrected seven MetaFilter records initially described as page evidence: detailed search-result text is still search evidence, not an independently opened page. Do not count candidates as verified questions or silently mix them into later evaluation data.

`verified` means original question-page text was checked, including indexed/cached page text. It does not certify human authorship, current popularity, or permission for bulk reuse. `candidate` records have weaker original-page provenance, such as search-result text or a dataset preview without an original post URL. Read `evidence_kind` and `notes` before using a record. Missing values remain null; zero must not be inferred.

`retrieved_on` records our research date. The web reader can serve older indexed content, so engagement counts must not be treated as measurements taken on that date. Relative publication ages were not converted to dates. Source categories are original site metadata, not proposed We Know labels.

## Direct access checks by the parent

- Stack Exchange: a keyless request to `/2.3/questions?site=cooking&sort=votes&order=desc&pagesize=1` returned HTTP 200 and one item; reported remaining quota was 299. This was an access probe, not part of the saved question sample. [API documentation](https://api.stackexchange.com/docs/questions).
- Ask MetaFilter: [official RSS index](https://rss.metafilter.com/) returned HTTP 200. [Ask RSS](https://rss.metafilter.com/ask.rss) returned HTTP 200 and parsed as XML with four items, including title, description, link, GUID, publication date, category, and creator fields. The robots URL returned HTTP 403; that response alone does not establish the site's crawl policy. Historical feed coverage was not tested.
- Fluther: the [homepage](https://www.fluther.com/) returned HTTP 200 and advertised `/feeds/general` as an RSS endpoint. Feed payload and historical pagination were not tested. The parent reopened both saved Fluther question pages and confirmed the recorded text and metadata.
- Quora: [robots.txt](https://www.quora.com/robots.txt) returned HTTP 200. Its wildcard rules disallow question-page crawling, with exceptions for some non-question paths. No bypass was attempted. The [BEIR dataset preview](https://huggingface.co/datasets/BeIR/quora) is accessible; original-post provenance and original reuse terms remain unresolved. A duplicate-retrieval dataset does not establish question frequency or popularity.

These observations establish limited access, not reliable collection of 1,000 questions. Source-specific reuse terms remain to be checked before selecting a bulk collection method.

## What this pilot teaches

Ask MetaFilter and Fluther merit a larger source pilot alongside the already reachable Stack Exchange API. Quora's historical dataset is a possible source of phrasing variety, but cannot provide a current popularity ranking. Answerbag remains a candidate; GirlsAskGuys was discovered but not individually sampled.

Agent browsing is useful for discovery and evidence checks, but free-form searching creates selection bias. The MetaFilter searches explicitly included constructions such as "What should I" and "Can anyone recommend"; the two verified Fluther questions both use "How does". This sample must not be used to estimate the prevalence of those forms or choose enum categories by simple counts.

For a larger collection, first fix source/time-window selection, distinguish popular posts from a time-based sample, and record retrieval queries. Preserve semantic duplicate groups and their separate occurrences instead of deleting evidence of repeated needs. Keep some questions aside before taxonomy design. Corpus in earlier discussion simply means this collected set of questions; it does not imply training a model.

## Validation scope

Local validation passed for JSONL parsing, required fields, unique record IDs and source URLs, allowed evidence/status values, dates, short excerpt lengths, and local document links. The parent checked all five page-verified records and the single Quora preview text; the seven other MetaFilter pages could not be reopened and retain candidate status. Source reports preserve the distinction between observed evidence and unresolved access or metadata. This does not establish representativeness, humor quality, production acceptance, or bulk reuse permission.
