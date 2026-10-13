# Offline answer seed v2 — editorial evidence

2026-10-12. **24 answers / 24 explicit eligibilities**, preliminary author screening only. Human scores are `null`. Enabled rows belong only to the isolated demonstration database, not a human-approved or production-ready library. No live classifications or audience tests were used.

[Complete replacement catalog](catalog.json) · [Exact quotations and cell assessments](editorial.json) · [Authoring guide](../answer-generation-guide.md) · [Unchanged pilot](../answer-pilot-v2.md)

## Gate and scope

Read each unchanged candidate against every listed original question. P means a recognizable satirical completion, not a factual answer; U permits explicit universal form degradation. C means no disqualifying assumption or implication identified in this author pass; A marks unsupported context, I an inappropriate implication. L means a legible turn; S means strained and F wrong form. Any meaningful S/F/A/I rejects that eligibility. Clarity and humor are separate: L never establishes amusement. Weakest pairing determines acceptance; no averages or hidden subgroups.

Preliminary score order in editorial.json: form, portability across listed probes, legible shift, economy, batch novelty, author-estimated comic potential (0–2). Accepted records have first two = 2, others ≥1, totals ≥9/12. These are author judgments, not measured reader reactions. Human scores remain null. Passing the seen probes does not prove whole-pool suitability.

Probes were chosen before finalizing new candidates. Original pilot probes were retained and strengthened for WHEN (finance) and OTHER (symptoms and finance). METHOD includes dangerous electrical symptoms and constrained exercise; EXPLAIN includes food safety, finance and health. WHO includes disaster relief and institutional responsibility; no direct healthcare WHO title was identified in this bounded probe set. No source-community metadata supplies runtime context. Titles are quoted data; their allegations and premises are not adopted as facts.

## Coverage

14 × 13 = 182 classifier pairs. **9 populated / 173 empty** exact classification pools, plus ANY/GENERAL. Six intent/GENERAL pools: WHEN, EXPLAIN, METHOD, WHO, CHOICE, OTHER. Eight intents have no GENERAL pool: YES_NO, DURATION, IDENTIFY, DESCRIBE, WHERE, QUANTITY, RECOMMEND, COMPARE. Three specialized pools: EXPLAIN/TECH, COMPARE/LANGUAGE, EXPLAIN/LANGUAGE. GENERAL rows also act as exact pools when the classified topic is GENERAL. Universal fallback is not counted as coverage of 182 pairs.

| Pool | Answers | Original probes | Candidate × question cells |
| --- | ---: | ---: | ---: |
| EXPLAIN/TECH | 4 | 8 | 32 |
| COMPARE/LANGUAGE | 2 | 8 | 16 |
| WHEN/GENERAL | 2 | 9 | 18 |
| CHOICE/GENERAL | 3 | 8 | 24 |
| OTHER/GENERAL | 2 | 10 | 20 |
| METHOD/GENERAL | 3 | 10 | 30 |
| EXPLAIN/GENERAL | 3 | 8 | 24 |
| WHO/GENERAL | 2 | 8 | 16 |
| EXPLAIN/LANGUAGE | 1 | 8 | 8 |
| ANY/GENERAL | 2 | 32 | 64 |

## Candidates and matrices

### EXPLAIN/TECH

| Probe | Unchanged original title |
| --- | --- |
| [L2377](../../research/questions-v1/questions.txt#L2377) | Why does Windows think that my wireless keyboard is a toaster? |
| [L2391](../../research/questions-v1/questions.txt#L2391) | Why does macOS keep asking for my SSH passphrase ever since I updated to macOS Sierra (10.12)? |
| [L2395](../../research/questions-v1/questions.txt#L2395) | Why does this PNG image display differently in Chrome & Firefox than in Safari and IE? |
| [L2431](../../research/questions-v1/questions.txt#L2431) | How does a computer restart itself? |
| [L2460](../../research/questions-v1/questions.txt#L2460) | Why don't websites immediately display their text these days? |
| [L2503](../../research/questions-v1/questions.txt#L2503) | What could be the reason that some messenger remove EXIF data from photos sent as regular messages instead of documents? |
| [L3299](../../research/questions-v1/questions.txt#L3299) | Why is my cellphone getting hot while listening to music, on YouTube, and charging at the same time? |
| [L3327](../../research/questions-v1/questions.txt#L3327) | How do QR codes work? |

| Key | Completed text | Mechanism | Humor / novelty / weakest-pair limit |
| --- | --- | --- | --- |
| A2 | Because the machine follows instructions, including the instructions telling it to disregard the instructions. | reversal | Instruction-following defeats itself. Obedience recursion; distinct from mutual corroboration in N01. An absurd operating principle, not a technical walkthrough; QR and overheating included. |
| A4 | Because every simple operation includes a full rehearsal of the complicated version. | hyperbole | Simple operations rehearse their complicated counterparts. Theatrical overhead; do not add more rehearsal variants. Does not diagnose heat or prescribe repair. |
| N01 | Because the hardware believes the software, and the software has mistaken that for independent confirmation. | false_causality | Circular corroboration masquerades as evidence. Two components validate each other, rather than cancel instructions. Hardware/software is technical vocabulary, not an assumed malfunction. |
| N02 | Because the interface translates every request into the dialect spoken by its least cooperative component. | metaphor | Interoperation becomes diplomacy in a hostile dialect. Translation/cooperation rather than A2 obedience or A4 rehearsal. Includes normal QR operation; cooperation is personification, not an actual defect claim. |

Cells separate **form / context / clarity**. Humor and novelty are assessed in the notes above.

| Key | L2377 | L2391 | L2395 | L2431 | L2460 | L2503 | L3299 | L3327 |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| A2 | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L |
| A4 | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L |
| N01 | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L |
| N02 | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L |

### COMPARE/LANGUAGE

| Probe | Unchanged original title |
| --- | --- |
| [L2991](../../research/questions-v1/questions.txt#L2991) | What's the difference between "requester" and "requestor"? |
| [L3015](../../research/questions-v1/questions.txt#L3015) | Difference between "artifact" and "artefact" |
| [L3017](../../research/questions-v1/questions.txt#L3017) | What is the difference between "complicated" and "complex"? |
| [L3033](../../research/questions-v1/questions.txt#L3033) | What is the difference between "till" and "until"? |
| [L3063](../../research/questions-v1/questions.txt#L3063) | Difference between "delete" and "remove" |
| [L3073](../../research/questions-v1/questions.txt#L3073) | What (film) or what kind of (film): difference |
| [L3138](../../research/questions-v1/questions.txt#L3138) | Differences between 'stand next to', 'stand with', and 'stand by' |
| [L3092](../../research/questions-v1/questions.txt#L3092) | Pronunciation of the name “Evelyn” in British vs. American English |

| Key | Completed text | Mechanism | Humor / novelty / weakest-pair limit |
| --- | --- | --- | --- |
| B1 | The distinction is small enough to fit in a footnote and large enough to support a lifelong feud. | hyperbole | Tiny distinctions sustain enormous social conflict. Scale/conflict contrast; no further footnote or feud variants. Smallness is comic exaggeration, not a claim of synonymy; three expressions and pronunciation included. |
| B3 | They differ mainly in which correction you will receive from someone who understood you perfectly. | reversal | Successful understanding generates unnecessary correction. Social correction, not B1's scale or B2's shared destination. Compares consequences of usage, not literal lexical properties; human form-fit review needed. |

Cells separate **form / context / clarity**. Humor and novelty are assessed in the notes above.

| Key | L2991 | L3015 | L3017 | L3033 | L3063 | L3073 | L3138 | L3092 |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| B1 | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L |
| B3 | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L |

### WHEN/GENERAL

| Probe | Unchanged original title |
| --- | --- |
| [L915](../../research/questions-v1/questions.txt#L915) | When should I disclose an upcoming vacation that overlaps with the start date of an internal job position? |
| [L1922](../../research/questions-v1/questions.txt#L1922) | When exactly does the game clock start during an American football kickoff? |
| [L1201](../../research/questions-v1/questions.txt#L1201) | When should I pile more dirt onto a growing potato plant? |
| [L1293](../../research/questions-v1/questions.txt#L1293) | When do crabgrass seeds mature? |
| [L1456](../../research/questions-v1/questions.txt#L1456) | When is the best time of day to accurately weigh yourself? |
| [L1472](../../research/questions-v1/questions.txt#L1472) | When is it time to buy new running shoes |
| [L1517](../../research/questions-v1/questions.txt#L1517) | When should you do warm-up sets? |
| [L3294](../../research/questions-v1/questions.txt#L3294) | At what times of the day should you take pills? |
| [L693](../../research/questions-v1/questions.txt#L693) | At what point do index funds become unreliable? |

| Key | Completed text | Mechanism | Humor / novelty / weakest-pair limit |
| --- | --- | --- | --- |
| C2 | At the precise moment the schedule stops describing its ambitions as facts. | false_causality | The schedule must stop mistaking plans for facts. Epistemic condition; distinct from ceremony chronology. Impossible meta-condition, not a waiting instruction; medication and index-fund threshold remain sensitive. |
| C4 | At the opening ceremony for the present. Attendance by the future is compulsory. | bureaucratic_absurdity | The present holds a ceremony with compulsory future attendance. Impossible temporal landmark; no renamed-ceremony variants. No real date or delay; human review of timing jokes on medication remains essential. |

Cells separate **form / context / clarity**. Humor and novelty are assessed in the notes above.

| Key | L915 | L1922 | L1201 | L1293 | L1456 | L1472 | L1517 | L3294 | L693 |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| C2 | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L |
| C4 | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L |

### CHOICE/GENERAL

| Probe | Unchanged original title |
| --- | --- |
| [L9](../../research/questions-v1/questions.txt#L9) | Do you heat the pan first, then add oil? Or put the oil in and heat up with the pan? |
| [L365](../../research/questions-v1/questions.txt#L365) | Should I bring Speedos or Swimming Shorts to Egypt? |
| [L618](../../research/questions-v1/questions.txt#L618) | Should I buy my house in cash, or with a mortgage and invest the rest of my money? |
| [L685](../../research/questions-v1/questions.txt#L685) | Pay online: credit card or debit card? |
| [L1462](../../research/questions-v1/questions.txt#L1462) | Persistent mild knee pain due to squats - push through or stop? |
| [L3025](../../research/questions-v1/questions.txt#L3025) | Which is correct: "Filename", "File Name" or "FileName"? |
| [L3253](../../research/questions-v1/questions.txt#L3253) | M2 MVNe drive or SATA SSD? |
| [L3502](../../research/questions-v1/questions.txt#L3502) | If you were going to completely replace your driveway, would you do asphalt, gravel, or concrete and why? |

| Key | Completed text | Mechanism | Humor / novelty / weakest-pair limit |
| --- | --- | --- | --- |
| E2 | Choose whichever option sounds least likely to ask you to join a loyalty program. | metaphor | Alternatives become vendors seeking customer loyalty. Commercial solicitation criterion, not pigeon evaluation. Fictional criterion gives no winner; on payment questions must not read as advice against loyalty cards. |
| E3 | Choose the option you can explain to a pigeon without losing the pigeon's respect. | reversal | A pigeon becomes the evaluator of a decision. Inverted judgment hierarchy; not a claim the reader owns a bird. Criterion-only answer is an explicit editorial allowance; no medical/financial winner selected. |
| N11 | Choose whichever option would be most embarrassed to appear in its own advertisement. | reversal | A sales pitch embarrasses the thing being sold. Self-image against publicity, not vendor loyalty or an external animal judge. No named winner; the criterion requires impossible option emotions, not financial comparison. |

Cells separate **form / context / clarity**. Humor and novelty are assessed in the notes above.

| Key | L9 | L365 | L618 | L685 | L1462 | L3025 | L3253 | L3502 |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| E2 | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L |
| E3 | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L |
| N11 | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L |

### OTHER/GENERAL

| Probe | Unchanged original title |
| --- | --- |
| [L3557](../../research/questions-v1/questions.txt#L3557) | Discord |
| [L3593](../../research/questions-v1/questions.txt#L3593) | It's time |
| [L3577](../../research/questions-v1/questions.txt#L3577) | cooking |
| [L2940](../../research/questions-v1/questions.txt#L2940) | Non-classical logics |
| [L2900](../../research/questions-v1/questions.txt#L2900) | L'amour, l'amour, l'amour |
| [L2487](../../research/questions-v1/questions.txt#L2487) | Audio in html website |
| [L524](../../research/questions-v1/questions.txt#L524) | Washing machine sometimes hums and won't spin |
| [L3184](../../research/questions-v1/questions.txt#L3184) | I have a medical question about dizziness? ( Details inside) |
| [L1446](../../research/questions-v1/questions.txt#L1446) | My legs still ache 3-4 days after legs day |
| [L603](../../research/questions-v1/questions.txt#L603) | I'm supposedly eligible for an inheritance from a distant relative. Offer appears to be legitimate |

| Key | Completed text | Mechanism | Humor / novelty / weakest-pair limit |
| --- | --- | --- | --- |
| F3 | The answer is on standby. It refuses to guess its own assignment. | reversal | The answer refuses to invent its assignment. Agency belongs to the answer; unlike historical answer-first/recruit-question joke. Mild clarification; no claim of meaningless input or deficient punctuation. |
| F4 | Please specify what you want to know. Omniscience requires unusually detailed instructions. | bureaucratic_absurdity | Omniscience requires detailed instructions. Knowledge/specification contradiction; avoid crystal-ball variants. Direct clarification remains considerate on incomplete symptoms and finance reports. |

Cells separate **form / context / clarity**. Humor and novelty are assessed in the notes above.

| Key | L3557 | L3593 | L3577 | L2940 | L2900 | L2487 | L524 | L3184 | L1446 | L603 |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| F3 | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L |
| F4 | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L |

### METHOD/GENERAL

| Probe | Unchanged original title |
| --- | --- |
| [L1](../../research/questions-v1/questions.txt#L1) | How can I chop onions without crying? |
| [L489](../../research/questions-v1/questions.txt#L489) | How do I fix a "water hammer" problem? |
| [L709](../../research/questions-v1/questions.txt#L709) | How do I budget effectively when income arrives at irregular intervals rather than a fixed monthly salary? |
| [L803](../../research/questions-v1/questions.txt#L803) | How to convince HR to let my star employee work from home? |
| [L1075](../../research/questions-v1/questions.txt#L1075) | How do you respond to a toddler who endlessly says "why?"? |
| [L1406](../../research/questions-v1/questions.txt#L1406) | How can I make myself get enough sleep? |
| [L1702](../../research/questions-v1/questions.txt#L1702) | How do I choose the right tent for different weather conditions? |
| [L2407](../../research/questions-v1/questions.txt#L2407) | Reload a Linux user's group assignments without logging out |
| [L593](../../research/questions-v1/questions.txt#L593) | how to fix voltage in the soil and shocks when outside AC compressor unit(s) turns on |
| [L1485](../../research/questions-v1/questions.txt#L1485) | How can I safely program leg exercises for a student with flat feet, hypermobility, and hEDS-type joint restrictions? |

| Key | Completed text | Mechanism | Humor / novelty / weakest-pair limit |
| --- | --- | --- | --- |
| N03 | Turn the procedure inside out. The missing step is usually hiding in the lining. | metaphor | An abstract procedure becomes clothing with a concealed step. Hidden lining, not a supervisor or solution-before-problem reversal. Impossible action; no actual treatment, electrical repair or investment instruction. |
| N04 | Give the instructions a practical exam. They have been marking everyone else's work. | reversal | The evaluator must demonstrate its own competence. Examining instructions rather than assigning more supervisors. Does not imply that the user failed; satire targets instructions. |
| N05 | Draw a straight line from intention to result. Apply for permission to bend the paper. | bureaucratic_absurdity | A shortcut in an abstract plan needs a physical-paper permit. Geometry plus permission, not repeated signatures or waiting recursion. Physical metaphor cannot be followed as clinical or electrical procedure. |

Cells separate **form / context / clarity**. Humor and novelty are assessed in the notes above.

| Key | L1 | L489 | L709 | L803 | L1075 | L1406 | L1702 | L2407 | L593 | L1485 |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| N03 | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L |
| N04 | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L |
| N05 | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L |

### EXPLAIN/GENERAL

| Probe | Unchanged original title |
| --- | --- |
| [L3](../../research/questions-v1/questions.txt#L3) | Why add salt to the water when cooking pasta? |
| [L54](../../research/questions-v1/questions.txt#L54) | Why is it dangerous to eat meat which has been left out and then cooked? |
| [L600](../../research/questions-v1/questions.txt#L600) | If a stock doesn't pay dividends, then why is the stock worth anything? |
| [L1397](../../research/questions-v1/questions.txt#L1397) | Why does running make you have to poop? |
| [L1535](../../research/questions-v1/questions.txt#L1535) | Why has my weight loss plateaued despite a consistent calorie deficit? |
| [L1782](../../research/questions-v1/questions.txt#L1782) | Points are given in tennis 15-30-40. Why 40? |
| [L2431](../../research/questions-v1/questions.txt#L2431) | How does a computer restart itself? |
| [L2783](../../research/questions-v1/questions.txt#L2783) | Why is there something instead of nothing? |

| Key | Completed text | Mechanism | Humor / novelty / weakest-pair limit |
| --- | --- | --- | --- |
| N06 | Because coincidence has been promoted to project manager. | false_causality | Coincidence receives causal responsibility by appointment. Misattributed causation, not evidence arriving after its conclusion. Does not assert a real diagnosis or assign blame to the questioner. |
| N07 | Because cause and effect are sharing a password. | metaphor | Separate causal roles lose their identity through shared credentials. Authentication transfer; no named technology or runtime substitution. Not actual credential advice; short but conceptual turn may need reader review. |
| N08 | Because the explanation is paid by the complication. | metaphor | Payment incentives manufacture unnecessary complexity. Incentive structure rather than A4 theatrical overhead; related complexity motif acknowledged. Satirizes explanatory machinery, not a medical professional or distressed user. |

Cells separate **form / context / clarity**. Humor and novelty are assessed in the notes above.

| Key | L3 | L54 | L600 | L1397 | L1535 | L1782 | L2431 | L2783 |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| N06 | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L |
| N07 | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L |
| N08 | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L |

### WHO/GENERAL

| Probe | Unchanged original title |
| --- | --- |
| [L272](../../research/questions-v1/questions.txt#L272) | Denied boarding although I have proper visa and documentation. To whom should I make a complaint? |
| [L597](../../research/questions-v1/questions.txt#L597) | Who can truly afford luxury cars? |
| [L1859](../../research/questions-v1/questions.txt#L1859) | Who keeps the record of each international cricket match? |
| [L1926](../../research/questions-v1/questions.txt#L1926) | Who is this guy at the back of the grid with a green flag? |
| [L2004](../../research/questions-v1/questions.txt#L2004) | Who was the oldest actor to play a teenager? |
| [L2096](../../research/questions-v1/questions.txt#L2096) | Who was Yuri Zhivago chasing in his final moments? |
| [L3343](../../research/questions-v1/questions.txt#L3343) | Who were the republicans that refused to show up to the public hearing on ICE victims? |
| [L3432](../../research/questions-v1/questions.txt#L3432) | Who is a primary relief organization that will focus on aid to Jamaica after Melissa hits? |

| Key | Completed text | Mechanism | Humor / novelty / weakest-pair limit |
| --- | --- | --- | --- |
| N09 | The witness called by the alibi. | reversal | An abstract defense calls its own witness. Legal role reversal; no calendar, committee or fabricated real identity. An obviously impossible agent relation; factual identification intentionally not provided. |
| N10 | The expert appointed by a show of invisible hands. | bureaucratic_absurdity | An appointment rests on votes nobody can observe. Invisible voting, distinct from alibi agency. No actual expert, doctor, accused person or relief organization is named. |

Cells separate **form / context / clarity**. Humor and novelty are assessed in the notes above.

| Key | L272 | L597 | L1859 | L1926 | L2004 | L2096 | L3343 | L3432 |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| N09 | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L |
| N10 | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L |

### EXPLAIN/LANGUAGE

| Probe | Unchanged original title |
| --- | --- |
| [L3013](../../research/questions-v1/questions.txt#L3013) | Why are the vowels in Christ and Christmas different? (and other strange diphthong behaviour) |
| [L3014](../../research/questions-v1/questions.txt#L3014) | Why is “cannot” spelled as one word? |
| [L3019](../../research/questions-v1/questions.txt#L3019) | Why do English writers avoid explicit numerals? |
| [L3021](../../research/questions-v1/questions.txt#L3021) | Why does "quadratic" describe second power when "quad" means "four"? |
| [L3024](../../research/questions-v1/questions.txt#L3024) | Why is "bicycle" pronounced differently from other obviously related words? |
| [L3028](../../research/questions-v1/questions.txt#L3028) | Why is "Pokémon" written with an accent? |
| [L3037](../../research/questions-v1/questions.txt#L3037) | Why should the first person pronoun 'I' always be capitalized? |
| [L3067](../../research/questions-v1/questions.txt#L3067) | Why does the multi-paragraph quotation rule exist? |

| Key | Completed text | Mechanism | Humor / novelty / weakest-pair limit |
| --- | --- | --- | --- |
| N12 | Because the dictionary records the rules after the words have already broken them. | reversal | Rule-breaking precedes the authority that records rules. Lexicographic time reversal, not generic exception exemption. Dictionary is a fictional authority here, not a factual history of spelling or pronunciation. |

Cells separate **form / context / clarity**. Humor and novelty are assessed in the notes above.

| Key | L3013 | L3014 | L3019 | L3021 | L3024 | L3028 | L3037 | L3067 |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| N12 | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L | P/C/L |

### ANY/GENERAL

| Probe | Unchanged original title |
| --- | --- |
| [L68](../../research/questions-v1/questions.txt#L68) | Should I refrigerate eggs? |
| [L3294](../../research/questions-v1/questions.txt#L3294) | At what times of the day should you take pills? |
| [L1480](../../research/questions-v1/questions.txt#L1480) | For better muscle growth, how many hours' sleep are recommended after doing a workout? |
| [L1535](../../research/questions-v1/questions.txt#L1535) | Why has my weight loss plateaued despite a consistent calorie deficit? |
| [L593](../../research/questions-v1/questions.txt#L593) | how to fix voltage in the soil and shocks when outside AC compressor unit(s) turns on |
| [L1859](../../research/questions-v1/questions.txt#L1859) | Who keeps the record of each international cricket match? |
| [L1258](../../research/questions-v1/questions.txt#L1258) | What is this fast growing plant with blue/black berries? |
| [L607](../../research/questions-v1/questions.txt#L607) | What happens to people without any retirement savings? |
| [L2145](../../research/questions-v1/questions.txt#L2145) | Where was the Mummers Parade scene in It's Always Sunny in Philadelphia filmed? |
| [L3560](../../research/questions-v1/questions.txt#L3560) | How much should I put into a joint account twice a month? |
| [L1462](../../research/questions-v1/questions.txt#L1462) | Persistent mild knee pain due to squats - push through or stop? |
| [L3311](../../research/questions-v1/questions.txt#L3311) | Recommend a printer? |
| [L1396](../../research/questions-v1/questions.txt#L1396) | What are the trade-offs of weight versus repetition? |
| [L3184](../../research/questions-v1/questions.txt#L3184) | I have a medical question about dizziness? ( Details inside) |
| [L1446](../../research/questions-v1/questions.txt#L1446) | My legs still ache 3-4 days after legs day |
| [L3557](../../research/questions-v1/questions.txt#L3557) | Discord |
| [L3593](../../research/questions-v1/questions.txt#L3593) | It's time |
| [L3563](../../research/questions-v1/questions.txt#L3563) | I need personal cloud storage |
| [L2407](../../research/questions-v1/questions.txt#L2407) | Reload a Linux user's group assignments without logging out |
| [L3191](../../research/questions-v1/questions.txt#L3191) | How do you feel about this? |
| [L3281](../../research/questions-v1/questions.txt#L3281) | What could have caused this (details below)? |
| [L324](../../research/questions-v1/questions.txt#L324) | Will I be able to enter Latvia without a visa? |
| [L1075](../../research/questions-v1/questions.txt#L1075) | How do you respond to a toddler who endlessly says "why?"? |
| [L2783](../../research/questions-v1/questions.txt#L2783) | Why is there something instead of nothing? |
| [L3487](../../research/questions-v1/questions.txt#L3487) | Does democracy work if majority of the population are uneducated? |
| [L3471](../../research/questions-v1/questions.txt#L3471) | How long will it take for the "Head and Shoulders, plus Old Spice , shampoo and conditioner " to get out of my eyes? |
| [L54](../../research/questions-v1/questions.txt#L54) | Why is it dangerous to eat meat which has been left out and then cooked? |
| [L618](../../research/questions-v1/questions.txt#L618) | Should I buy my house in cash, or with a mortgage and invest the rest of my money? |
| [L3432](../../research/questions-v1/questions.txt#L3432) | Who is a primary relief organization that will focus on aid to Jamaica after Melissa hits? |
| [L2177](../../research/questions-v1/questions.txt#L2177) | How can I tell if a corpse is safe to eat? |
| [L915](../../research/questions-v1/questions.txt#L915) | When should I disclose an upcoming vacation that overlaps with the start date of an internal job position? |
| [L2991](../../research/questions-v1/questions.txt#L2991) | What's the difference between "requester" and "requestor"? |

| Key | Completed text | Mechanism | Humor / novelty / weakest-pair limit |
| --- | --- | --- | --- |
| U01 | This answer contains no useful information. Its confidence is sold separately. | metaphor | Confidence is an optional paid accessory to an empty product. Commodity separation rather than fictional certification, proof of purchase or settled uncertainty. Explicitly useless answer; specific form degrades without pretending to offer advice. |
| U02 | The oracle is out of facts. It is polishing the delivery. | reversal | The source of knowledge substitutes presentation for knowledge. Presentation replacing substance, distinct from U01 accessory pricing. Target is the oracle; neither reassures about harm nor blames incomplete inputs. |

Cells separate **form / context / clarity**. Humor and novelty are assessed in the notes above.

| Key | L68 | L3294 | L1480 | L1535 | L593 | L1859 | L1258 | L607 | L2145 | L3560 | L1462 | L3311 | L1396 | L3184 | L1446 | L3557 | L3593 | L3563 | L2407 | L3191 | L3281 | L324 | L1075 | L2783 | L3487 | L3471 | L54 | L618 | L3432 | L2177 | L915 | L2991 |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| U01 | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L |
| U02 | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L | U/C/L |

## Rejections and pilot reassessment

Of the 11 requested pilot survivors: **10 retained unchanged, 0 revised, 1 excluded (B2)**. A2, A4, B1, B3, C2, C4, E2, E3, F3 and F4 passed this new preliminary gate. Original pilot texts/statuses remain unchanged in their historical file. All 13 original pilot hold/reject texts remain excluded; none was imported or silently relabeled. B2 is held for possible rewriting, not certified unusable in every narrower future pool.

14 new texts passed. Eleven candidates below failed this pass (B2 plus ten new drafts): **35 candidate versions assessed, 24 retained, 11 excluded**. The requested 30–50 usable count was not filled: duration, open recommendation and location pools still fail form, implications or sufficient breadth; weaker repetitions were not used to reach the quota. No substantive revisions were made after these matrices, so no unrecorded retest is implied.

| Key | Proposed pair | Excluded completed text | Decision / representative failure |
| --- | --- | --- | --- |
| B2 | COMPARE/LANGUAGE | Same destination, different toll booths. Each charges extra for being technically correct. | hold: Same destination can assert equivalent meaning at L3017 and L3138. The author cannot require readers to reinterpret it as merely communication. |
| R01 | CHOICE/GENERAL | Choose the option that makes the fewest demands on you. The demands have formed a union. | reject: At L1462 a literal least-demand criterion can decide whether to continue through pain; joke does not cancel advice. |
| R02 | DURATION/GENERAL | Three business eternities. The receipt expires first. | reject: Long-wait implication fails food storage, water sanitation and eye exposure; also clones pilot D1. |
| R03 | COMPARE/LANGUAGE | The differences are pronounced. The agreement is silent. | hold: At L3092 pronounced is literal and the supposed agreement is invented; familiar wordplay, weak comparison. |
| R04 | WHEN/GENERAL | When the clock finishes checking whether now is available. | hold: Form is temporal but concept repeats the guide waiting-period recursion and pilot temporal bureaucracy. |
| R05 | OTHER/GENERAL | Please specify the request. The crystal ball currently works as a paperweight. | hold: Considerate clarification but repeats F4 omniscience needing user input with a replacement prop. |
| R06 | EXPLAIN/GENERAL | Because the exception found a loophole in the rule against exceptions. | hold: At L1535 treats a health plateau as an exception without basis; recursive-rule motif adds little novelty. |
| R07 | COMPARE/LANGUAGE | The similarities fit on a postcard. The differences have applied for their own postal code. | hold: Form fits but repeats B1 small-versus-large distinction scale with substituted stationery. |
| R08 | EXPLAIN/LANGUAGE | Because grammar has diplomatic immunity from its own examples. | hold: Grammar does not cover all pronunciation and spelling probes; at L3028 the assumed grammar jurisdiction is strained. |
| R09 | RECOMMEND/GENERAL | A pocket-sized second opinion. It disagrees before unfolding. | hold: Wrong kind of recommendation for printer, food and shower-filter requests; abstraction is not a portable suitable item. |
| R10 | WHERE/GENERAL | At the point where the map quietly changes the subject. | hold: Virtual source and factual filming-location requests strain a single map premise; current original probes lack sufficient health/financial breadth. |

The full unchanged question × excluded-candidate matrices and exact source URLs are in editorial.json. R02 uses the eight original pilot duration probes; R09 and R10 have their own eight-source matrices there. Passing form cells do not rescue a novelty failure (R04/R05/R07).

## Universal fallback and residual risks

Both U texts were tested unchanged on 32 originals covering every v2 intent, all 13 topics, specialized financial/health/safety cases and incomplete requests. Universal checks include a medical announcement, symptom report, opaque headings, missing referents, eye exposure, electrical shock, injury choice, money amount and disaster relief. Neither gives a time, dose, action winner, diagnosis or invented personal experience. Their loss of answer form is deliberate degradation. Self-directed satire can still feel unhelpful during distress; human policy/tone review is outstanding.

Novelty comparison included the full guide, stage-1 examples, all pilot versions and this batch, at both text and premise level. No external originality search or claim was made. Bureaucracy and personification remain recurrent motifs; repeated exposure and humor were not measured. Criterion-only CHOICE, comic principles instead of EXPLAIN walkthroughs, and impossible temporal conditions are explicit editorial allowances needing human confirmation. If a human finds an unsafe or incompatible same-pool pairing, remove or revise that eligibility; never add hidden runtime exclusions.

## Artifact counts

Accepted matrices: **252** cells. Excluded matrices: **91** cells. Total: **343** author judgments on **110 distinct original titles**. Each proposed pair has at least eight unchanged source questions. Answer texts, UUIDs, timestamps, pairs, exact quotations, CSV row alignment and arithmetic are validated separately from these judgments.

UUIDv5 identifiers are deterministically assigned within this seed artifact namespace by candidate key and explicit pair; preserve them and their UTC creation times on repeat import. Each answer has one eligibility and one primary mechanism. No review/provenance columns were added to the database.
