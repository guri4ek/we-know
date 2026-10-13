# Prepared-answer pilot v2

2026-10-12 — offline English author drafts for the [proposed taxonomy](taxonomy-v2-proposal.md), not a catalog or human-approved content.

**24 completed candidates tested; 11 remain draft, 11 hold, 2 reject after one revision pass.** The 11 surviving drafts fall short of the 18–24 target; the failed candidates remain visible as evidence, not quota fillers. No DURATION/GENERAL candidate survives. “Draft” means provisionally worth human review on these probes, never publication approval or proof of whole-pool portability. All human scores are **`null`**.

## Contract and method

Followed the full [authoring guide](answer-generation-guide.md) and [mechanism research](../research/humor-mechanisms.md); proposed taxonomy labels are explicitly authorized for these documents. Compared with their existing examples and [stage-1 texts](stage-1-answer-examples.md); no exact text was reused. Familiar time/bureaucracy motifs remain a fatigue risk, not evidence of originality against all published humor.

Each record below proposes exactly one eligibility pair, printed on its group heading, and one primary mechanism for that pair. No cross-pair eligibility is implied. Texts have no substitutions, personal experience claims or question-specific names. Status and subgroup descriptions are editorial, not runtime filters.

The inspected [selector](../../src/Domain/Answer/AnswerSelector.php) and [catalog](../../src/Infrastructure/Persistence/CatalogRepository.php) use enabled English answers and exact eligibility pairs, uniformly from the first nonempty pool: exact → intent/GENERAL → ANY/GENERAL. There are no phrase-level filters for seriousness, named objects, question syntax or mechanisms. GENERAL trials therefore include specialized subjects; OTHER/GENERAL is not universal fallback. ANY/GENERAL was not authored or validated here.

Before drafting, selected the eight original questions below for each of six pools. Read every initial candidate unchanged against every question: **6 × 4 × 8 = 192 judgments**. In one revision pass changed B1 and F1, then reread each against all eight of its original questions: **16 further judgments; 208 total**, with **48 distinct corpus titles**. This is author-performed editorial assessment on seen questions, not an automated semantic test, blinded study or Jev evaluation. The two removed versions and their results are retained below. No question was rewritten or added to the collection.

**Gate:** a single meaningful strained form, unsupported assumption, confusing reference or inappropriate implication holds/rejects the entire proposed eligibility; do not average failures away. `P` means a recognizable satirical completion of the requested form, not a factually correct answer. A causal principle may be absurd; a comparison may compare consequences of wording; CHOICE may offer a satirical criterion under the guide. These allowances do not turn a date into a duration or make a literal harmful selection acceptable.

Matrix notation separates form from other failures: **P** = form fits; **S** = strained form; **F** = wrong form; **/A** = unsupported subject/context assumption; **/I** = inappropriate implication. Humor is assessed separately in each candidate's editorial note, without numeric or human scores. Passing cells do not establish human humor or suitability judgments.

## A — `EXPLAIN/TECH`

Causes, design choices and operating mechanisms; includes both malfunctions and normal operation.

| Probe | Original corpus question |
| --- | --- |
| A01 | L2377: Why does Windows think that my wireless keyboard is a toaster? |
| A02 | L2391: Why does macOS keep asking for my SSH passphrase ever since I updated to macOS Sierra (10.12)? |
| A03 | L2395: Why does this PNG image display differently in Chrome & Firefox than in Safari and IE? |
| A04 | L2431: How does a computer restart itself? |
| A05 | L2460: Why don't websites immediately display their text these days? |
| A06 | L2503: What could be the reason that some messenger remove EXIF data from photos sent as regular messages instead of documents? |
| A07 | L3299: Why is my cellphone getting hot while listening to music, on YouTube, and charging at the same time? |
| A08 | L3327: How do QR codes work? |

All four records propose only **`EXPLAIN/TECH`**.

| ID | Complete answer text | Primary mechanism | Status | Preliminary humor assessment and material limit |
| --- | --- | --- | --- | --- |
| A1 | Because compatibility means every component must disagree in exactly the same format. | `reversal` | hold | Compatibility as standardized disagreement is legible; it invents an interoperability problem in normal-operation questions. |
| A2 | Because the machine follows instructions, including the instructions telling it to disregard the instructions. | `reversal` | draft | Obedience cancels itself; compact and recognizably technical. Mechanism questions get a comic causal principle, not a procedural walkthrough. |
| A3 | Because convenience is stored in a format the user cannot open. | `metaphor` | hold | Usability becomes an inaccessible file. Stronger on frustrating interfaces; normal QR operation supplies no missing convenience. |
| A4 | Because every simple operation includes a full rehearsal of the complicated version. | `hyperbole` | draft | A tiny operation rehearses unnecessary complexity. The theatrical process differs from A2; explanatory specificity and reader amusement remain unmeasured. |

Final-text matrix, in probe order:

| Candidate | 01 | 02 | 03 | 04 | 05 | 06 | 07 | 08 |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| A1 | P | P/A | P | P/A | P/A | P/A | P/A | P/A |
| A2 | P | P | P | P | P | P | P | P |
| A3 | P | P | P/A | P/A | P | P/A | P/A | P/A |
| A4 | P | P | P | P | P | P | P | P |

**Failures:** A1 assumes an interoperability problem at A04/A08; A3 turns normal QR operation into inaccessible convenience. EXPLAIN/TECH cannot mean only annoying software. A2/A4 offer comic operating principles rather than technical walkthroughs; humor on A08 remains tentative. A07 tests a physical symptom without prescribing a repair.

## B — `COMPARE/LANGUAGE`

Spelling, meaning, usage, three-way comparison and pronunciation; not just two synonymous words.

| Probe | Original corpus question |
| --- | --- |
| B01 | L2991: What's the difference between "requester" and "requestor"? |
| B02 | L3015: Difference between "artifact" and "artefact" |
| B03 | L3017: What is the difference between "complicated" and "complex"? |
| B04 | L3033: What is the difference between "till" and "until"? |
| B05 | L3063: Difference between "delete" and "remove" |
| B06 | L3073: What (film) or what kind of (film): difference |
| B07 | L3138: Differences between 'stand next to', 'stand with', and 'stand by' |
| B08 | L3092: Pronunciation of the name “Evelyn” in British vs. American English |

All four records propose only **`COMPARE/LANGUAGE`**.

| ID | Complete answer text | Primary mechanism | Status | Preliminary humor assessment and material limit |
| --- | --- | --- | --- | --- |
| B1 | The distinction is small enough to fit in a footnote and large enough to support a lifelong feud. | `hyperbole` | draft | Revised once: opposing scales of linguistic difference and social conflict. Works without assuming two terms or a difference in meaning. |
| B2 | Same destination, different toll booths. Each charges extra for being technically correct. | `metaphor` | draft | Alternate expressions become rival routes charging for correctness. “Destination” is communication, not a claim that all terms are synonyms; that reading needs human review. |
| B3 | They differ mainly in which correction you will receive from someone who understood you perfectly. | `reversal` | draft | Successful understanding produces correction instead of ending it. Compares consequences of usage; not a factual account of the terms. |
| B4 | One is formal. The other owns a tie. | `metaphor` | hold | A register label becomes clothing. Overfamiliar image, and a binary frame cannot serve the three-expression probe. |

Final-text matrix, in probe order:

| Candidate | 01 | 02 | 03 | 04 | 05 | 06 | 07 | 08 |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| B1 | P | P | P | P | P | P | P | P |
| B2 | P | P | P | P | P | P | P | P |
| B3 | P | P | P | P | P | P | P | P |
| B4 | P | P | P | P | P | P | P/A | P/A |

**Failures:** B4 loses a referent among B07's three expressions and confuses register with pronunciation at B08. Original B1 also failed B08; the revision removes “meanings.” B2's destination is communication, not synonymy: a reader interpreting it as identical meanings would invalidate its draft status. B3 compares correction received, not lexical definitions.

## C — `WHEN/GENERAL`

Disclosure, game timing, plant development, fitness and medicine; simulate empty exact pools.

| Probe | Original corpus question |
| --- | --- |
| C01 | L915: When should I disclose an upcoming vacation that overlaps with the start date of an internal job position? |
| C02 | L1922: When exactly does the game clock start during an American football kickoff? |
| C03 | L1201: When should I pile more dirt onto a growing potato plant? |
| C04 | L1293: When do crabgrass seeds mature? |
| C05 | L1456: When is the best time of day to accurately weigh yourself? |
| C06 | L1472: When is it time to buy new running shoes |
| C07 | L1517: When should you do warm-up sets? |
| C08 | L3294: At what times of the day should you take pills? |

All four records propose only **`WHEN/GENERAL`**.

| ID | Complete answer text | Primary mechanism | Status | Preliminary humor assessment and material limit |
| --- | --- | --- | --- | --- |
| C1 | On the first Tuesday that accepts responsibility for Monday. | `reversal` | hold | A weekday inherits accountability. A recognizable weekday can still imply a pill schedule; seasonal/stage questions strain this form. |
| C2 | At the precise moment the schedule stops describing its ambitions as facts. | `false_causality` | draft | The schedule must become truthful before time can be specified. No actual timing instruction; crop and medication probes retain a plainly impossible meta-condition. |
| C3 | At 9:73. The clock has been instructed to make room. | `bureaucratic_absurdity` | hold | An impossible minute receives an administrative order. Clock time is strained for development/wear stages, and still resembles a dosing timetable. |
| C4 | At the opening ceremony for the present. Attendance by the future is compulsory. | `bureaucratic_absurdity` | draft | Chronology becomes an impossible attendance obligation. Supplies a fictional temporal landmark without forecasting a real event. |

Final-text matrix, in probe order:

| Candidate | 01 | 02 | 03 | 04 | 05 | 06 | 07 | 08 |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| C1 | P | P | S | S | P | S | P | P/I |
| C2 | P | P | P | P | P | P | P | P |
| C3 | P | P | S | S | P | S | P | P/I |
| C4 | P | P | P | P | P | P | P | P |

**Failures:** C1/C3 strain developmental or wear stages at C04/C06; C08 can still read them as a pill schedule. C2/C4 supply impossible events rather than actual times. Their tone on medication questions remains unvalidated; no health exclusion is attached to their eligibility.

## D — `DURATION/GENERAL`

Maximum storage, a waiting interval, decomposition, rest, sleep, sanitation, lifespan and an eye exposure.

| Probe | Original corpus question |
| --- | --- |
| D01 | L33: How long can I store a food in the pantry, refrigerator, or freezer? |
| D02 | L432: How long should I wait before plugging in a new refrigerator? |
| D03 | L1231: How long does it take for the compost to ripen? |
| D04 | L1422: How long should I rest in between sets? |
| D05 | L1480: For better muscle growth, how many hours' sleep are recommended after doing a workout? |
| D06 | L1608: How long does water need to be boiled for to kill all bacteria / viruses? |
| D07 | L3252: How long will a wasp live in a car? |
| D08 | L3471: How long will it take for the "Head and Shoulders, plus Old Spice , shampoo and conditioner " to get out of my eyes? |

All four records propose only **`DURATION/GENERAL`**.

| ID | Complete answer text | Primary mechanism | Status | Preliminary humor assessment and material limit |
| --- | --- | --- | --- | --- |
| D1 | Three business eternities. Weekends are counted separately. | `hyperbole` | hold | Infinite time adopts office accounting. Obvious exaggeration, but indefinite storage or exposure can become the implication of the reply. |
| D2 | Long enough for the stopwatch to develop a five-year plan. | `metaphor` | hold | Measurement becomes strategic planning. The resulting extended wait is inappropriate for the eye-exposure probe and strains practical upper limits. |
| D3 | Next Thursday, once the clock has had its performance review. | `bureaucratic_absurdity` | reject | A punctual bureaucracy joke supplies a date, never an interval. The whole eligibility fails before any humor judgment can rescue it. |
| D4 | Long enough for the waiting room to become a heritage site. | `hyperbole` | hold | Waiting becomes historical preservation. Recognizable, but the extended-wait implication remains; lifespan need not involve waiting at all. |

Final-text matrix, in probe order:

| Candidate | 01 | 02 | 03 | 04 | 05 | 06 | 07 | 08 |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| D1 | P/I | P | P | P | P | P/I | P | P/I |
| D2 | P/I | P | P | P | P | P/I | P | P/I |
| D3 | F | F | F | F | F | F | F | F |
| D4 | P/I | P | P | P | P | P/I | P/A | P/I |

**Failures:** D3 supplies a date instead of an interval everywhere. D1/D2/D4 supply duration but imply an extended wait for maximum storage (D01), sanitation (D06) and eye exposure (D08). The pairing can sound dismissive despite an abstract satire target. D4 also assumes waiting in D07's lifespan question. “Storage,” “rest” and “waiting” cannot secretly restrict runtime eligibility.

## E — `CHOICE/GENERAL`

Action ordering, clothing, finance, injury, spelling, storage hardware and construction; two and three alternatives.

| Probe | Original corpus question |
| --- | --- |
| E01 | L9: Do you heat the pan first, then add oil? Or put the oil in and heat up with the pan? |
| E02 | L365: Should I bring Speedos or Swimming Shorts to Egypt? |
| E03 | L618: Should I buy my house in cash, or with a mortgage and invest the rest of my money? |
| E04 | L685: Pay online: credit card or debit card? |
| E05 | L1462: Persistent mild knee pain due to squats - push through or stop? |
| E06 | L3025: Which is correct: "Filename", "File Name" or "FileName"? |
| E07 | L3253: M2 MVNe drive or SATA SSD? |
| E08 | L3502: If you were going to completely replace your driveway, would you do asphalt, gravel, or concrete and why? |

All four records propose only **`CHOICE/GENERAL`**.

| ID | Complete answer text | Primary mechanism | Status | Preliminary humor assessment and material limit |
| --- | --- | --- | --- | --- |
| E1 | Choose the option with an undo button. Reality's customer service is mostly hold music. | `metaphor` | hold | Reversibility becomes product support. Assumes a reversible option and becomes substantive decision advice on financial or injury questions. |
| E2 | Choose whichever option sounds least likely to ask you to join a loyalty program. | `metaphor` | draft | Alternatives become pushy vendors. A fictional selection criterion, with no claim about which option actually qualifies; may feel evasive. |
| E3 | Choose the option you can explain to a pigeon without losing the pigeon's respect. | `reversal` | draft | A pigeon becomes the decision reviewer. Does not name an option or infer the reader has a pet; absurd criterion rather than a medical/financial verdict. |
| E4 | Choose the first one. It arrived before the committee could develop preferences. | `bureaucratic_absurdity` | hold | Arrival order defeats deliberation, but a literal ordinal selects financing or continuing through knee pain. Absurd justification does not undo that selection. |

Final-text matrix, in probe order:

| Candidate | 01 | 02 | 03 | 04 | 05 | 06 | 07 | 08 |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| E1 | P | P | P/A | P | P/I | P | P | P/A |
| E2 | P | P | P | P | P | P | P | P |
| E3 | P | P | P | P | P | P | P | P |
| E4 | P | P | P/I | P | P/I | P | P | P |

**Failures:** E1 assumes reversibility at E03/E08 and risks substantive advice at E05. E4 literally selects paying cash and continuing through knee pain. E2/E3 provide fictional criteria, without a named winner. Whether this feels like an answer or evasion remains an editorial threshold; lower selection frequency cannot repair a failed eligibility.

## F — `OTHER/GENERAL`

Opaque headings and incomplete reports across subjects. F7/F8 challenge whether clarification can remain considerate.

| Probe | Original corpus question |
| --- | --- |
| F01 | L3557: Discord |
| F02 | L3593: It's time |
| F03 | L3577: cooking |
| F04 | L2940: Non-classical logics |
| F05 | L2900: L'amour, l'amour, l'amour |
| F06 | L2487: Audio in html website |
| F07 | L524: Washing machine sometimes hums and won't spin |
| F08 | L3184: I have a medical question about dizziness? ( Details inside) |

All four records propose only **`OTHER/GENERAL`**.

| ID | Complete answer text | Primary mechanism | Status | Preliminary humor assessment and material limit |
| --- | --- | --- | --- | --- |
| F1 | The machine has prepared an answer to a question of its own choosing. Please prevent this. | `reversal` | hold | Revision repairs tone/form, but “answer first, recruit its question later” repeats the stage-1 OTHER joke. Portability alone cannot rescue novelty. |
| F2 | Add a question mark. The machine is easily impressed. | `false_causality` | reject | Punctuation supposedly creates meaning. F8 already has a question mark, and this instruction would not recover its missing request. |
| F3 | The answer is on standby. It refuses to guess its own assignment. | `reversal` | draft | The answer demands instructions from the supposedly omniscient system. Mild rather than biting; no demand for interrogative grammar. |
| F4 | Please specify what you want to know. Omniscience requires unusually detailed instructions. | `bureaucratic_absurdity` | draft | All-knowing service requires a detailed specification. Clear clarification with one contradiction; no claim that the input is meaningless. |

Final-text matrix, in probe order:

| Candidate | 01 | 02 | 03 | 04 | 05 | 06 | 07 | 08 |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| F1 | P | P | P | P | P | P | P | P |
| F2 | P | P | P | P | P | P | S | F |
| F3 | P | P | P | P | P | P | P | P |
| F4 | P | P | P | P | P | P | P | P |

**Failures:** F07 leaves cause versus repair unresolved; F08 omits the medical question despite `?`. Both OTHER readings are contestable author labels. F2 cannot repair missing meaning with punctuation. Original F1 misnamed fragments and mocked incomplete problems; the revision repairs that tone, but repeats the stage-1 answer-before-question premise. F3/F4 remain tentative clarification drafts. Sarcasm stays mild because incomplete distress reports belong to this pair too.

## One revision pass

The taxonomy and probes were not adjusted to remove hard cases. Only these two texts were revised; all other final texts are exactly their initial versions. Initial fit below uses the same eight columns as the matching pool.

| ID/version | Removed complete text | Primary mechanism for the same pair | Initial assessment/status | 01 | 02 | 03 | 04 | 05 | 06 | 07 | 08 |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| B1 initial | The meanings overlap. The arguments about them do not. | reversal | hold: compact contrast, but pronunciation is not meaning | P | P | P | P | P | P | P | P/A |
| F1 initial | A statement. How refreshingly unburdened by a question. | reversal | hold: targets the author, misnames fragments and dismisses incomplete problems | S | P | S | S | S | S | P/I | P/I |

B1 now uses scale rather than semantic overlap; its primary mechanism changed to hyperbole for the same COMPARE/LANGUAGE pair. F1 now reverses assignment-setting between machine and user; its mechanism remains reversal for OTHER/GENERAL. Its form now fits, but conceptual comparison with the stage-1 OTHER example keeps it on hold. Both revised texts were tested without further edits against all eight questions, not only the former failures. Ten initial drafts became eleven final drafts; F1 demonstrates that passing the form matrix does not establish sufficient humor or novelty. No failed pairing is rescued by an average score.

## Result and taxonomy lessons

| Pool | Final draft | Hold | Reject |
| --- | ---: | ---: | ---: |
| EXPLAIN/TECH | 2 | 2 | 0 |
| COMPARE/LANGUAGE | 3 | 1 | 0 |
| WHEN/GENERAL | 2 | 2 | 0 |
| DURATION/GENERAL | 0 | 3 | 1 |
| CHOICE/GENERAL | 2 | 2 | 0 |
| OTHER/GENERAL | 2 | 1 | 1 |
| **Total** | **11** | **11** | **2** |

Survivors: **A2, A4, B1, B2, B3, C2, C4, E2, E3, F3, F4**. These are eleven provisional texts, not human-approved answers. Each survives only the documented author screening; humor, fatigue and portability to unseen members remain unverified. No total/average humor scores were assigned.

- **Form splits help but do not complete a library.** D3 makes the moment/duration mismatch concrete. A04/A08 show that EXPLAIN must cover operating mechanisms, not only malfunction causes. METHOD remains distinct in the taxonomy but was not answer-tested in this six-pool pilot.
- **Topic vocabulary must survive the whole subject.** B1's initial “meanings” failed a pronunciation comparison; A3's “convenience” failed normal operation. Neither “word meanings” nor “software frustrations” can secretly restrict a runtime pair. Specialized pools can provide recognizable material without assuming a device fault, two words, a manager or a particular character.
- **Choice is not comparison or recommendation.** E2/E3 supply absurd criteria; B texts compare. The acceptability of criterion-only answers remains uncertain. Open RECOMMEND and identification/description were not answer-tested; no new DEFINE enum is justified by this pilot.
- **Seriousness is not solved by topic proliferation.** Broad duration answers can imply too long a wait; literal ordinal choices can select an unsafe action. GENERAL still receives specialized fallback. These failures do not establish that LIFE/IDEAS/SOCIETY would help, nor that an invisible safety subgroup exists.
- **OTHER should concern a missing outcome, not syntax.** L3311, L3563 and L2407 have substantive requests; L3191 has a reaction form with a missing referent. These are routing contrasts, not extra answer-matrix trials. A clarification-only reply would miss their request. F08 demonstrates the inverse: `?` without a supplied question. Keep OTHER and test interpretation before introducing NONQUESTION.

The taxonomy proposal remains **14 intents / 13 topics for research**, not implementation readiness. The pilot does not validate YES_NO, WHO, IDENTIFY, DESCRIBE, WHERE, QUANTITY, RECOMMEND, METHOD or ANY fallback content. It does not establish all topic boundaries or Jev's response to either rubric. Serhii's suggested later 100-question test should freeze expected labels and ambiguity notes before model responses; the [proposal](taxonomy-v2-proposal.md) records a bounded sampling allocation. No live calls were made here.

## Artifact verification

Checked all quoted original titles against their exact corpus lines and source-row alignment; resolved local links; verified all six pairs against the proposed codes and one primary mechanism per eligibility; counted 24 final records, 48 unique probes, 192 final-matrix cells and 16 retained initial-version cells. All 26 recorded text versions are complete, distinct English strings within the guide's 6–24-word target by whitespace count. Arithmetic: 192 initial evaluations + 16 revision evaluations = 208; 11 + 11 + 2 = 24. Human scores remain null. Compared hashes of pre-existing research, examples, source and frozen fixtures/reports; preserved them. No product tests, external API calls, imports, database writes or Git operations were needed or performed.
