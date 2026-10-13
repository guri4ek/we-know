# Collected questions

Collected 2026-10-12: **3,593 original Q&A titles**, with no generated or rewritten questions.

- [questions.txt](questions.txt): one question or request title per line, no metadata.
- [sources.csv](sources.csv): the matching line number and original URL.

## How these were collected

One-session Python scripts read public APIs, lists and feeds. Two Luna agents handled Fluther and Ask MetaFilter. The scripts stayed in temporary storage; no collector was added to the application.

| Source | Kept | Selection |
| --- | ---: | --- |
| Stack Exchange | 3,167 | Requested 100 highest-voted and 100 newest questions from each of 16 communities |
| Fluther | 372 | Titles from 11 pages of the General question list |
| Ask MetaFilter | 54 | Current RSS and complete titles found in search results; archive access was unavailable |

Stack Exchange communities: cooking, travel, home improvement, personal finance, workplace, parenting, gardening, fitness, outdoors, sports, movies, gaming, Super User, academia, philosophy, and English usage. API sorts were `votes` and `creation`, descending. [API](https://api.stackexchange.com/docs/questions) · [source licensing](https://stackoverflow.com/help/licensing).

Only HTML decoding and whitespace cleanup were applied. Exact duplicates, titles longer than 25 words, and titles ending in an ellipsis were omitted. Original spelling is retained. Some Q&A titles are requests or short headings rather than complete interrogative sentences. This is a useful starting collection, not a global popularity ranking; search-derived titles were not individually rechecked.

Reddit was excluded. No taxonomy labels, answers, paid API calls, or database imports were added. Questions and source rows were checked for matching counts, uniqueness and valid URL structure.
