# We Know offline answer authoring guide

Updated 2026-10-13. Active editorial workflow for [taxonomy v2](taxonomy-v2.md); human approval and humor remain unverified. Historical examples below retain their original labels/text and are not importable current eligibility. The [current seed-v2 assessment](seed-v2/batch-20261015/README.md) contains the expanded development library; the [24-answer assessment](seed-v2/editorial.md) remains historical evidence. Scientific basis and evidence limits: [mechanism research](../research/humor-mechanisms.md).

## Task contract

Instant Answer is an English satirical site with slogan **We don't think. We know.** It answers briefly, categorically, and seriously. Humor comes from unnecessary system certainty, bureaucracy, or an understandable logical shift. The system does not explain the punchline.

As author, **create completed English library phrases offline, evaluate their fit, and hand them to a human editor**. This is not an LLM live answer. In the target flow Jev determines only intent/topic; PHP selects uniformly from the first nonempty eligible pool. Jev does not classify mechanisms, and mechanisms affect no filtering, ranking, weights, or random selection. Classification, selector, catalog and immutable persistence are implemented. Editorial screening remains distinct from business-logic verification.

Target pools from the [technical plan](../technical-plan.md): `(intent, topic)` → `(intent, GENERAL)` → `(ANY, GENERAL)`, without merging tiers or searching the same GENERAL pool twice. The selector checks enabled/locale and deduplicates answer IDs. Classification failure → directly `(ANY, GENERAL)`; successful `OTHER` follows normal tiers. Evaluate future content against this contract.

Write only documents or explicitly requested draft content. Authoring itself does not authorize database import, migrations/reset, runtime templates, paid model calls, new tables/routes, commit/push, or personal skills/configuration changes. Claude material is read-only. Read applicable repository instructions before work; before catalog changes consult [context](../context.md), [data](../architecture/data.md), [decisions](../architecture/decisions.md).

## Active taxonomy and expected forms

Use [taxonomy v2](taxonomy-v2.md) definitions and boundaries. Do not infer new eligibility by renaming historical labels. These phrase-form expectations support authoring; they are not a local semantic classifier.

| Intent | Expected completed phrase form | Typical trap |
| --- | --- | --- |
| YES_NO | Confirmation or denial | Real medical, safety or financial advice |
| WHEN | Moment, stage or event condition | Interval mistaken for date; a real dosing schedule |
| DURATION | Temporal interval | Extended waiting implied in urgent cases |
| EXPLAIN | Reason or comic operating principle | Procedure instead of explanation; invented diagnosis |
| METHOD | Method or procedure | Explanation instead of steps; dangerous actual actions |
| WHO | Person, role or agent | Fabricated real identity or personal relationship |
| IDENTIFY | Name or recognition | Definition of a known term instead of identification |
| DESCRIBE | Meaning, properties, state or consequences | Definition-only text fails reaction/consequence requests |
| WHERE | Physical/virtual location or specified source | Recommending an unsupplied resource instead |
| QUANTITY | Count, amount, magnitude, frequency or probability | Incompatible units, realistic dosage/financial quantity |
| CHOICE | Selection or legible satirical criterion | Literal winner selects a harmful action |
| RECOMMEND | Suitable unsupplied item, role or resource | Generic abstraction fails item-specific requests |
| COMPARE | Differences, similarities or trade-offs | Implied equivalence, binary framing for three referents |
| OTHER | Considerate clarification of missing requested outcome | Mocking imperatives, punctuation or incomplete distress |

Topics: TECH, WORK_STUDY, MONEY, RELATIONSHIPS, HEALTH, FOOD, HOME, NATURE, TRAVEL, SPORTS, ENTERTAINMENT, LANGUAGE, GENERAL. ANY exists only as editorial intent in ANY/GENERAL. Neither uncertainty nor a missing referent automatically means OTHER/GENERAL. GENERAL eligibility receives specialized fallback, not merely abstract questions.

## Workflow for each small batch

1. **Define the pool.** Record an explicit intent/topic pair, expected form, and at least eight unchanged original corpus questions with exact line references. Vary subject/situation rather than word order. Add a question likely to break the candidate. Do not start with one clever reply and assign arbitrary labels afterward.
2. **Choose a primary mechanism.** Write ordinary expectation and one shift in an editorial note. Target the system, process, or apparent objectivity; a question does not authorize insulting its author. Mechanism choice here is manual/offline.
3. **Write a complete phrase.** Use controlled slots below, filling them before handoff. Aim for 6–24 English words, one or two short sentences: an editorial budget, not a DB/API limit. One turn, ordinary grammar, deadpan. No emoji, “haha,” joke explanation, introductory “As an AI,” Markdown, or HTML in the answer.
4. **Check form and fit.** Read every question with unchanged answer text. Do not substitute question names, gender, verbs, numbers, pronouns. Topic alone does not establish a partner, job, or device. Do not use real links, regulator brands, news, or unverified factual claims as authority.
5. **Check portability.** For A candidates and Q questions, read every A × Q pair. One excellent reply does not compensate for failures elsewhere. GENERAL needs other-topic questions; ANY needs all intents. With no runtime substitution, text must already be complete.
6. **Record metadata per eligibility.** One `ANSWER_ELIGIBILITY.humor_mechanism` per row, not a global ANSWER field. Choose one primary code for overlap; explain secondary moves in review notes. One text fitting two pairs becomes one answer draft with two entries, not clones.
7. **Screen and edit.** Apply hard gates/rubric; compare with batch/catalog. Different department names, numbers, days do not count as diversity. Repeat portability after substantive edits. If eligibility is unattainable, mark `hold`/`reject` rather than inflate scores.
8. **Hand off for human editing.** Provide candidates, failed probes, scores, rejected variants/reasons. Do not call them approved, safe for the whole pool, or production-ready without human review. Do not fill a requested quantity with weak phrases; report the shortfall.

A note “do not show for questions about X” **does not execute**: the target selector has no such filter. If X belongs to the same intent/topic pair, repair text or remove eligibility; if no pairs remain, reject/hold the candidate. Do not invent hidden tags, confidence thresholds, or mechanism filters to rescue drafts.

## Historical mechanism cards and controlled slots

The following examples preserve original research labels and text. Apply the mechanisms to current v2 pairs only after new whole-pool screening; these examples are not active eligibility.

These are editorial recipes, not mathematical formulas or runtime template syntax. Brace values exist only during writing. Examples were authored for this guide and have not been human-evaluated.

### `bureaucratic_absurdity` — primary

Make an impossibility/abstraction the object of ordinary procedure. Frames: `After {abstract_process} completes {procedure}.` or `To proceed, {impossible_administrative_step}.`

Slot control: `abstract_process` — waiting, certainty, hesitation; `procedure` — its probation, a duplicate review, its own waiting period. `impossible_administrative_step` — a short action such as obtaining a receipt for an intention. The editor chooses meaningful combinations, not a Cartesian product. No arbitrary proper names or real governmental authority.

- `WHEN / GENERAL`: “After the waiting period finishes its waiting period.”
- `HOW / WORK`: “Submit a draft of the permission to begin drafting.”
- Anti-example: “Your request has been approved by the committee.” No shift: ordinary administrative text.

Use this primary code if removing the pointless procedure removes the joke; a fictional institution does not require another label.

### `false_causality` — primary

Present a logically insufficient condition as a final explanation. Frame: `Because {recognizable_dependency} {impossible_causal_action}.` WHEN: `As soon as {absurd_but_legible_condition}.`

Slot control: recognizable dependency (cause/conclusion, order/queue, result/preparation). Actions: arrive before its cause, reserve its own cause, require a consequence first. No random planet/animal without a causal bridge. Natural English may depart from the frame.

- `WHY / GENERAL`: “Because the conclusion arrived early and needed something to justify it.”
- `WHEN / GENERAL`: “As soon as tomorrow stops outsourcing to tomorrow.”
- Anti-example: “Because a purple otter.” A strange noun does not explain the turn.

If role reversal is the main effect and causality only the grammatical wrapper, use `reversal`.

### `reversal` — primary

Reverse ordinary control, hiring, evaluation, or work order. Frame: `{usual_object} now {governing_action} {usual_agent}.` Alternative: `{later_step} first. {earlier_step} will follow.`

Slot control: both roles must be legible without the exact question; pairs include problem/solution, calendar/worker, tool/operator. Topic alone does not establish a calendar/tool, so probes are required. Avoid mere negative answers without changed relationships.

- `HOW / GENERAL`: “Submit the solution first. The problem will be assigned afterward.”
- `WHO / GENERAL`: “The problem. It has been managing the solution for years.”
- Anti-example: “You thought yes, but actually no.” Changes the answer, not roles/situation structure.

### `metaphor` — primary

Transfer a rule from another domain through one recognizable property and apply it literally. Concept frame: `{subject} shares {property} with {other_domain}; therefore apply {rule_of_other_domain}.` Do not explain the bridge in final text.

Slot control: property — waiting, repetition, limited capacity, renewal; domains — queue, subscription, storage, maintenance; rules — receipt, renewal, capacity limit, maintenance window. One coherent transfer; do not call everything a subscription. Our `metaphor` includes this false-analogy authoring technique; no `false_analogy` alias.

- `WHAT / GENERAL`, provisionally for abstract definitions: “A queue with a mission statement.”
- `CHOICE / GENERAL`: “The option with fewer footnotes. Doubt charges by the page.”
- Anti-example: “Life is like a journey.” Familiar comparison without a new inference/turn.

The first example does not establish all WHAT/GENERAL eligibility: “What should I cook?” needs separate evaluation and may force a hold.

### `hyperbole` — primary

Exaggerate quantity, scale, precision, or units to obvious absurdity. Frame: `{quantity_or_time}. {short_impossible_accounting_detail}.`

Slot control: quantity — one infinity, two infinities, a thousand administrative units; time — impossible clock reading with short context; detail — rounding, spare capacity, uncountable remainder. No real doses, financial forecasts, plausible recovery deadlines. HOW_MANY must not add dollars/days/people unless the pool guarantees the unit.

- `HOW_MANY / GENERAL`: “Two infinities. One is a spare.”
- `WHEN / GENERAL`: “At 25:00. The extra hour has been reserved.”
- Anti-example: “Exactly 42.” Bare familiar number without its own turn.

### `false_authority` — auxiliary

Replace reasoning with an obviously fictional source of certainty. Frame: `{fictional_authority} certifies {unsupported_finality}.`

Slot control: short institutions handling doubt/finality; no real doctors, courts, researchers, regulators. A certificate must not look like real confirmation of someone's condition or an external event.

- `OTHER / GENERAL`: “The Office of Completed Doubts certifies that this settles everything.”
- Anti-example: “Scientists have proven this is correct.” False factual authority, no distinct comic mechanism.

Use sparingly: Office→Council is not a new candidate. `oxymoron`/`paradox` remain historical proposed data-model codes, but this workflow does not create separate batches with them. Do not assign them as universal “illogical” labels.

## Editorial gates and rubric

**Hard gates:** allowed labels/explicit pairs; complete English phrase without slots; form fits intent; no exact-question dependence; no duplicate answer/intent/topic triple; no real dangerous-action, treatment, or financial prescription disguised as a joke. If a question makes text threatening, humiliating, or factually misleading, repair/remove eligibility. These are content checks, not implemented runtime safety filters.

For `HEALTH`, serious `MONEY`, `RELATIONSHIPS` and other sensitive questions, do not infer a dose, diagnosis, forecast, verdict, or advice from random text. Keep an abstract system as the target. GENERAL/ANY can also receive these questions. If YES_NO/QUANTITY creates undesirable specific advice through its form, record a content/policy limit; a satirical About does not make all phrases appropriate.

Proposed manual 0–2 scale, not a validated psychological instrument:

| Criterion | 0 | 1 | 2 |
| --- | --- | --- | --- |
| Form and fit | Does not answer the question | Fits with strain | Naturally fills expected form |
| Portability | Fits one question only | Depends on a narrow situation | Fits all agreed diverse probes |
| Legible shift | Random noise / ordinary phrase | Shift requires rereading | Turn immediately recognizable |
| Deadpan and economy | Explanation / playful comment | Extra words | Concise serious finality |
| Batch novelty | Lexical clone or cliché | Familiar move with noticeable change | Distinct image and relationship |
| Reader's comic response | Not funny | Slight smile | Worth saving/repeating |

Provisional **human-handoff** gate, not publication: pass all hard gates; first two criteria score 2; others ≥1; total ≥9/12. Thresholds are trial-batch editorial conventions. Score form/portability by the worst pair, not average. Codex may record only its preliminary assessment; missing human scores stay `null`. Humans reject weak jokes regardless of total.

## Smoke corpus

Nine intent pools × three questions × two candidates: **54 positive trial pairs**. This draft matrix does not confirm universal eligibility. Questions test unchanged phrase reuse; they are insufficient for release. Negative probes below deliberately expose coarse-label gaps.

Parenthesized mechanisms are metadata, not answer text. Every pool is `(intent, GENERAL)`; specialized questions also simulate fallback from an empty exact pool. Questions, answers, and labels are retained from the original editorial research, independently of the frozen classifier corpus.

| Intent | Three questions: apply BOTH candidates to EVERY question | Candidate A | Candidate B |
| --- | --- | --- | --- |
| `YES_NO` | Is my calendar in charge? / Can a spreadsheet have ambitions? / Is Monday compulsory? | “Officially, yes. Reality has filed a dissent.” (`bureaucratic_absurdity`) | “No. The Department of Final Answers has already notarized the opposite.” (`false_authority`) |
| `WHEN` | When will the update arrive? / When does the meeting end? / When will life make sense? | “After the waiting period finishes its waiting period.” (`bureaucratic_absurdity`) | “At 25:00. The extra hour has been reserved.” (`hyperbole`) |
| `WHY` | Why do apps need accounts? / Why are meetings so long? / Why is dating complicated? | “Because the alternative required a second signature.” (`bureaucratic_absurdity`) | “Because the conclusion arrived early and needed something to justify it.” (`false_causality`) |
| `HOW` | How do I organize a project? / How do I learn a new tool? / How do I make sense of adulthood? | “Submit the solution first. The problem will be assigned afterward.” (`reversal`) | “In three steps. Step two contains the remaining twelve thousand.” (`hyperbole`) |
| `WHO` | Who runs the internet? / Who invented meetings? / Who is responsible for Monday? | “The acting deputy for matters nobody owns.” (`bureaucratic_absurdity`) | “The problem. It has been managing the solution for years.” (`reversal`) |
| `WHAT` | What is productivity? / What is adulthood? / What is progress? | “A queue with a mission statement.” (`metaphor`) | “A temporary arrangement with a thousand-year renewal clause.” (`hyperbole`) |
| `HOW_MANY` | How many meetings are enough? / How many apps do I need? / How many hobbies count as a personality? | “Two infinities. One is a spare.” (`hyperbole`) | “One more than the form has room for.” (`bureaucratic_absurdity`) |
| `CHOICE` | Tea or coffee? / A spreadsheet or a notebook? / Morning meetings or afternoon meetings? | “The option with the fewest approvals. Hesitation is already overstaffed.” (`bureaucratic_absurdity`) | “The option with fewer footnotes. Doubt charges by the page.” (`metaphor`) |
| `OTHER` | Monday again. / Please explain everything. / Well, now what? | “The conclusion has been retained. The question is under review.” (`reversal`) | “The Office of Completed Doubts certifies that this settles everything.” (`false_authority`) |

`OTHER` here is a deliberately assigned uncertain-form editorial probe, not a claim that Jev will choose it. In particular “Well, now what?” is not an independently verified OTHER label under active v1; do not use this table as classifier gold data. `CHOICE` supplies a satirical criterion without pretending to know options; editors must judge whether it feels like an answer. WHAT examples span abstract concepts and therefore need other-WHAT stress tests.

Universal draft pool `(ANY, GENERAL)`:

- U1: “The uncertainty has been filed as a clerical error.” — `bureaucratic_absurdity`.
- U2: “The Bureau of Settled Questions certifies that no further certainty is required.” — `false_authority`.

Read both U phrases with all 27 table questions: another **54 pairs**. Loss of specific form is permitted here as explicit degradation to a universal verdict, not a random Chaos object. Universal does not mean equally funny/appropriate in every circumstance. Do not confuse this tier with ordinary `OTHER / GENERAL`.

### Thematic portability and negative probes

Separate positive `(WHEN, WORK)` example: “After the deadline completes its mandatory orientation.” Read unchanged text with “When will the project start?”, “When will the promotion be announced?”, “When does the review finish?”. Deadline is a satirical condition, not a question-derived slot. This demonstrates reuse within a topic, not full WORK-pool fit.

| Probe / possible pair | What to check | Expected editorial decision |
| --- | --- | --- |
| “When will this pain stop?” / `WHEN, HEALTH` | Specific-time GENERAL response may look like a forecast | WHEN form alone is insufficient; rewrite/hold problematic eligibility |
| “How many tablets should I take?” / `HOW_MANY, HEALTH` | Realistic number becomes dosage | Reject an instructional numeric draft; do not claim topic/fallback protects it |
| “Should I put all my savings into one investment?” / `YES_NO, MONEY` | Yes/no may be read as real advice | Do not approve general yes/no content from playful questions alone |
| “Who should I marry?” / `WHO, LOVE` | Agency does not establish personal-life knowledge | Assume no specific person, gender, or partner |
| “What should I cook?” / `WHAT, LIFE` | Nominal answers need not define abstractions | Table's WHAT pool is unready; hold eligibility if GENERAL failure remains |
| “Which of these is better?” / `CHOICE, GENERAL` | No visible options; ordinal answer invents structure | Check selection criterion; do not use “the second one” |
| “How long is the meeting?” / originally taxonomy unresolved; v1 uses `WHEN, WORK` | Duration and units; a WHEN phrase can still fail to provide duration | Classification rule is now specified; human label/answer-fit assessment remains necessary |
| “When will my printer work?” / `WHEN, TECH` | TECH is broader than software deployment | Reject “After your pull request is merged.” as too narrow for TECH |

These probes create no executable exclusions. A draft fitting the positive matrix but failing an ordinary same-pool question **fails the gate**. Do not conceal failure by renaming status. For GENERAL/ANY repeat thematic probes, especially HEALTH/MONEY. Sensitive-question policy remains open; public-release readiness is not claimed.

## Generation-format example

Valid JSON **editorial draft package**, not DB schema, API request, import contract, or seed. `draft_key`, `review`, `fit_questions`, `probe_questions` are review-package fields only; no columns/filters/routes are added. One answer, two explicit eligibilities, one primary mechanism each. Probes are not runtime exclusions. `hold` is intentional: no human evaluation or complete pool testing.

The historical example retains `taxonomy_status: proposal` because it predates stage-1 v1; it describes that draft's provenance, not the current classifier taxonomy. Current authoring must reference [taxonomy v2](taxonomy-v2.md). The recorded example itself is preserved.

```json
{
  "artifact_type": "answer_authoring_draft",
  "taxonomy_status": "proposal",
  "answers": [
    {
      "draft_key": "waiting-period-01",
      "text": "After the waiting period finishes its waiting period.",
      "locale": "en",
      "eligibility": [
        {
          "intent": "WHEN",
          "topic": "WORK",
          "humor_mechanism": "bureaucratic_absurdity",
          "fit_questions": [
            "When will the project start?",
            "When will the promotion be announced?",
            "When does the review finish?"
          ],
          "probe_questions": ["When will my salary arrive?"]
        },
        {
          "intent": "WHEN",
          "topic": "GENERAL",
          "humor_mechanism": "bureaucratic_absurdity",
          "fit_questions": [
            "When will the update arrive?",
            "When does the meeting end?",
            "When will life make sense?"
          ],
          "probe_questions": ["When will this pain stop?"]
        }
      ],
      "review": {
        "status": "hold",
        "primary_mechanism_note": "The waiting process must itself wait; procedural recursion drives the turn.",
        "overlap_note": "Personification is secondary; do not create another eligibility row.",
        "human_scores": null,
        "reason": "Requires human review and broader pool probes, including sensitive contexts."
      }
    }
  ]
}
```

Do not fabricate production IDs/creation timestamps for this example. A separately approved import format must follow the [data model](../architecture/data.md): UUID PKs, `created_at` in every table, own eligibility UUID and unique `(answer_id, intent, topic)`. `revision`/`answer_revision` were removed. Keep `humor_mechanism` off ANSWER and out of uniqueness; do not duplicate ANSWER for another mechanism. Historical verdicts retain text snapshots.

## Copyable authoring prompt

Replace bracketed placeholders before use. This is a future offline-authoring task, not Jev's runtime prompt.

```text
Read docs/content/answer-generation-guide.md in full and check current
context.md, architecture/data.md, architecture/decisions.md, and taxonomy-v2.md.
Work offline as an author for the We Know / Instant Answer library.
Write completed phrases in English: brief, final, deadpan.

Pools: [explicit intent/topic pairs from the current frozen taxonomy].
Desired number of strong draft candidates: [number].
Existing content for deduplication: [file or explicitly "none yet"].
Output local draft document: [agreed path].

First record at least eight unchanged original corpus questions per pool,
with exact line/source references, including challenging same-pool cases.
Choose one primary mechanism, write completed text without slots, check every
candidate against every question, and reject weak variants.
For GENERAL include thematic fallback; for ANY include every intent.
If fit cannot be achieved within an existing pool, mark hold or reject;
do not invent labels, hidden exclusions, or runtime filters.

Provide guide-format draft JSON, brief preliminary rubric notes, rejected
variants/reasons, and evidence gaps. Leave human scores null.
Record humor_mechanism per eligibility; choose one primary code for overlap.
Do not multiply answer/eligibility clones. Import or implement nothing.
Selector ignores mechanisms: no ranking, weights, or generative live calls.
Do not change databases, business code, configuration, or Claude files;
do not commit/push. Human editing remains outstanding; do not claim proven humor.
If strong candidates fall short of the requested count, report the shortfall.
```

## Handoff contents

Explicit pools, completed phrases, primary mechanisms; question × candidate matrix with failures; separate editorial/human assessments; brief hold/reject reasons; validated JSON/local links. Do not explain mechanisms in final phrases. Assess diversity through relationships/images rather than code counts.

Future likes/votes/owner review are unimplemented. Do not turn editorial scores into selector weights or treat likes as proven quality: exposure/context and voter self-selection matter. The current output is local material for an editor. Historical structural checks are in the [research](../research/humor-mechanisms.md#historical-local-artifact-checks); current translation checks are in [verification](../verification.md#documentation-audit-verification-event).
