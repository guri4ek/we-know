# Proposed Intent / Topic taxonomy

2026-10-12 — corpus-based editorial proposal; not an accepted taxonomy or a classifier evaluation.

**Recommend 14 intents and 16 topics, replacing the provisional 9 × 7 baseline.** The useful changes are:

- Split `WHAT` into identification, description/meaning, location, and recommendation; add comparison. These requests need different answer forms.
- Separate duration from moment/date. Broaden `WHY` to explanations of mechanisms and purposes; reserve `HOW` for actions and methods. Rename `HOW_MANY` to `QUANTITY`.
- Broaden `LOVE` to relationships and parenting, and `WORK` to work/study. Give food, home, nature, travel, sports, entertainment, language, ideas, and society their own subjects instead of burying them in `GENERAL`/`LIFE` or stretching `TECH`.
- Recover substantive requests even without interrogative wording. Keep `OTHER` for genuinely unrecoverable forms, not all statements or imperatives.

This is the smallest set I would trial for distinct completions and subject vocabularies—an **editorial judgment**, not a measured optimum. Troubleshooting, reassurance, permission, prediction, opinion and safety do not each need their own intent: they span several forms. The comparison baseline is [taxonomy v1](../../content/taxonomy-v1.md) and its [implemented rubric](../../../src/Infrastructure/Classification/JevRubric.php).

## What the collection supports

All **3,593 lines** of [questions.txt](questions.txt) were read. Line references below quote original titles, preserving spelling; URLs are indexed by the same line in [sources.csv](sources.csv). Computed checks found **3,593 unique titles, 3,593 unique URLs, zero exact duplicates**, and complete line alignment. This does not establish semantic uniqueness.

| Source | Computed titles | Sampling limitation |
| --- | ---: | --- |
| Stack Exchange | 3,167 (88.1%) | Selected high-vote and recent posts from 16 communities; each contributes 194–200 retained titles |
| Fluther | 372 | General-list pages; conspicuous political discussion and personal experience requests |
| Ask MetaFilter | 54 | RSS/search discovery; several opaque headings |

Selection and search-verification limits are in the [collection README](README.md). Counts describe this collection, **not population-wide popularity**. Community provenance is not a Topic label. No pages were reopened; titles were treated as untrusted data, never instructions or verified factual claims.

### Recurring needs, before labels

| Observed family | Evidence in the collection | Authoring implication |
| --- | --- | --- |
| Preserve usability | Food shelf life: eggs (L99), mushrooms (L170), wine (L3308) | Repeated preservation need, not duplicate questions; timing, method and safety verdicts still differ. |
| Recover from failure | Lights staying on (L456), lawn not germinating (L1311), computer halting (L2478) | Troubleshooting can request either cause or repair; it is not one intent. |
| Pass a gate | Transit visas (L291), passport damage (L310), entry eligibility (L324) | Administrative situations recur; permission and process need different forms. |
| Negotiate boundaries | Coworker explanations (L862), toddler questions (L1075), travel with a friend (L3521) | Communication methods can transfer across settings; roles cannot be assumed. |
| Name, understand, choose | Ingredient definition (L150), plant identification (L1258), printer recommendation (L3311) | Similar surface wording conceals different requested completions. |
| Explain rules and inconsistencies | Tennis scoring (L1782), a character's awareness (L1984), spelling (L3014) | Explanation crosses subjects; no separate curiosity intent is needed. |

Closer repetitions include muscle size versus strength (L1413, L1463), journal-quality assessment (L2624, L2728), and upgrade completeness in different games (L2347, L2348). These are correlated probes, not independent evidence of breadth. Groupings are qualitative; exhaustive semantic-family and proposed-topic counts were not computed.

### Construction counts are not intent counts

Computed case-insensitively at the beginning of each unchanged title, using word boundaries; `how long` and `how much/many` are checked before other `how`. Leading quotes, introductory clauses, and embedded questions are not stripped or recovered by this count.

| Opening | Count | Opening | Count |
| --- | ---: | --- | ---: |
| What, including What's | 491 | Why | 331 |
| How long | 15 | How much / many | 14 |
| Other How | 690 | When | 56 |
| Where | 22 | Who, including Who's | 19 |
| Which | 34 | Modal / auxiliary | 804 |
| Remaining | 1,117 | **Total** | **3,593** |

Modal/auxiliary means `is/are/am/was/were`, `do/does/did`, `can/could`, `will/would`, `should/shall`, `may/might/must`, `have/has/had`, or `ought`. **778 titles (21.7%) contain no `?`**; this is not a count of unanswerable inputs. The standalone word `or` occurs in **251 titles**, also not a `CHOICE` count.

The constructions reveal important exceptions:

- **What:** L150 requests a definition; L1258 an identification; L905 a career recommendation; L974 an action. The word itself cannot determine the output form.
- **Why / How:** L3 asks for a reason; L2431 asks how a computer restarts itself—an explanatory mechanism, not instructions to restart it. Conversely, L1 asks for a method. “How dangerous” (L31) requests an assessment, not a procedure.
- **When:** L915 requests disclosure timing. L2975 asks when to use punctuation—usage conditions, usually `HOW`, not a calendar answer. Context clauses beginning “When” (L78) do not determine intent.
- **How long:** of the 15 matching openings, **13 concern time and two concern hair length** (L3198, L3273), by manual inspection. Route the latter to `QUANTITY`. Duration also appears without this opening (L159).
- **How much/many:** counts, prices, mass, and magnitude share an amount form, but not interchangeable units. L3560 asks for money despite mentioning “twice a month”; L1480 asks for hours of sleep despite using “how many”.
- **Where / Who / Which:** L2145 needs a filming location; L1926 a person; L356 a town's identity. “Which” can also request a recommendation (L123) or explicit selection (L3025). Metaphorical “where” (L662) seeks an explanation of a scam, not a place.
- **Should / Can / Is / Do:** L68 is a genuine yes/no decision. L3397: “Can anyone recommend a weighted blanket that isn't too hot?” is a recommendation request; L608: “Can someone explain a stock's "bid" vs. "ask" price relative to "current" price?” asks for an explanation/comparison. L9 selects between cooking sequences. L2980 seeks a word, not merely confirmation that one exists.
- **Alternatives:** L618 asks to select a financing approach; L1396 asks for trade-offs; L9 selects an order of actions. Mentioning two things or using “vs” is insufficient to choose between `CHOICE` and `COMPARE`.
- **No interrogative wording:** L42 clearly requests a method; L2079 identification; L3215 recommendations. L2407 is an actionable method request. Bare symptom reports such as L524 leave cause-versus-repair ambiguous; bare “Discord” (L3557) does not establish either the intended sense or a requested outcome.

## Recommended intents

Proposed enum codes and editorial readings, not gold labels or Jev predictions. Classify the requested completion, not its opening word.

| Code and definition | Original examples | Nearest boundary | Change from v1 |
| --- | --- | --- | --- |
| `YES_NO` — confirmation, permission, feasibility, binary judgment or forecast | L68: “Should I refrigerate eggs?”; L324: “Will I be able to enter Latvia without a visa?” | An indirect request for a name, method, or recommendation follows that request. Choosing between substantive alternatives uses `CHOICE`; yes/no itself does not. | Keep; tighten indirect-request rule |
| `WHEN` — moment, date, event stage, or timing condition | L915: “When should I disclose an upcoming vacation that overlaps with the start date of an internal job position?”; L1922: “When exactly does the game clock start during an American football kickoff?” | Elapsed/waiting time → `DURATION`; applicability conditions without timing → `HOW` or `DESCRIBE`. | Narrow |
| `DURATION` — elapsed time, waiting time, lifespan, or temporal interval | L1231: “How long does it take for the compost to ripen?”; L1422: “How long should I rest in between sets?” | Clock/date → `WHEN`; physical length → `QUANTITY`. | Split from `WHEN` |
| `WHY` — cause, reason, purpose, or explanatory mechanism | L3: “Why add salt to the water when cooking pasta?”; L2431: “How does a computer restart itself?” | Instructions for achieving something → `HOW`; naming/meaning → `IDENTIFY`/`DESCRIBE`. “How it works” can need an explanatory clause without literally starting “because”. | Keep and broaden beyond why-wording |
| `HOW` — method, procedure, practical strategy, or way to act | L1: “How can I chop onions without crying?”; L3521: “How do I tell a good friend I don't want to travel with her?” | A tool/product to choose → `RECOMMEND`; a cause to understand → `WHY`. | Keep; remove explanatory mechanisms |
| `WHO` — person, role, group, or responsible agent | L1859: “Who keeps the record of each international cricket match?”; L1926: “Who is this guy at the back of the grid with a green flag?” | A term for a kind of person → `IDENTIFY`; recommended person/service → `RECOMMEND`; explicit candidate selection → `CHOICE`. | Keep |
| `IDENTIFY` — name or identity of an existing thing, word, work, species, or referent | L1294: “What is this nut bearing tree in Southern Ontario?”; L3541: “Book Identification: Chapter book about girls experiencing puberty” | Explain a known term → `DESCRIBE`; suggest a suitable new candidate → `RECOMMEND`; people → `WHO`; locate something → `WHERE`. | Split from `WHAT` |
| `DESCRIBE` — definition, meaning, properties, state, outcome, or qualitative assessment | L150: “What is celery salt?”; L607: “What happens to people without any retirement savings?” | Cause → `WHY`; a name alone → `IDENTIFY`; quantity → `QUANTITY`; comparative account → `COMPARE`. Includes open reactions/experience accounts, but remains a broad residual form. | Replace the descriptive part of `WHAT` |
| `WHERE` — physical or virtual location, destination, or source location | L2145: “Where was the Mummers Parade scene in It's Always Sunny in Philadelphia filmed?”; L2314: “Where is the anvil in the Shattered Keep Cellars?” | Recommend a destination/provider → `RECOMMEND`; identify an unnamed place → `IDENTIFY`; explain metaphorical “where” → `WHY`. | Split from `WHAT` |
| `QUANTITY` — count, amount, price, size, frequency, probability, or degree on a scale | L535: “How many clamps is needed for a frameless shower glass?”; L3198: “How long does my hair need to be to keep it off my face, and neck?” | Time interval → `DURATION`; qualitative judgment without a meaningful scale → `DESCRIBE`/`YES_NO`. | Replace `HOW_MANY`; clarify magnitude |
| `CHOICE` — select among supplied alternatives, including an ordering decision | L365: “Should I bring Speedos or Swimming Shorts to Egypt?”; L685: “Pay online: credit card or debit card?” | Differences/trade-offs without a requested winner → `COMPARE`; no supplied alternatives → `RECOMMEND`. | Keep; clarify alternatives versus comparison |
| `RECOMMEND` — propose suitable items, resources, people, or options without a supplied candidate set | L3311: “Recommend a printer?”; L3397: “Can anyone recommend a weighted blanket that isn't too hot?” | Recognize an existing referent → `IDENTIFY`; explain how to act → `HOW`; locate a specified item → `WHERE`. | Split from `WHAT` and indirect yes/no readings |
| `COMPARE` — explain differences, similarities, relative properties, or trade-offs | L28: “What is the difference between white and brown eggs?”; L1396: “What are the trade-offs of weight versus repetition?” | Name the better/appropriate option → `CHOICE`; verify a single equivalence claim → `YES_NO`. | Add; draws from `WHAT`/`HOW`, avoids overusing `CHOICE` |
| `OTHER` — no recoverable requested completion | L3557: “Discord”; L3593: “It's time” | Missing punctuation is insufficient. Recover a clear substantive request, including imperatives; preserve uncertainty where title context is missing. | Keep, substantially narrow |

`DESCRIBE` remains a pressure point: definitions, consequences and opinions need separate authoring probes. Split it further only if useful category-wide texts prove unattainable. Frequency/probability likewise remain editorial groups within `QUANTITY`; defer `DEFINE`, `PREDICT`, `OPINION`, `EXPERIENCE` and `FREQUENCY` enums until their authoring value is established.

## Recommended topics

These describe the **requested subject**, never the question's source community or grammatical form. “Add” means carving a subject out of current `GENERAL`/`LIFE` and, where necessary, an overextended `TECH`; it is not a claim that v1 was run on these titles.

| Code and concise scope | Original examples | Nearest boundary / reason for a distinct pool | Change |
| --- | --- | --- | --- |
| `TECH` — computing, software, digital devices and their operation | L2377: “Why does Windows think that my wireless keyboard is a toaster?”; L3242: “How do I turn Zoom meeting information into a one-click Zoom link?” | Physical household systems → `HOME`; gameplay/story → `ENTERTAINMENT`; digital operation still stays TECH at work or in a game launcher. | Keep; clarify scope |
| `WORK_STUDY` — employment, education, teaching, research practice and institutional progress | L803: “How to convince HR to let my star employee work from home?”; L2680: “How can I stay on task and be productive when writing a thesis?” | Pay amount → `MONEY`; subject knowledge follows its domain; private relationships → `RELATIONSHIPS`. Shared deadlines, evaluation, credit and gatekeeping justify combining work and study. | Replace/expand `WORK` |
| `MONEY` — personal finance, transactions, prices, debt, investment, pay and tax obligations | L709: “How do I budget effectively when income arrives at irregular intervals rather than a fixed monthly salary?”; L3560: “How much should I put into a joint account twice a month?” | Career development → `WORK_STUDY`; public policy rather than personal finances → `SOCIETY`. | Keep |
| `RELATIONSHIPS` — romance, friendship, family, parenting and interpersonal boundaries | L1075: “How do you respond to a toddler who endlessly says "why?"?”; L3266: “Boyfriend can’t stay away from his ex-girlfriend's kid. Is this normal?” | Institutional power/work conflict → `WORK_STUDY`; human symptoms/treatment → `HEALTH`. Parenting stays an editorial group rather than a separate enum. | Replace `LOVE`; take family/friends from `LIFE` |
| `HEALTH` — human physical/mental wellbeing, sleep, nutrition, fitness and recovery | L1406: “How can I make myself get enough sleep?”; L1535: “Why has my weight loss plateaued despite a consistent calorie deficit?” | Sport rules/technique → `SPORTS`; food preparation/storage → `FOOD`; nonclinical habits → `LIFE`. | Keep; make fitness explicit |
| `LIFE` — personal habits, self-management, appearance and everyday experience outside specialized subjects | L3277: “How do I get over feelings of meaninglessness?”; L3565: “I regret not seeing the total eclipse, how do I get over it?” | Abstract meaning of existence → `IDEAS`; distress as a clinical concern → `HEALTH`; relationship conflict → `RELATIONSHIPS`. | Keep, narrow |
| `FOOD` — cooking, ingredients, drinks, kitchen technique and food storage/safety | L1: “How can I chop onions without crying?”; L3308: “How long does an opened bottle of red wine stay good?” | Human health effects/diet → `HEALTH`; repair of an appliance → `HOME`; cultivation → `NATURE`. Ingredient/process vocabulary does not fit general lifestyle answers. | Add |
| `HOME` — housing upkeep, building, repairs, utilities, cleaning and household equipment | L489: “How do I fix a "water hammer" problem?”; L544: “How can I fix a leak around the outer edge of a standing shower?” | Computing/network configuration → `TECH`; gardening/organisms → `NATURE`; mortgage finance → `MONEY`. Repair/material vocabulary differs from software operations. | Add |
| `NATURE` — plants, animals/pets, cultivation, ecology and the nonhuman natural environment | L1237: “How do you prune a rose bush?”; L3583: “What pets don't hate being pets?” | Hiking logistics → `TRAVEL`; food after harvest → `FOOD`; human exposure symptoms → `HEALTH`. Growth, care and ecosystems warrant their own vocabulary. | Add |
| `TRAVEL` — trips, transport, borders, accommodation, navigation and outdoor-trip logistics | L232: “I have two passports/nationalities. How do I use them when I travel?”; L1702: “How do I choose the right tent for different weather conditions?” | Sport technique → `SPORTS`; organisms encountered → `NATURE`; ordinary device operation → `TECH`. Camping remains a travel editorial group. | Add |
| `SPORTS` — sporting rules, competition, tactics, technique and performance records | L1782: “Points are given in tennis 15-30-40. Why 40?”; L1871: “How do you correctly learn how to hockey stop” | General exercise, body change and recovery → `HEALTH`; video-game rules → `ENTERTAINMENT`. Match/score/opponent framing needs this distinction. | Add |
| `ENTERTAINMENT` — films, TV, books, music, art, games and their fictional worlds | L1984: “Why isn't Bruce Willis's character in the Sixth Sense self-aware?”; L2212: “How does Skyrim determine if a dungeon is "cleared"?” | Actual software faults → `TECH`; wording as language → `LANGUAGE`. Fictional motives/game resources must not become real-life diagnoses or financial advice. | Add |
| `LANGUAGE` — words, grammar, usage, pronunciation, naming conventions and linguistic meaning | L3014: “Why is “cannot” spelled as one word?”; L3060: “What does "something 101" mean?” | General definition requests keep their subject; work conflict phrasing is `WORK_STUDY`, not automatically LANGUAGE. Words/sentences provide a distinct authoring vocabulary. | Add |
| `IDEAS` — abstract philosophy, logic, ethics, religion, knowledge and existence | L2783: “Why is there something instead of nothing?”; L2855: “What is the difference between Fact and Truth?” | Concrete public institutions → `SOCIETY`; lived personal purpose/habits → `LIFE`. Abstract truth/meaning differs from everyday self-management. | Add |
| `SOCIETY` — public affairs, government, civic institutions, law, collective norms and social policy | L3238: “Why do we have segregated public washrooms between men and women?”; L3487: “Does democracy work if majority of the population are uneducated?” | Personal financial obligations → `MONEY`; travel permissions → `TRAVEL`; abstract ethics → `IDEAS`. Civic power/rules differ from private relationships. | Add |
| `GENERAL` — subject outside the set, or insufficient subject evidence | L3191: “How do you feel about this?”; L3593: “It's time” | Do not use merely because a title is ambiguous in intent. Also remains the deliberately topic-neutral answer fallback. | Keep, reduce residual load |

Keep visas, scams, budgeting, bosses, PhDs, toddlers, sleep, weight loss, pets, camping gear, individual sports, film identification, games and grammar as **editorial groupings**. AI spans tools, employment, publication norms and philosophy; an AI topic would obscure the subject. Do not clone source communities: academia joins work/study, fitness joins health, and outdoors spans travel/nature/sports.

## Difficult boundaries and missing context

**Requested subject beats incidental setting.** Printer recommendations (L3311), removing finance software (L711), and identifying airport Wi-Fi (L256) concern TECH. Running cable through a wall (L466) concerns HOME installation. Purchasing does not automatically imply MONEY, and a Money-community source does not override software operation.

**Indirect request versus literal feasibility:** L448 seeks a removal method, while L6 asks whether an unusual cooking method is possible. L2098 asks whether a book exists; L2980 seeks a word. These need semantic review, not keyword precedence.

**Separate research context from runtime input.** L2217: “How do I deliver a baby?” and L2230: “How do I make money efficiently?” both come from Gaming according to the CSV. Provenance suggests game mechanics, but neither title establishes that for a classifier receiving only the question. Do not quietly label these confident ENTERTAINMENT examples or inject source-community context into the proposed runtime contract. Likewise, deictic titles depend on missing images/bodies: L304, L1294 and L3380 can expose the requested form while leaving the referent unavailable. L367, L1173, L2481, L3281 and L3547 need context for some or all of their interpretation; L306 is French. Record ambiguity rather than normalize, translate or rewrite the collection.

**Multiple outputs:** L459 asks identity, cause and action. Retain v1's first-recoverable-request convention for both dimensions; subordinate context is not a separate request. Apply the first-mentioned-topic tie-break only after excluding incidental setting. Loaded/rhetorical premises and current-event allegations are not factual authoring foundations.

## First answer batches and portability

Priority is an editorial judgment about reusable form and breadth, not a ranking by traffic or votes. “Template” below means an **offline writing structure for complete prepared texts**, with no runtime substitution.

| Priority | Family and proposed pools | Shared writing form / strongest probes |
| --- | --- | --- |
| 1 | Methods for boundaries, communication and institutional friction: `HOW/WORK_STUDY`, `HOW/RELATIONSHIPS` | Short action or sequence. Probe coworker, friend and parent situations (L862, L1075, L3521), then unrelated same-pool methods such as thesis productivity (L2680). |
| 1 | Causes and puzzling systems: `WHY/TECH`, `WHY/WORK_STUDY`; carefully tested `WHY/GENERAL` | Causal/mechanistic explanation. Probe faults, institutional behavior and purposes (L2377, L928, L273). A software-specific answer cannot inherit GENERAL eligibility. |
| 2 | Waiting and stages: `DURATION/GENERAL`, `WHEN/GENERAL` | Interval versus moment/trigger. Probe storage, composting, rest, disclosure and game-clock timing (L99, L1231, L1422, L915, L1922). The split prevents clock answers to duration requests. |
| 2 | Choosing and comparing: `CHOICE/GENERAL`, `COMPARE/GENERAL`, then small recommendation trials | Selection criterion versus explicit comparative relation versus suitable candidate/profile. Probe L365/L685, L28/L1396, L3311/L3397. A selection criterion alone may disappoint a request for a named recommendation. |
| 3 | Recognition and meaning: `IDENTIFY/NATURE`, `IDENTIFY/ENTERTAINMENT`, `DESCRIBE/LANGUAGE` | Naming versus definition/description. Probe trees, remembered works and word meanings (L1294, L3541, L3060). High contextual dependence makes these harder to reuse unchanged. |

Reusable form does not imply reusable content. `DESCRIBE` still mixes definitions, outcomes and reactions; `RECOMMEND/GENERAL` mixes tools, careers, destinations and people; `QUANTITY/GENERAL` mixes units; `YES_NO/GENERAL` mixes taste, legality, safety and forecasts. Broad HOME/NATURE/ENTERTAINMENT pools also span incompatible objects. Start with family-specific writing probes, then challenge each candidate with **other families in its actual eligible pair**. Editorial group names do not filter the runtime pool. If a candidate fails ordinary same-pool questions, hold it, rewrite it, or reconsider the taxonomy; do not silently assume finer routing.

The [current selector](../../../src/Domain/Answer/AnswerSelector.php) chooses uniformly from the first eligible pool: exact pair → intent/GENERAL → ANY/GENERAL. It cannot rescue a narrow phrase by matching a named product, ingredient, family role or story. Humor metadata does not rank candidates. Preserve that constraint: test every unchanged candidate against diverse positive and adversarial questions in its pair, including food safety, electrical faults, serious family situations and health/finance questions actually present here. An abstract tone alone does not establish fit or acceptability.

The proposed **224 possible pairs are not a request to fill 224 pools**. Establish broad form fallbacks and a few promising exact pools first. `ANY` remains editorial fallback eligibility, never a Jev intent; a provider failure remains distinct from successful OTHER/GENERAL. Whether 14 × 16 is worth its authoring/classification cost must be decided from those trials, not from apparent catalog completeness.

## Evidence status

New evidence: full-title reading, local counts and editorial analysis. Luna's counts were independently recalculated; quotations, line references, local links and protected-input hashes were checked. No semantic accuracy, answer-fit score or human humor assessment was measured. [Historical stage-1 measurements](../../evaluation/stage-1-results.md) do not validate this proposal. Adoption needs a separately versioned rubric and independently reviewed comparison. Only this report was written in the repository; no answer generation, implementation, database changes, Git operations or external API calls occurred.
