# Інструкція офлайн-авторингу відповідей We Know

Дата: 2026-10-11. Статус: **proposed editorial guide** для наступної сесії Codex і людського редактора. Приклади — draft content, не production-ready seed. Таксономія, mechanism codes, rubric і пороги нижче є пропозицією. Наукове обґрунтування й межі перевірки — [дослідження механізмів](../research/humor-mechanisms.md).

## Контракт завдання

Instant Answer — англомовний сатиричний сайт зі слоганом **We don't think. We know.** Він відповідає коротко, безапеляційно та серйозно. Гумор виникає з непотрібної впевненості системи, бюрократії або зрозумілого логічного зсуву. Система не пояснює punchline.

Твоя задача як автора — **офлайн створити готові англійські фрази для бібліотеки, перевірити їхню придатність і передати людині на редактуру**. Це не live-відповідь від LLM. У цільовому потоці Jev визначає лише intent/topic, PHP бере uniform random із першого непорожнього допустимого пулу. Механізм не класифікується Jev і не впливає на filtering, ranking, ваги чи random. На початку цього дослідження документація фіксувала лише skeleton; цей guide описує контентний контракт, а не засвідчує поточну бізнес-реалізацію.

Пули за [технічним планом](../technical-plan.md): `(intent, topic)` → `(intent, GENERAL)` → `(ANY, GENERAL)`, без об’єднання tiers і без повторення однакового GENERAL-пулу. Selector враховує активність та locale, дедуплікує answer IDs. При failure класифікації — одразу `(ANY, GENERAL)`; успішний `OTHER` проходить звичайні tiers. Перевіряти майбутній контент з урахуванням цього контракту.

Пиши лише документи або явно замовлений draft контенту. Авторинг сам по собі не дозволяє імпорт у БД, міграції, reset, runtime-шаблони, платні model calls, нові таблиці/маршрути, commit/push або зміни особистих skills/config. Claude files залишаються read-only. Перед роботою прочитай застосовні інструкції репозиторію; перед зміною каталогу звір [контекст](../context.md), [дані](../architecture/data.md) і [рішення](../architecture/decisions.md).

## Таксономія: поточна пропозиція

Не створюй нових labels у відповідях чи draft JSON. Якщо таксономія заважає якісній придатності, познач evidence gap у звіті.

| Intent | Очікувана форма фрази | Типова пастка |
| --- | --- | --- |
| `YES_NO` | Впевнене підтвердження/заперечення з одним зсувом | Підтвердження, яке прочитується як реальна небезпечна порада |
| `WHEN` | Час, момент або умова настання | Кількість без часової форми; залежність від сьогоднішньої дати |
| `WHY` | Причина або удавана причина | Перефразоване питання без нового зв’язку |
| `HOW` | Спосіб, процедура, послідовність | Просто результат замість способу |
| `WHO` | Особа, роль, агент або вигадана відповідальна установа | Предмет без зрозумілої ролі агента |
| `WHAT` | Назва, визначення або номінальна відповідь | Вузьке визначення, яке не працює з іншим значенням WHAT |
| `HOW_MANY` | Кількість/міра з контрольованою одиницею або без неї | «Три долари» до людей, годин чи повторів |
| `CHOICE` | Вибір або зрозумілий сатиричний критерій вибору | «Другий» за відсутності другого; вигадана конкретна опція |
| `OTHER` | Самодостатній вердикт без припущення про форму питання | Потай трактувати всі OTHER як WHY або YES_NO |

Topics: `TECH`, `WORK`, `MONEY`, `LOVE`, `HEALTH`, `LIFE`, `GENERAL`. `ANY` дозволено лише як редакторський intent універсальної відповіді, не як label Jev; у поточному fallback це пара `(ANY, GENERAL)`. Не вигадуй `(ANY, TECH)` або topic `ANY`.

Тривалість (`WHEN` чи `HOW_MANY`), складені питання і межі `CHOICE` ще не затверджені. У smoke corpus нижче labels задані людиною для редакторської перевірки, а не отримані від Jev. `GENERAL` охоплює також fallback із тематичних питань; це не синонім «лише філософські питання».

## Workflow для кожного малого batch

1. **Визнач пул.** Запиши явну пару intent/topic, очікувану форму і щонайменше три різні питання. Вони мають відрізнятися предметом або ситуацією, а не тільки порядком слів. Додай питання, на якому кандидат найімовірніше зламається. Не починай із одного дотепного reply, якому потім довільно роздаєш labels.
2. **Вибери primary mechanism.** Сформулюй нормальне очікування і один зсув в окремій редакторській нотатці. Мішень сатири — система, процес, удавана об’єктивність; питання не слугує дозволом ображати користувача. Вибір механізму тут ручний/offline.
3. **Склади завершену фразу.** Використовуй контрольовані slots із карток нижче, але заповни їх до передачі результату. Орієнтир: 6–24 англійські слова, одна або дві короткі фрази; це редакторський бюджет, не DB/API limit. Один поворот, звичайна граматика, deadpan. Немає emoji, «haha», пояснення жарту, вступного «As an AI», markdown чи HTML у тексті відповіді.
4. **Перевір форму та доречність.** Прочитай кожне питання разом із незмінним текстом. Не підставляй назву, стать, дієслово, число чи pronoun із питання. Не припускай партнера, посади або пристрою лише за topic. Не використовуй реальні посилання, бренди-регулятори, новини чи неперевірені фактичні твердження як авторитет.
5. **Перевір переносимість.** Для пулу з A кандидатами та Q питаннями прочитай усі A × Q поєднання. Один чудовий reply не компенсує непридатні решту. Для GENERAL додай питання інших topics; для ANY — усі intents. Без runtime-підстановки фраза має бути завершеною вже зараз.
6. **Запиши metadata на кожну придатність.** Один `ANSWER_ELIGIBILITY.humor_mechanism` на eligibility row, не глобальне поле ANSWER. Overlap позначай одним primary code; другорядний хід можна описати в review notes. Той самий текст для двох справді придатних пар — один answer draft із двома eligibility entries, не два клони.
7. **Відсій і відредагуй.** Застосуй hard gates та rubric; порівняй з іншими текстами batch і наявним каталогом. Зміна назви відділу, числа або дня не рахується різноманіттям. Повтори переносимість після змістовної правки. Якщо вимога придатності недосяжна — познач `hold` або `reject`, не прикрашай оцінку.
8. **Передай на людську редактуру.** Видай кандидати, невдалі probes, оцінки й відхилені варіанти з короткою причиною. Не називай їх approved, safe для всього пулу чи production-ready без відповідної перевірки людиною. Не заповнюй замовлену кількість слабкими фразами: повідом дефіцит.

Примітка «не показувати до питань про X» сама по собі **не працює**: selector не має такого фільтра. Якщо X належить до тієї самої intent/topic пари, виправ текст або зніми цю eligibility; якщо придатних пар не лишилося — відхили чи відклади кандидат. Не додавай приховані tags, confidence thresholds або механізм-фільтр для порятунку draft.

## Картки механізмів і контрольовані slots

Це редакторські рецепти, не математичні формули й не runtime template syntax. Значення у фігурних дужках існують лише під час написання. Приклади нижче оригінально складені для цього guide; вони не є результатом людського evaluation.

### `bureaucratic_absurdity` — основний

Перетворити неможливість або абстракцію на об’єкт звичайної процедури. Каркаси: `After {abstract_process} completes {procedure}.` або `To proceed, {impossible_administrative_step}.`

Контроль slots: `abstract_process` — waiting, certainty, hesitation; `procedure` — its probation, a duplicate review, its own waiting period. `impossible_administrative_step` — коротка дія на кшталт obtaining a receipt for an intention. Поєднання обирає редактор за сенсом, не декартовий добуток словників. Заборонені довільні власні назви та реальні владні повноваження.

- `WHEN / GENERAL`: “After the waiting period finishes its waiting period.”
- `HOW / WORK`: “Submit a draft of the permission to begin drafting.”
- Anti-example: “Your request has been approved by the committee.” Немає зсуву: це звичайний адміністративний текст.

Primary code саме цей, якщо жарт зникає після вилучення безглуздої процедури; наявність вигаданої установи не потребує другої мітки.

### `false_causality` — основний

Подати логічно недостатню умову як остаточне пояснення. Каркас: `Because {recognizable_dependency} {impossible_causal_action}.` Для WHEN: `As soon as {absurd_but_legible_condition}.`

Контроль slots: залежність має бути впізнаваною (причина/висновок, порядок/черга, результат/підготовка). Дозволені дії: arrive before its cause, reserve its own cause, require a consequence first. Не вставляй випадкову планету чи тварину без причинного містка. Формулювання може відходити від каркаса заради природної англійської.

- `WHY / GENERAL`: “Because the conclusion arrived early and needed something to justify it.”
- `WHEN / GENERAL`: “As soon as tomorrow stops outsourcing to tomorrow.”
- Anti-example: “Because a purple otter.” Незвичний іменник не пояснює поворот.

Якщо основний ефект — перестановка ролей, а причина лише граматична оболонка, використовуй `reversal`.

### `reversal` — основний

Змінити звичний напрямок контролю, найму, оцінки або порядку роботи. Каркас: `{usual_object} now {governing_action} {usual_agent}.` Другий варіант: `{later_step} first. {earlier_step} will follow.`

Контроль slots: обидві ролі мусять читатися без точного питання; дозволені пари problem/solution, calendar/worker, tool/operator. Topic сам по собі не гарантує, що в питанні є саме calendar чи tool, тому потрібні probes. Уникай простої заперечної відповіді без зміни відношення.

- `HOW / GENERAL`: “Submit the solution first. The problem will be assigned afterward.”
- `WHO / GENERAL`: “The problem. It has been managing the solution for years.”
- Anti-example: “You thought yes, but actually no.” Змінено відповідь, а не ролі чи структуру ситуації.

### `metaphor` — основний

Перенести правило з іншої сфери через одну впізнавану властивість і застосувати його буквально. Каркас задуму: `{subject} shares {property} with {other_domain}; therefore apply {rule_of_other_domain}.` У фінальному тексті не пояснюй цей місток.

Контроль slots: property — waiting, repetition, limited capacity, renewal; домени — queue, subscription, storage, maintenance. Правила — receipt, renewal, capacity limit, maintenance window. Один зв’язний перенос; не всі предмети слід називати підпискою. Наш code `metaphor` охоплює цей authoring-прийом false analogy; alias `false_analogy` не створювати.

- `WHAT / GENERAL`, попередньо для абстрактних визначень: “A queue with a mission statement.”
- `CHOICE / GENERAL`: “The option with fewer footnotes. Doubt charges by the page.”
- Anti-example: “Life is like a journey.” Відоме порівняння без нового висновку чи повороту.

Перше речення вище не дає права позначити весь WHAT/GENERAL придатним: «What should I cook?» потребує окремої перевірки й може змусити відкласти eligibility.

### `hyperbole` — основний

Перебільшити кількість, масштаб, точність або одиницю до очевидно абсурдного рівня. Каркас: `{quantity_or_time}. {short_impossible_accounting_detail}.`

Контроль slots: кількість — one infinity, two infinities, a thousand administrative units; час — неможливий годинниковий показник із коротким контекстом; detail — rounding, spare capacity, uncountable remainder. Не використовуй реальні дозування, фінансові прогнози чи переконливий строк одужання. У HOW_MANY не додавай dollars/days/people, якщо цього не гарантує пул.

- `HOW_MANY / GENERAL`: “Two infinities. One is a spare.”
- `WHEN / GENERAL`: “At 25:00. The extra hour has been reserved.”
- Anti-example: “Exactly 42.” Голий шаблонний номер без власного повороту.

### `false_authority` — допоміжний

Замість підстави висновку підставити очевидно вигадане джерело впевненості. Каркас: `{fictional_authority} certifies {unsupported_finality}.`

Контроль slots: короткі установи, що займаються сумнівом або остаточністю; жодних справжніх лікарів, судів, дослідників чи регуляторів. Сертифікат не повинен виглядати справжнім підтвердженням стану людини або зовнішньої події.

- `OTHER / GENERAL`: “The Office of Completed Doubts certifies that this settles everything.”
- Anti-example: “Scientists have proven this is correct.” Фальшиве фактичне посилання, немає окремого комічного механізму.

Використовуй рідше: зміна назви Office на Council не створює нового кандидата. `oxymoron` і `paradox` зберігаються як історичні proposed codes у моделі даних, але цей workflow не генерує ними окремі batch. Не присвоюй їх як універсальне «нелогічно».

## Редакторські gates і rubric

**Hard gates:** дозволені labels і явні пари; готова англійська фраза без slots; форма відповідає intent; немає залежності від одного точного питання; немає дубльованої трійки answer/intent/topic; жодної реальної інструкції про небезпечну дію, лікування чи фінансового припису під виглядом жарту. Якщо питання робить текст загрозливим, принизливим або фактично оманливим — виправити/зняти eligibility. Це контентна перевірка, а не реалізований runtime safety filter.

Для `HEALTH`, а також серйозних питань у `MONEY`, `LOVE` чи `LIFE`, не виводь дозу, діагноз, прогноз, вирок або пораду на основі випадкового тексту. Залишай мішенню абстрактну систему. GENERAL та ANY також можуть отримати такі питання. Якщо форма YES_NO/HOW_MANY сама створює небажану конкретну пораду, познач це як обмеження контенту й policy; сатиричний About не робить будь-яку фразу доречною.

Запропонована ручна шкала 0–2, не валідований психологічний інструмент:

| Критерій | 0 | 1 | 2 |
| --- | --- | --- | --- |
| Форма й доречність | Не відповідає питанню | Працює з натяжкою | Природно заповнює очікувану форму |
| Переносимість | Працює лише для одного питання | Є залежність від вузької ситуації | Працює на всіх погоджених різних probes |
| Зрозумілий зсув | Випадковий шум / звичайна фраза | Зсув є, але потребує перечитування | Поворот упізнається відразу |
| Deadpan й економність | Пояснення / грайливий коментар | Є зайві слова | Стисла серйозна фінальність |
| Новизна в batch | Лексичний клон або кліше | Знайомий хід із помітною зміною | Відмінні образ і відношення |
| Комічний ефект для читача | Не смішно | Слабка усмішка | Хочеться зберегти або повторити |

Попередній gate для **передачі людині**, не публікації: усі hard gates пройдено; перші два критерії — 2; решта не нижче 1; сума щонайменше 9/12. Пороги — редакторська домовленість для пробного batch. Оцінюй форму/переносимість за найгіршою парою, не середнім. Codex може записати лише свою попередню оцінку; незаповнену людську оцінку залишай `null`. Остаточний відсів слабких жартів робить людина незалежно від суми.

## Smoke corpus

Нижче 9 intent-пулів × 3 питання × 2 кандидати: **54 позитивні пробні поєднання**. Це draft-матриця, не підтвердження універсальної придатності цих eligibility. Питання підібрані так, щоб бачити повторне використання незмінної фрази. Їх недостатньо для release; додаткові негативні probes нижче навмисно виявляють прогалини широких labels.

У стовпці кандидатів наведено primary mechanism у дужках; ці дужки не входять у текст відповіді. Усі пули цієї таблиці — `(intent, GENERAL)`; питання зі спеціалізованими темами моделюють також fallback, якщо точний пул порожній.

| Intent | Три питання: застосувати ОБИДВА кандидати до КОЖНОГО | Кандидат A | Кандидат B |
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

`OTHER` тут — навмисно заданий тест невизначеної форми, а не твердження, що Jev неодмінно обере саме його. `CHOICE` використовує сатиричний критерій, не вдає знання конкретних варіантів; чи це достатньо відчувається як відповідь, має вирішити редактор. WHAT-приклади демонструють переносимість між трьома абстрактними поняттями, але саме тому потребують stress test на інших WHAT.

Універсальний draft-пул `(ANY, GENERAL)`:

- U1: “The uncertainty has been filed as a clerical error.” — `bureaucratic_absurdity`.
- U2: “The Bureau of Settled Questions certifies that no further certainty is required.” — `false_authority`.

Прочитай обидві U-фрази з усіма 27 питаннями таблиці: ще **54 поєднання**. Тут допускається втрата конкретної форми відповіді, бо це явна деградація до універсального вердикту, але не випадковий предмет із Chaos. Universal не означає однаково смішний або доречний за будь-яких обставин. Не плутай цей tier зі звичайним `OTHER / GENERAL`.

### Тематична переносимість і негативні probes

Окремий позитивний приклад для `(WHEN, WORK)`: “After the deadline completes its mandatory orientation.” Перевір незмінний текст до “When will the project start?”, “When will the promotion be announced?”, “When does the review finish?”. Deadline тут сатирична умова, а не слот із конкретного питання. Це показує повторне використання всередині теми, але не підтверджує весь WORK-пул.

| Probe / можлива пара | Що перевіряємо | Очікуване редакторське рішення |
| --- | --- | --- |
| “When will this pain stop?” / `WHEN, HEALTH` | GENERAL-відповідь з конкретним часом може виглядати прогнозом | Не видавати позитивну оцінку лише через WHEN-форму; проблемну eligibility переписати/відкласти |
| “How many tablets should I take?” / `HOW_MANY, HEALTH` | Реалістичне число стає дозуванням | Відхилити числовий draft, що читається як інструкція; не заявляти, що topic або fallback захищає |
| “Should I put all my savings into one investment?” / `YES_NO, MONEY` | Yes/no може сприйматися як реальна порада | Не схвалювати загальний yes/no-каталог за самими жартівливими питаннями |
| “Who should I marry?” / `WHO, LOVE` | Агентність не означає знання особистого життя | Не припускати конкретної людини, статі чи наявності партнера |
| “What should I cook?” / `WHAT, LIFE` | Номінальна відповідь не завжди є визначенням абстракції | WHAT-пул таблиці ще не готовий; якщо проблема всередині GENERAL не виправлена — hold eligibility |
| “Which of these is better?” / `CHOICE, GENERAL` | Немає видимих опцій; ordinal-кандидат вигадував би структуру | Перевірити критерій вибору; не використовувати “the second one” |
| “How long is the meeting?” / taxonomy unresolved | Тривалість і одиниці | Не оголошувати gold label; винести рішення редактору таксономії |
| “When will my printer work?” / `WHEN, TECH` | TECH не означає software deployment | Відхилити “After your pull request is merged.” як надто вузьке eligibility для TECH |

Ці probes не створюють виконуваних exclusions. Якщо draft проходить позитивну таблицю, але падає на звичайному питанні того самого пулу, він **не пройшов gate**. Не приховуй failure за перейменуванням status. Для GENERAL та ANY повтори також тематичні probes, особливо HEALTH/MONEY; остаточна policy для чутливих питань ще відкрита, готовність public release тут не заявляється.

## Приклад формату генерації

Нижче валідний JSON **редакторського draft-пакета**, не DB schema, API request, import contract або seed. `draft_key`, `review`, `fit_questions`, `probe_questions` існують лише в пакеті для перевірки; вони не додають колонок, фільтрів чи маршрути. Один answer, дві явні eligibility, у кожної один primary mechanism. Питання в probes не є runtime-виключеннями. `hold` обрано чесно: людської оцінки та повної перевірки пулів ще немає.

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

Не генеруй удавані production IDs/час створення заради цього прикладу. Коли буде окремо затверджений import-формат, він мусить дотримуватися [моделі даних](../architecture/data.md): усі PK — UUID, усі таблиці мають `created_at`; eligibility має власний UUID і unique `(answer_id, intent, topic)`. `revision` та `answer_revision` вилучені. Поле `humor_mechanism` не переносити на ANSWER, не включати до unique, не дублювати ANSWER для другого механізму. Наявні історичні вердикти зберігають свої текстові snapshots.

## Копійований authoring prompt

Замінити квадратні placeholders перед запуском; це завдання наступній авторській сесії, не runtime prompt Jev.

```text
Прочитай docs/content/answer-generation-guide.md повністю та звір актуальні
context.md, architecture/data.md і architecture/decisions.md.
Працюй офлайн як автор бібліотеки We Know / Instant Answer.
Мова готових фраз — англійська; тон — короткий остаточний deadpan.

Пули: [явні пари intent/topic з поточної proposed taxonomy].
Бажана кількість сильних draft-кандидатів: [число].
Наявний контент для дедуплікації: [файл або явно «ще немає»].
Вихідний локальний draft-документ: [узгоджений шлях].

Спочатку зафіксуй для кожного пулу щонайменше три різні питання й негативний
probe. Вибери один primary mechanism, напиши завершену фразу без slots,
перевір кожного кандидата на кожному питанні та відсій слабкі варіанти.
Для GENERAL врахуй fallback із тематичних питань, для ANY — усі intents.
Якщо неможливо забезпечити придатність усередині існуючого пулу, став hold
або reject; не вигадуй нові labels, приховані exclusions чи runtime-фільтри.

Дай JSON draft у форматі guide, короткі попередні rubric notes, відхилені
варіанти з причинами та evidence gaps. Human scores лишай null.
Записуй humor_mechanism на кожній eligibility; overlap — один primary code.
Не множ клони answer/eligibility. Нічого не імпортуй і не реалізуй.
Selector ігнорує механізми: без ranking, weights чи generative live calls.
Не змінюй БД, бізнес-код, конфігурації або Claude files; не commit/push.
Людська редактура ще попереду; не називай результат доведено смішним.
Якщо сильних кандидатів менше замовленого, повідом дефіцит.
```

## Що має містити handoff

Явні пули, готові фрази та primary mechanisms; матриця question × candidate з failures; редакторські оцінки окремо від людських; короткі причини hold/reject; перевірений JSON і локальні посилання. Не додавати у фінальну фразу пояснення механізму. Різноманіття оцінювати по відношеннях і образах, а не по кількості кодів.

Майбутні likes/votes або owner review не реалізовані. Не перетворюй редакторську оцінку на вагу selector і не вважай кількість лайків доведенням якості: потрібні дані про exposure і контекст вибору, а також урахування самообрання голосувальників. Зараз результат — локальний матеріал для редактора.
