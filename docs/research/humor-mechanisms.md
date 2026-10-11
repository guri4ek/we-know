# Humor mechanisms for We Know

Research date: 2026-10-11. Status: **editorial proposal**, not an approved mechanism taxonomy, implemented generator, or empirically confirmed formula for humor. Practical application: [offline authoring guide](../content/answer-generation-guide.md). The later audit translates and reconciles status; it does not replace research conclusions or regenerate examples.

## Product recommendation

Start with five mechanisms: `bureaucratic_absurdity`, `false_causality`, `reversal`, `metaphor`, `hyperbole`. The first three editorial experiment priorities are bureaucratic procedure applied to an impossible object, switching who controls a situation, and transferring a familiar rule into an incompatible domain. This is our choice based on product voice, **not an audience-research ranking**. Keep `false_authority` auxiliary; do not yet make `oxymoron`/`paradox` separate production families.

Working heuristic: **expected answer form → one understandable logical shift → calm final verdict**. Arrows represent editorial actions, not a causal law or arithmetic humor function. Brevity, confidence, or a random strange noun alone do not make a joke.

Jev classifies intent/topic. In the target architecture PHP selects prepared text uniformly from the first eligible pool. This research concerns **offline library preparation with human editing**. Live slot substitution, mechanism selection by Jev, ranking, and mechanism-dependent weights are outside its output. At research start the documentation recorded a skeleton; selector/database were targets in the [plan](../technical-plan.md). Stage-1 classification was subsequently implemented; selector/persistence remain future work. This research does not establish business implementation.

## Claim provenance

| Layer | What belongs to it | How to use it |
| --- | --- | --- |
| Source chat | Serhii's request for practical formulas; earlier assistant claims about theories, eight techniques, runtime templates | Concept history, not scientific evidence or active instructions |
| Serhii's decisions | Contextual only; prepared answers; metadata on eligibility; selector ignores mechanism | Research boundaries; supersede old schemes |
| Checked primary material | Author's GTVH account, JAPE, West/Horvitz, McGraw/Warren papers | Conclusions within their genres/experiments only |
| Author's practical conclusions | Codes, priorities, slots, rubric, question corpus, examples | Proposals for editorial evaluation |
| Open hypotheses | Humor, portability to unfamiliar questions, repetition effects | Do not call proven before separate evaluation |

Concept source: [public conversation](https://chatgpt.com/share/6acb479e-9110-83eb-8773-c0a199dd4297). In the research session, orchestrator-prepared visible user/assistant final extracts in `/private/tmp/we-know-humor-source.md` were read in full: messages 77, 96, 98, 116, marked as retrieved over public HTTP on 2026-10-11. The browser was not reopened. That temporary file is not a guide dependency and was not copied into the repository; raw transcript/HTML is not published.

## What primary sources support

### GTVH: a language for describing jokes

Attardo and Raskin published *Script theory revis(it)ed: joke similarity and joke representation model*, HUMOR 4(3–4), 1991, DOI `10.1515/humr.1991.4.3-4.293`. The publisher's 2009 date is online publication, not the theory's origin. Bibliography was confirmed through the indexed [publisher page](https://www.degruyterbrill.com/de/document/doi/10.1515/humr.1991.4.3-4.293/html); direct opening failed, and the full 1991 publisher text was not read.

The accessible abstract of Attardo's 2020 chapter lists six Knowledge Resources: Script Opposition — conflicting interpretations; Logical Mechanism — connection/resolution of incongruity; Situation — circumstances; Target — object of ridicule; Narrative Strategy — text organization; Language — specific wording. It describes a joke-based model of humor competence; the full chapter is closed. This is author support for the six components, not verification of all original-paper arguments. [Attardo, The General Theory of Verbal Humor, 2020](https://academic.oup.com/book/41037/chapter/349334548).

**Our adaptation:** privately articulate ordinary expectation, shift, and satire target before compressing the text. Normally a procedure ends waiting; in our phrase the procedure itself must wait. The target is the overconfident system. Our `humor_mechanism` codes are convenient content labels, not a complete GTVH Logical Mechanisms ontology or six mandatory database columns.

### JAPE: controlled structure is possible; quality is not automatic

Binsted and Ritchie, *An Implemented Model of Punning Riddles*, AAAI-94: JAPE-1 builds question–answer punning riddles from lexical entries; schemata define relationships, templates define linguistic form, and generated text is checked. The evaluation section explicitly limits conclusions: 188 outputs, 14 judges, no control group of jokes; evaluation was not statistically rigorous. Recognizable joke structure did not imply high quality. [Author's paper copy, Post-production checking and The evaluation procedure](https://homepages.abdn.ac.uk/g.ritchie/pages/papers/aaai.pdf); [author's AAAI preprint](https://arxiv.org/abs/cmp-lg/9406022).

**Our adaptation:** separate concept, slot vocabulary, final text, and screening. Do not adopt puns as the main voice: JAPE controls both question and answer, while visitors write our questions. Dependence on an exact word, homonym, or pronunciation conflicts with broad pool eligibility. Do not copy heard/published research jokes into the catalog.

### Reverse-Engineering Satire: meaningful shifts in short form

West and Horvitz, AAAI 2019, analyze The Onion satirical headlines and closely related serious edits from Unfun.me. They find a tendency for the key humorous fragment to appear near the end, often in a noun phrase. Their false analogy transfers an inference between entities sharing a property despite a crucial difference making the transfer invalid; this is more precise than combining arbitrary incompatible words. Findings concern selected headlines and editing methods, not all humor genres. [Paper and DOI](https://ojs.aaai.org/index.php/AAAI/article/view/4712), [PDF, §§2–5, 7](https://ojs.aaai.org/index.php/AAAI/article/view/4712/4590).

**Our adaptation:** preserve a recognizable answer frame and make one transferred property/rule the source of absurdity. Compare two short edits with an earlier/later turn; do not require the final word to carry it. Editorial diagnostic: replace the absurd component with an ordinary one. If the logical change cannot be described, the candidate may be random words. This diagnoses structure, not proven humor.

### Benign Violation Theory: perceived violation depends on the reader

McGraw and Warren, *Benign Violations: Making Immoral Behavior Funny*, Psychological Science 21(8), 2010, DOI `10.1177/0956797610376073`: five experiments studied moral violations. Results support simultaneous perception of violation and acceptability; studied factors include an alternative norm, weaker commitment to the violated norm, and psychological distance. Amusement and disgust could coexist. Laughter therefore does not mean the reader felt nothing unpleasant. [Author's copy, abstract and Discussion](https://leeds-faculty.colorado.edu/mcgrawp/pdf/mcgraw.warren.2010.pdf). The author explains the theory and origins in [his 2010 introduction](https://petermcgraw.org/a-brief-introduction-to-the-benign-violation-theory-of-humor/).

**Our adaptation:** target fictional certainty, procedure, or a system rather than the questioner's vulnerability. An obviously fictional institution does not guarantee suitability for a personal crisis. Headlines/moral scenarios from these studies are not evaluation of our pools; full pre-release content policy remains open in the [ADRs](../architecture/decisions.md).

## Refining source-chat claims

| Initial claim | Research result / We Know decision |
| --- | --- |
| GTVH provides a “humor formula” | It provides a theoretical model; our short sequence is an author heuristic |
| JAPE demonstrates rule-based humor | Yes, in a narrow riddle genre; no broad quality/human-equivalence claim is transferred |
| False analogy and final turns help satire | Supported in headline analysis; transfer to user answers is a hypothesis |
| Strange but harmless is sufficient for humor | BVT describes perception; arbitrary oddness and our “harmless” judgment do not guarantee reader response |
| Jev/random can choose a mechanism before answering | Earlier runtime proposal is not adopted: prepare labels offline; selector ignores them |
| 15–20 techniques/combinatorial templates guarantee a rich library | Unsupported. Begin with five families; changing department names is not a new joke |

## Practical set and eligibility limits

These are stable **proposed authoring codes**, preserving spellings from the [data model](../architecture/data.md); the final mechanism enum is unapproved. Intent/topic was proposed when research was written and is now frozen [taxonomy v1](../content/taxonomy-v1.md). Pairs below are writing directions, not automatic eligibility.

| Code / status | Operation | Most promising intent/topic | Weak case | Repetition risk |
| --- | --- | --- | --- | --- |
| `bureaucratic_absurdity` / primary | Give an abstraction/impossible event an ordinary procedural obligation | `WHEN`, `WHY`, `HOW`, `WHO`; `WORK`, `TECH`, `GENERAL` | Mere “approved” without logical change; long institution title | High: repeated approvals, forms, committees |
| `false_causality` / primary | Present a weak/impossible connection as a sufficient cause/condition | `WHY`, `WHEN`; `GENERAL`, `LIFE`, `WORK` | Random planet/animal without a legible connection | High: “because X” with another noun is the same move |
| `reversal` / primary | Switch agent/object, controller/controlled, or ordinary order | `WHO`, `HOW`, `WHY`, `WHAT`; `WORK`, `TECH`, `LIFE` | Roles unclear without the exact situation | Medium: everything starts “hiring” the person |
| `metaphor` / primary | Transfer a familiar rule across domains through a recognizable shared property | `WHAT`, `WHY`, `HOW`; `TECH`, `WORK`, `MONEY`, `LOVE`, `LIFE` | Pretty comparison without false inference; narrow metaphor in broad pool | Medium: everything becomes a subscription, queue, spreadsheet |
| `hyperbole` / primary | Supply absurd scale, precision, or measurement unit | `HOW_MANY`, `WHEN`, `HOW`; `GENERAL`, `WORK`, `TECH` | Realistic number sounds advisory; amount incompatible with question unit | High: another large number is not another mechanism |
| `false_authority` / auxiliary | Replace reasoning with a fictional certificate/verdict | `YES_NO`, `WHO`, `OTHER`; `GENERAL`, `WORK` | “Council” name alone; imitation of a real regulator | Very high: decorative references to similar institutions |
| `oxymoron` / reserve | Compress contradiction into a phrase | Possible emphasis in `WHAT`, `WHEN` | Label does not answer the question; worn cliché | High; editing technique for now, not separate generation stream |
| `paradox` / reserve | Self-undermining/circular statement | Possible `WHY`, `HOW`, `OTHER` | Requires explanation/rereading | High; do not use it to label any illogical text |

Our `metaphor` includes the practical false-analogy technique; do not add alias `false_analogy`. This is editorial grouping, not scientific terminology equivalence. Do not combine turns just for quantity. Deadpan is shared voice, not a ninth mechanism.

## Portability matters more than one good example

Labels are coarse: `TECH` does not mean programmers only; `LOVE` does not establish that the user has a partner; `HOW_MANY` includes different units. An author seeing the exact question can easily write a phrase later selected for another subject. Evaluate **question × answer pairs within the eligible pool**, not isolated one-liners.

`GENERAL` also receives specialized-topic fallback, so it cannot be evaluated only on abstract “life.” `OTHER` is unknown form after successful classification; `ANY` is editorial universal tier. A phrase depending on the exact name, verb, or unit does not become broadly eligible by labeling it. A non-executable note “except these questions” cannot repair failure within an intent/topic pair.

One primary `ANSWER_ELIGIBILITY.humor_mechanism` per eligibility is proposed. If both bureaucracy/metaphor appear, choose the main turn and retain secondary explanation in draft notes. Do not duplicate answers/eligibilities for each label. Preserve unique `(answer_id, intent, topic)`; the mechanism does not influence ranking/random. More similar phrases in a pool already increases their frequency without weights, so reject lexical clones.

## How to evaluate hypotheses next

**Proposed small experiment, not conducted:** two candidates and three different questions for each of nine intents; manually check all six combinations plus universal tier and negative probes. The [smoke corpus and rubric](../content/answer-generation-guide.md#smoke-corpus) are in the guide. This covers form/portability, not statistical humor validation or a sufficient production catalog.

For the next editorial evaluation, freeze questions and screening criteria first; keep some questions unseen by authors. Separate fit, clarity, and humor scores. If possible, independent English readers rate shuffled pairs without mechanism labels, then short answer sequences for fatigue. Retain disagreements rather than concealing them in an average. Codex self-rating does not replace human readers.

Open questions: tone outside TECH/WORK; adequacy of coarse `CHOICE` pools and `HOW_MANY` units; bureaucracy fatigue; humor without website presentation; HEALTH/personal-crisis fit. Do not silently approve new labels/runtime filters to “repair” a failed corpus. The classification ambiguity rules are now specified in v1; their semantic success and answer portability still need evaluation.

Future likes are not quality: counts depend on exposure, question distribution, fallback tier, and voter self-selection. Even likes/exposures does not remove selection bias. If later authorized, compare similar exposure/context, show uncertainty, and separate repeated impressions. This is methodological caution, not a requirement for new tables, telemetry, routes, or selector weights now.

## Result limits

Primary web material was checked on 2026-10-11 through web tools. GTVH bibliography/author abstract were available; full publisher texts from 1991/2020 were not. JAPE, AAAI 2019, McGraw/Warren 2010 were accessible as PDFs. These are short summaries without borrowed examples. All practical guide phrases were written for that research; exhaustive overlap with published humor was not checked.

The humor research did not run audience tests, live Jev, paid model calls, database changes, or runtime implementation. Subsequent stage-1 live classification is separate evidence, not humor evaluation. The documents support writing/screening content and do not promise empirically confirmed humor.

## Historical local artifact checks

On 2026-10-11 the research session performed read-only validation of the root README and all `docs/` Markdown (12 files): 13 fenced blocks closed; 5 fenced JSON and 1 inline JSON parsed; 52 local Markdown links, including an existing fragment target, resolved. Draft metadata checked allowed intent/topic/codes, one mechanism per eligibility, no duplicate pairs, `locale: en`, human scores `null`.

Structural smoke checks confirmed 9 intents, 27 questions, 18 main candidates: 54 main and 54 universal trial combinations. This is matrix composition, **not 108 successful fit/humor ratings**. Negative probes/unresolved editorial eligibility were retained. Main-matrix examples fit the proposed 6–24-word budget.

Comparison with then-current data/selector documents found no change to accepted rules: mechanism on eligibility, first nonempty pool, uniform random, no new weights/revision/schema. Research changes were limited to two new documents and README/context navigation/provenance additions. Markdown whitespace checked; no PHP suite, formatters, live API, or migrations ran in that research session. Current audit checks are separately recorded in [verification](../verification.md#documentation-audit-verification-event).
