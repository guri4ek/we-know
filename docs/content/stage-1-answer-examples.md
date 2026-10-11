# Приклади форми відповіді для етапу 1

Дата: 2026-10-11. Прочитано завершені [дослідження гумору](../research/humor-mechanisms.md) та [guide](answer-generation-guide.md). Їхні файли й drafts не змінювалися. Нижче нові авторські ілюстрації до [taxonomy v1](taxonomy-v1.md), **не каталог, seed чи approved eligibility**. Human scores — `null`; усі кандидати мають `hold` до людської редактури й broader probes. Jev їх не отримує.

Кожен рядок означає лише запропоновану пару `(intent, GENERAL)` та один primary `ANSWER_ELIGIBILITY.humor_mechanism`. Механізм не є полем ANSWER і не впливає на майбутній uniform random. Це текстовий документ, не новий import/schema contract.

| Intent | Draft answer | Primary mechanism | Три різні fit probes |
| --- | --- | --- | --- |
| YES_NO | Yes. The objection has been promoted to supporting evidence. | reversal | Is Monday compulsory? / Can a calendar hold a grudge? / Is my browser in charge? |
| WHEN | After the calendar receives permission to proceed. | bureaucratic_absurdity | When will the update arrive? / When will the meeting end? / When will adulthood begin? |
| WHY | Because the explanation submitted its conclusion before its evidence. | false_causality | Why are meetings endless? / Why do apps need accounts? / Why is progress slow? |
| HOW | Appoint a supervisor for the first step. The remaining steps will request supervisors. | bureaucratic_absurdity | How do I organize a project? / How do I learn a tool? / How do I establish a routine? |
| WHO | The calendar. It has already assigned everyone else a supporting role. | reversal | Who invented meetings? / Who owns Monday? / Who runs the office? |
| WHAT | A subscription to the possibility of completion. Cancellation requires completion. | metaphor | What is productivity? / What is progress? / What is adulthood? |
| HOW_MANY | One infinity, rounded up to the nearest committee. | hyperbole | How many meetings are enough? / How many tabs do I need? / How many hobbies count as a personality? |
| CHOICE | The option with less paperwork. Indecision has already opened a second office. | bureaucratic_absurdity | Tea or coffee? / A notebook or a spreadsheet? / Morning or afternoon meetings? |
| OTHER | The conclusion is ready. A suitable question will be recruited shortly. | reversal | Monday again. / Another meeting appeared. / My browser has ambitions. |
| ANY | Certainty has been issued. Please retain the doubt as proof of purchase. | metaphor | When will this end? / Who invented Monday? / Tea or coffee? |

Universal draft — тільки `(ANY, GENERAL)`. Втрата форми в цьому tier — майбутня явна деградація; OTHER лишається звичайною класифікованою формою.

**Відомі негативні probes, не виконувані exclusions:** WHEN-фраза не дає тривалості до `How long will this last?`; WHO-фраза не відповідає до `Who should I marry?`; WHAT-фраза не підходить до `What should I cook?`. YES_NO і HOW_MANY потребують окремого відсіву на HEALTH/MONEY, де форма може звучати як реальна порада. Тому позитивні probes не доводять придатності для цілого пулу. Не імпортувати ці широкі GENERAL eligibility без виправлення/відхилення; не вводити runtime-фільтри, нові labels чи mechanism weights для порятунку прикладів.

Незалежної оцінки доречності, смішності й втоми від повторів не було. Production контент і його імпорт належать до окремого етапу; цей документ лише показує форму й evidence gaps.
