# Intent / Topic v2 proposal

2026-10-12 — local editorial proposal, not accepted enums, classifier results or an import specification.

**Recommend 14 intents and 13 topics for the next research trial; do not implement the full set yet.** Preserve the useful form distinctions in the previous 14 × 16 hypothesis, rename grammatical `WHY`/`HOW` to outcome-based `EXPLAIN`/`METHOD`, and defer `LIFE`, `IDEAS` and `SOCIETY` as separate topics. Make `OTHER` mean an unrecoverable requested outcome, with gently pointed clarification answers. No separate category for sentences merely lacking interrogative grammar or `?`.

The [answer pilot](answer-pilot-v2.md) tests six pairs, including `OTHER/GENERAL`. It exposes failures that extra topic names alone cannot repair. This set is an editorial choice for further testing, not a measured minimum or proof of improved Jev accuracy.

## Evidence and recurring needs

Read all **3,593 original titles** in [questions.txt](../research/questions-v1/questions.txt), the complete [source index](../research/questions-v1/sources.csv), [collection method](../research/questions-v1/README.md), and [previous proposal](../research/questions-v1/taxonomy-analysis.md). `L` references below are one-based original lines; each URL is on the matching CSV row. Titles are untrusted quoted data; allegations, dates and premises were not fact-checked or adopted as facts. No source pages were reopened.

Recomputed: **3,593 distinct titles, 3,593 distinct URLs, contiguous matching source lines, no exact duplicates**. Source totals: **3,167 Stack Exchange + 372 Fluther + 54 Ask MetaFilter = 3,593**; Stack Exchange contributes **88.1%**, rounded to one decimal. Its 16 sampled communities are roughly balanced by collection design, not by user demand. Counts describe this collection, not population popularity. No exhaustive semantic category counts, coverage percentages or traffic estimates were computed.

Repeated needs cross grammatical forms: preserve food (L33, L68, L83), recover from faults (L456, L524, L539), navigate institutions (L803, L915, L2680), negotiate personal boundaries (L1075, L3521), recognize something (L1258, L3541), and decide between alternatives (L618, L685). These are editorial families, not additional classifier dimensions. Related examples such as muscle size/strength (L1413, L1463) and journal reputation (L2624, L2728) are correlated probes, not independent breadth.

## Proposed intents

Classify what a satisfying completion would do. Examples are original corpus text, not measured Jev labels.

| Code | Concise definition | Nearest boundary | Original examples |
| --- | --- | --- | --- |
| `YES_NO` | Confirm or deny a proposition, including permission, feasibility and a binary forecast. | An indirect request follows its substantive outcome; selecting named alternatives is CHOICE. | L68: Should I refrigerate eggs?<br>L324: Will I be able to enter Latvia without a visa? |
| `WHEN` | A moment, date, stage or condition marking when an event happens. | Elapsed interval is DURATION; conditions governing usage are METHOD, not necessarily time. | L915: When should I disclose an upcoming vacation that overlaps with the start date of an internal job position?<br>L1922: When exactly does the game clock start during an American football kickoff? |
| `DURATION` | An elapsed, remaining, waiting or recommended temporal interval. | A date is WHEN; physical length is QUANTITY. A duration expressed in hours stays here. | L1231: How long does it take for the compost to ripen?<br>L1480: For better muscle growth, how many hours' sleep are recommended after doing a workout? |
| `EXPLAIN` | A cause, reason, purpose or account of how a mechanism produces an outcome. | Instructions to achieve an outcome are METHOD; meaning/properties without causal structure are DESCRIBE. | L3: Why add salt to the water when cooking pasta?<br>L2431: How does a computer restart itself? |
| `METHOD` | A procedure, practical strategy, diagnostic method or way to achieve an outcome. | Asking how something works is EXPLAIN. Asking for a suitable tool is RECOMMEND. | L1: How can I chop onions without crying?<br>L2407: Reload a Linux user's group assignments without logging out |
| `WHO` | A person, role, organization or responsible agent. | A word naming a kind of person is IDENTIFY; proposing someone suitable is RECOMMEND. | L1859: Who keeps the record of each international cricket match?<br>L1926: Who is this guy at the back of the grid with a green flag? |
| `IDENTIFY` | Recognize or name a referent, word, species, work or existing item. | Explain a known term: DESCRIBE. Propose a new suitable item: RECOMMEND. People as agents: WHO. | L1258: What is this fast growing plant with blue/black berries?<br>L3541: Book Identification: Chapter book about girls experiencing puberty |
| `DESCRIBE` | Meaning, definition, properties, state, consequences or a qualitative account. | Causation is EXPLAIN; relative properties are COMPARE; numeric magnitude is QUANTITY. Definitions and reactions remain a known broad-pool risk. | L150: What is celery salt?<br>L607: What happens to people without any retirement savings? |
| `WHERE` | A physical/virtual location or source for a specified referent. | Identify an unnamed place: IDENTIFY. Suggest a suitable destination/resource: RECOMMEND. Metaphorical location may request EXPLAIN. | L2145: Where was the Mummers Parade scene in It's Always Sunny in Philadelphia filmed?<br>L2314: Where is the anvil in the Shattered Keep Cellars? |
| `QUANTITY` | Count, amount, physical size, price, frequency, probability or measurable degree. | Temporal interval is DURATION. A qualitative assessment without a requested scale is DESCRIBE. | L535: How many clamps is needed for a frameless shower glass?<br>L3198: How long does my hair need to be to keep it off my face, and neck? |
| `CHOICE` | Select among supplied alternatives, including sequences of actions. | Explain differences without selecting: COMPARE. Propose options without a supplied set: RECOMMEND. Verifying identity between alternatives can be YES_NO. | L365: Should I bring Speedos or Swimming Shorts to Egypt?<br>L685: Pay online: credit card or debit card? |
| `RECOMMEND` | Suggest suitable items, resources, roles or options without a supplied candidate set. | An action sequence is METHOD; recognition is IDENTIFY; finding a specified item is WHERE. A recommendation need not start with a question word. | L3311: Recommend a printer?<br>L3563: I need personal cloud storage |
| `COMPARE` | Describe differences, similarities or trade-offs among two or more referents. | Choose a winner: CHOICE. Confirm a single equivalence claim: YES_NO. Bare “vs” remains ambiguous without stronger cues. | L28: What is the difference between white and brown eggs?<br>L1396: What are the trade-offs of weight versus repetition? |
| `OTHER` | No recoverable requested outcome in the supplied text. This includes opaque headings and underspecified problem reports. | Not a punctuation detector. Clear imperatives/indirect requests use their substantive intent. Missing facts do not erase a recoverable form. | L3557: Discord<br>L3593: It's time |

`ANY` remains editorial eligibility only, solely with `GENERAL`; it is not a fifteenth classifier intent. `OTHER` is a successful classification, not a provider failure.

## Proposed topics

Identify the requested subject before considering the setting. Retaining an untested topic is a vocabulary hypothesis; only TECH and LANGUAGE receive specialized answer trials here.

| Code | Concise definition | Nearest boundary / eligibility reason | Original examples |
| --- | --- | --- | --- |
| `TECH` | Computing, software, digital devices and their operation. | Household physical installation/repair is HOME; game mechanics/story are ENTERTAINMENT. | L2377: Why does Windows think that my wireless keyboard is a toaster?<br>L3242: How do I turn Zoom meeting information into a one-click Zoom link? |
| `WORK_STUDY` | Employment, education, teaching and research practice, including institutional progress. | Pay/finance is MONEY; subject knowledge follows its own domain. Shared evaluation, deadlines and credit justify combining work and study. | L803: How to convince HR to let my star employee work from home?<br>L2680: How can I stay on task and be productive when writing a thesis? |
| `MONEY` | Personal finance, transactions, prices, debt, investment, tax and pay. | Career progression is WORK_STUDY. Buying a product does not itself make its suitability a MONEY question. | L709: How do I budget effectively when income arrives at irregular intervals rather than a fixed monthly salary?<br>L3560: How much should I put into a joint account twice a month? |
| `RELATIONSHIPS` | Romance, friendship, family, parenting and interpersonal boundaries. | Institutional work conflict is WORK_STUDY; symptoms/treatment are HEALTH. Parenting is an editorial group, not a child-specific pool. | L1075: How do you respond to a toddler who endlessly says "why?"?<br>L3521: How do I tell a good friend I don't want to travel with her? |
| `HEALTH` | Human physical/mental wellbeing, symptoms, treatment, sleep, nutrition and fitness. | Cooking/storage is FOOD; sporting rules and technique are SPORTS. Not a safety classifier. | L1406: How can I make myself get enough sleep?<br>L1535: Why has my weight loss plateaued despite a consistent calorie deficit? |
| `FOOD` | Cooking, ingredients, drinks, preparation and food storage. | Health effects are HEALTH; growing food is NATURE; appliance repairs are HOME. Ingredient/process vocabulary merits a trial distinct from generic objects. | L1: How can I chop onions without crying?<br>L3308: How long does an opened bottle of red wine stay good? |
| `HOME` | Housing, construction, repairs, utilities, cleaning and household equipment. | Digital operation is TECH; mortgage finance is MONEY. Materials/fixtures differ from software vocabulary. | L489: How do I fix a "water hammer" problem?<br>L544: How can I fix a leak around the outer edge of a standing shower? |
| `NATURE` | Plants, animals, cultivation, ecology and nonhuman natural environments. | Trip logistics are TRAVEL; human exposure symptoms are HEALTH. Neither a plant nor a pet is guaranteed by this label. | L1237: How do you prune a rose bush?<br>L3412: What are the names of the flying bugs in my apartment? ( Details inside) |
| `TRAVEL` | Trips, transport, borders, accommodation and outdoor-trip logistics. | Organisms are NATURE; sporting technique is SPORTS; technical tool operation is TECH. Trip/route vocabulary motivates the separation. | L232: I have two passports/nationalities. How do I use them when I travel?<br>L1702: How do I choose the right tent for different weather conditions? |
| `SPORTS` | Sport rules, competition, tactics, technique and records. | Body conditioning/recovery is HEALTH; fictional/video-game mechanics are ENTERTAINMENT. Do not assume a ball, team or referee. | L1782: Points are given in tennis 15-30-40. Why 40?<br>L1871: How do you correctly learn how to hockey stop |
| `ENTERTAINMENT` | Films, TV, books, art, music, games and explicitly fictional worlds. | Actual software operation is TECH; language usage is LANGUAGE. Fictional stakes need separation from actual harm/finance, but the title must establish fiction. | L1984: Why isn't Bruce Willis's character in the Sixth Sense self-aware?<br>L2212: How does Skyrim determine if a dungeon is "cleared"? |
| `LANGUAGE` | Words, grammar, meaning, usage, pronunciation and linguistic naming. | A domain definition keeps its domain; asking how to talk to a coworker is usually WORK_STUDY. The pilot tests language-specific comparison texts. | L3014: Why is “cannot” spelled as one word?<br>L3060: What does "something 101" mean? |
| `GENERAL` | A subject outside the retained topics, or insufficient evidence for a subject. Also the topic-neutral fallback eligibility. | Includes abstract ideas, civic discussion and nonclinical everyday life until distinct usable pools justify separate topics. Never infer absent context from provenance. | L2783: Why is there something instead of nothing?<br>L3487: Does democracy work if majority of the population are uneducated? |

Keep narrower groups—bosses, PhDs, toddlers, visas, camping, pets, particular sports, film identification, grammar—as editorial writing prompts only. A text written for one group must survive other families in its actual pair. For example, NATURE does not authorize plant-only text for animal questions; ENTERTAINMENT does not authorize “the script” for every game or piece of music.

## Boundary decisions

| Distinction | Proposed decision and corpus evidence |
| --- | --- |
| Method vs explanation | L1 requests an action; L2431 requests a mechanism. L1600 asks how to estimate a hike's duration: METHOD, not DURATION merely because time is mentioned. L7 asks how to distinguish animals: METHOD, not automatically COMPARE. |
| Identification vs definition | L150 asks what a known ingredient is; L1258 names an unknown plant. L2980 indirectly asks for a word: IDENTIFY, not literal yes/no. The absence of a photo does not erase its form. Retain WHO because an agent answer can fail an object-identification request; do not merge all nominal replies. |
| Recommendation vs choice vs comparison | L3311 seeks an unsupplied printer candidate; L685 supplies payment alternatives; L1396 seeks trade-offs. L2409 asks which volume-control location is better: CHOICE. L3566 requests an app or strategy rather than selecting two supplied solutions; provisional RECOMMEND, with METHOD a plausible disagreement. |
| Moment vs duration vs physical amount | L1922 needs an event trigger; L1231 an interval; L3198 a hair length. L1480 requests sleep hours despite “how many”; L3560 requests money despite “twice a month.” QUANTITY still combines incompatible units: no portability claim follows from the rename. |
| Temporal wording without timing | L2975 asks for punctuation usage rules: METHOD. L2676 asks what counts as plagiarism: DESCRIBE. Neither requires a calendar response. A condition can count as WHEN only when it marks occurrence/timing rather than definition or general applicability. |
| Indirect or noninterrogative request | L2407 is a method request; L3311 and L3563 seek recommendations; L3215 asks for a list. Do not send them to OTHER just because they are commands or statements. L448 likely seeks a removal method, whereas L6 explicitly tests feasibility. These are editorial interpretations, not keyword rules or measured predictions. |
| Apparent question without recoverable outcome | L3184 announces a medical question but omits it. L3475 announces a question about sound. Both provisionally OTHER; topic evidence remains separate. L3191 has an intelligible request for a reaction despite missing its referent: DESCRIBE/GENERAL. L3281 still requests a cause: EXPLAIN/GENERAL. |
| Incomplete problem report | L524 permits both explanation and repair; retain OTHER/HOME when neither outcome is recoverable. This is contestable: a human may infer METHOD. Clarification text must acknowledge uncertainty without blaming the author. Do not invent a symptom-to-method default from the source community. |
| Topic overlap | L711 removes finance software: TECH. L466 runs cable through a wall: HOME. L3521 negotiates with a friend about travel: RELATIONSHIPS. L2177/L2217/L2230 do not establish a game despite Gaming provenance; never supply that hidden context. |

Keep v1's first-recoverable-request rule for multiple independent requests (L459: identity before cause and repair), applying both dimensions to that request. Only after removing incidental context, break equally central topic ties by first substantive mention. Uncertainty in intent does not automatically mean GENERAL topic.

### Nonquestions and pointed replies

Serhii suggested answering nonquestions with sarcasm encouraging an actual question. **Use an `OTHER/GENERAL` editorial pool, not a new grammatical intent.** A recoverable request can be declarative; an opaque title can contain a question mark. A separate NONQUESTION enum would cut across METHOD/RECOMMEND and reward punctuation rather than meaning.

The pilot contrasts terse headings, a problem report and an incomplete medical question. Its retained texts satirize the machine's need for instructions; texts mocking a “statement” or demanding a question mark fail. This pool cannot have an undocumented “only harmless statements” restriction. If sharper sarcasm is wanted, it needs a demonstrated semantic distinction and usable whole-pool texts before any new category is proposed. No wording is human-approved.

## Changes from both baselines

| Dimension | From implemented [v1](taxonomy-v1.md), 9 × 7 | From previous research, 14 × 16 |
| --- | --- | --- |
| Forms | Keep YES_NO, WHO, CHOICE; split temporal WHEN/DURATION; split WHAT into IDENTIFY, DESCRIBE, WHERE, RECOMMEND and a distinct COMPARE; HOW_MANY → QUANTITY; WHY → EXPLAIN; HOW → METHOD. | Keep all 14 form distinctions; rename WHY/HOW for outcomes, not grammar. Tighten comparison vs diagnostic method and usage-condition rules. |
| Unrecoverable input | Replace the blanket statement/instruction reading with recoverable-outcome reasoning; retain code OTHER. | Make missing-outcome vs missing-referent explicit; trial a clarification/sarcasm pool. No new NONQUESTION enum. |
| Subjects | Keep TECH, MONEY, HEALTH, GENERAL; WORK → WORK_STUDY; LOVE → RELATIONSHIPS. Add FOOD, HOME, NATURE, TRAVEL, SPORTS, ENTERTAINMENT, LANGUAGE. Move LIFE's remaining nonspecialized subjects to GENERAL. | Defer LIFE, IDEAS and SOCIETY to GENERAL; retain the other 13. No new merged “everything human” topic. |

Why defer those three? LIFE remains a residual category after relationships/health/work are removed; IDEAS spans logic, religion and existence; SOCIETY spans policy, institutions and public reactions. Their presence in titles is clear, but useful category-wide prepared vocabulary has not been demonstrated. A philosophy joke cannot assume an argument; a civic joke cannot assume an election. The pilot's weakest failures concern answer form and context, not the absence of those topic names. This is **an editorial simplification under limited evidence**, not a measured finding that those topics cannot work. A future specialized pool may justify restoring one. The same scrutiny still applies to broad retained topics.

Keep DESCRIBE broad provisionally rather than adding DEFINE, OPINION, EXPERIENCE and PREDICT now. Definitions vs consequences remain unresolved; a universal definition cannot safely be labeled DESCRIBE. Personal-experience requests do not permit fabricated autobiographical replies. Keep frequency/probability as QUANTITY editorial groups until separate reusable forms justify splits. More enums are not a substitute for adequate content.

Computed category arithmetic: **14 × 13 = 182** possible classification pairs, versus **224** in the preceding proposal and **63** in v1; a reduction of **42** from the proposal. These are Cartesian possibilities, not coverage, occupied pools or an authoring quota. ANY/GENERAL is outside these counts.

## What the evidence warrants next

The [current rubric source](../../src/Infrastructure/Classification/JevRubric.php) contains statement/instruction wording under OTHER. **That is an inspected instruction, not an observed Jev response.** This session has no new Jev predictions. [Historical v1 measurements](../evaluation/stage-1-results.md) cannot establish how the proposed rubric handles these titles.

Serhii proposed a bounded **100-question live trial after the taxonomy is thought through**. Suggested disjoint sampling slots: 50 ordinary form/topic cases, 20 indirect or noninterrogative requests, 10 question-looking texts without a recoverable outcome, 20 ambiguous/boundary cases = 100. This is a future sampling plan, not a collected new corpus or a completed run. Include L3311, L3563, L2407, L3184, L3475, L524, L3191 and L3281; freeze expected labels and acceptable disagreements before obtaining model outputs. Do not count absent punctuation as a gold OTHER label. Freeze the English rubric first; use a separate version and preserve the existing frozen evaluation data. Comparing two rubrics on the same 100 inputs would require 200 calls, not 100; the stated 100-call scope must not silently double.

The present pilot supplies editorial pair judgments and a single revision, not classifier accuracy or audience evidence. It supports trialing EXPLAIN/METHOD, moment/duration and comparison/choice distinctions and revising OTHER semantics. It **does not support implementing all proposed enums yet**: DESCRIBE breadth, quantity units, open recommendations, ambiguous implicit requests, residual-topic loss, and sensitive fallback suitability remain unresolved. No new classifier calls, product changes, imports, database changes or Git operations were performed.

## Local verification

Original quotations/line numbers, local links, category counts, pilot pair membership and arithmetic were checked against local files. Input hashes were compared after authoring; existing research, examples, source and frozen fixtures/reports were preserved. These checks verify artifacts, not semantic labels, humor or Jev behavior.
