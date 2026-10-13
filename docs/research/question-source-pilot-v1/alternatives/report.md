# Alternative Q&A source pilot

Retrieved 2026-10-11. This is a bounded access and source-quality pilot for potential human-authored question research. It does not assess site popularity and does not establish that every post on either platform was written by a human. Reddit was excluded. See [`questions.jsonl`](questions.jsonl) for the evidence-backed records.

## Source comparison

| Source | Observed access | Pilot evidence | Initial source-quality observations |
| --- | --- | --- | --- |
| Fluther | Search indexing exposed question lists and direct question pages, with question body, topic tags, section, answer counts and (on some pages) a dated metadata field. The official help page distinguishes General (knowledge/thoughtful discussion) from Social (open-ended opinions/polls) and states that content should be genuine and well-written. Parent-run read-only GET of the homepage returned HTTP 200 and showed the official RSS path `/feeds/general`; this agent's shell request could not resolve the host, and the web reader returned cache misses for several direct pages. | Two verified records: one curiosity/factual question and one practical how-to question. | Useful visible structure and some explicit curation rules. The feed path suggests a low-friction way to sample current General entries, but the feed's fields, retention, pagination, and whether it covers Social were not tested here. Site rules are not proof that all user posts meet them. |
| Answerbag | The indexed `/questions` listing exposed a large question list and links to individual `q_view` pages. One individual page was retrievable and showed two answers and visible site terms/privacy links, but the extractor omitted that question's heading. | One candidate retained from the list snippet; not treated as verified page evidence. | The listing is broad but dates appeared as “some time ago” and the inspected detail page did not surface the title in its extracted body. The limited sample also raised obvious relevance/moderation concerns in adjacent related content; this is not a representative quality estimate. |
| GirlsAskGuys | Not sampled in this pilot. A sitemap/listing was previously identified as a possible discovery surface, but no individual question page was inspected here. | None. | No access or quality conclusion. |

## Search queries and selection

Web search queries used exactly:

- `site:fluther.com "What" Fluther question`
- `site:fluther.com "How" Fluther questions`
- `site:answerbag.com/questions "why" question`
- `site:girlsaskguys.com "Why" question`

I selected distinct, ordinary English questions and opened linked individual pages where possible. Both verified titles use the same "How does" construction, so this small set does not demonstrate answer-form diversity. The two verified records came from Fluther's indexed question list and direct-page links. The Answerbag title is kept only as a candidate because the listing exposed its exact wording but the detail-page extraction did not show the heading. No question was generated, paraphrased, or reconstructed from a slug. No bulk feed/list text was copied. The set is intentionally small and not a random or representative sample.

Parent verification on 2026-10-11 reopened both Fluther pages and confirmed their titles, section, recorded counts, and the explicit date on alt-002. The web reader served indexed/cached content. Retrieval date is not the date those engagement counts were measured; alt-001's relative age must not be converted into a publication date using today's date.

## Scaling and limitations

Fluther looks like the strongest of the inspected options for a larger controlled pilot because the pages expose answer counts, tags, section labels and some dates, and an official General RSS endpoint was observed by the parent agent. Whether that endpoint can supply 1,000 distinct questions was not established: no feed payload, pagination behavior, historical coverage, deduplication rate or stability check was performed. Indexed listings and page links show discoverability, not a reliable bulk-export interface. Answerbag's large index suggests discovery at scale may be possible, but page completeness and usable dates need validation. No conclusion can be drawn about GirlsAskGuys.

This pilot did not assess language detection, duplicate rates, spam proportions, respondent quality, taxonomy coverage, accessibility stability over time, or current moderation effectiveness. Response counts and Fluther “Great Question” marks are recorded only as observed site metrics; neither is a universal quality label. Dates are nullable where the page exposed only relative or vague time.

## Reuse and terms

Public accessibility does not establish permission to reuse question text in a prepared-answer corpus, redistribute it, or collect it at scale. Fluther pages link to Terms & Conditions and guidelines; Answerbag pages link to Terms of Service and Privacy Policy. This pilot did not review the full terms or determine copyright, database, or privacy implications. Any production ingestion or reuse decision needs a separate terms and rights review, as well as a source-specific collection policy. The records preserve source URLs and evidence so that later review can distinguish verified page text from snippet-only candidates.
