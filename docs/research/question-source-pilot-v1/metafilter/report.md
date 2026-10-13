# Ask MetaFilter question-source pilot

Retrieved 2026-10-11. This bounded pilot contains 3 verified short English question titles from individually opened Ask MetaFilter post pages and 7 candidate titles seen only in search results. All records provide a direct source URL; candidates are not part of the verified corpus. Categories are source metadata only; no We Know Intent or Topic labels are assigned.

## Access and discovery evidence

Search queries used:

- `site:ask.metafilter.com "How do I" Ask MetaFilter`
- `site:ask.metafilter.com "What should I" "Ask MetaFilter"`
- `site:ask.metafilter.com "Is there a way" ask metafilter`
- `site:ask.metafilter.com/ "What is the best way" "posted by" Ask MetaFilter`
- `site:ask.metafilter.com/ "Does anyone know" "Ask MetaFilter"`
- `site:ask.metafilter.com/ "How can I" "Ask MetaFilter" "answers total"`
- `site:ask.metafilter.com/ "Can anyone recommend" "Ask MetaFilter"`

Search results returned substantial text for all ten posts, but a search result is not direct page inspection. Only records mf-001, mf-002, and mf-007 were individually opened and verified (parent re-opened all three URLs). Records mf-003 through mf-006 and mf-008 through mf-010 remain candidates with `evidence_kind=search_snippet` and `status=candidate`; their displayed text and metadata have not been independently confirmed against the pages. Numeric `source_item_id` values are the post IDs from the individual page URLs, not RSS item IDs. The task owner separately verified `https://rss.metafilter.com/ask.rss` returned HTTP 200 XML with four current items, and `https://ask.metafilter.com/robots.txt` returned HTTP 403. This pilot did not attempt to evade that restriction or fetch a bulk corpus.

Public availability and crawlability demonstrate access only. They do not establish permission to reproduce, reuse, or train on the text.

## Selection method

Selected distinct, individually addressable post URLs from search results, retaining only the displayed short title. For the three verified records, titles and metadata were checked against opened pages. For the seven candidates, the title wording and associated page metadata rely on search-result text alone and need page-level confirmation before inclusion in any verified corpus. No body or answer text is included. Popularity fields retain observed counts from returned evidence; missing metrics are omitted or null. No authorship claims are made. Public platform posts do not prove that a human wrote them; no reliable AI-authorship test was performed, and there were no obvious bot indicators in the excerpts.

## Limits and reuse

This is a discovery and provenance pilot, not a representative sample: search indexing favors discoverable posts, selection was not randomized, and only three of ten are verified by direct page inspection. It demonstrates that candidates can be discovered, but does not establish a lawful or operationally scalable way to collect 1,000 verified questions. The RSS response observed by the task owner had only four items; robots returned 403. Source terms, copyright status, licensing, and permission for dataset reuse were not established. A larger collection requires separate review of source terms and an allowed collection route. Accessibility must not be treated as a reuse license.
