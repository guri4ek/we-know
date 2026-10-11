# Реєстр архітектурних рішень

2026-10-11. «Прийнято» означає явне рішення користувача або конкретний обмежений вибір bootstrap; «Пропозиція» потребує перевірки на відповідному етапі, але не блокує дослідження.

| ID | Статус | Рішення та наслідок |
| --- | --- | --- |
| ADR-001 | Прийнято користувачем | Symfony, SSR, vanilla JS POST fetch; без SPA |
| ADR-002 | Прийнято для bootstrap | Symfony 7.4 LTS, PHP baseline 8.2, Twig; Composer lock. Пакети конфігуруються явно, без Flex/Runtime recipes |
| ADR-003 | Прийнято користувачем | Contextual Absurdity, Jev спочатку; Chaos/NLP відкладено |
| ADR-004 | Прийнято користувачем | Jev класифікує категорії; PHP random у категорії. Модель не генерує й не ранжує фінальні фрази |
| ADR-005 | Прийнято користувачем | Англійська UI/контенту для першої версії; документація українською |
| ADR-006 | Пропозиція | SQLite dev, PostgreSQL prod, DBAL + Migrations без ORM. Перевіряти обидві платформи |
| ADR-007 | Пропозиція, відповідає вимозі share URL | Immutable snapshot у VERDICT, UUIDv4, GET без Jev/random |
| ADR-008 | Пропозиція | Eligibility як явні пари intent/topic; не дві незалежні множини |
| ADR-009 | Реалізовано classification частину етапу 1 | Один Jev call, початкові timeout 0.30 s / max_duration 0.40 s; після двох live серій Serhii обрав **0.60/0.80 s як defaults**, без retry/redirect; model configurable/pinned. Adapter повертає typed failure; application fallback лишається наступним етапом |
| ADR-010 | Пропозиція | Каталог через перевірений CLI import; без web admin і шаблонної генерації на старті |
| ADR-011 | Пропозиція | Публічний за URL, noindex, без стрічки; перед submit повідомити про зберігання/передачу питання |
| ADR-012 | Прийнято користувачем | Спочатку мінімальна основа, дослідження, архітектура і план тестів; не реалізовувати весь продукт зараз |
| ADR-013 | Прийнято користувачем | Unit та інтеграційні тести, зокрема Jev; реальний API відокремити від offline suite |
| ADR-014 | Прийнято користувачем | До production для disposable dev/test БД міграції можна редагувати на місці й перебудовувати БД. Коли дані стають спільними/постійними — нові незмінні incremental migrations; зараз жодного reset чи deployment |
| ADR-015 | Прийнято користувачем | У всіх таблицях власний UUID PK і `created_at`; eligibility зберігає окремий unique `(answer_id, intent, topic)`. Поля ANSWER.revision і VERDICT.answer_revision вилучено |
| ADR-016 | Прийнято напрям; форма — пропозиція | `ANSWER_ELIGIBILITY.humor_mechanism` заповнюється при підготовці/генерації контенту, не впливає на selector, лишається для статистики. Пропонується один контрольований string на рядок; кілька механізмів, лайки/голосування та оцінка власником — майбутні рішення |
| ADR-017 | Пропозиція, не затверджено | VERDICT: UUID id, question_snapshot, answer_snapshot, nullable answer_eligibility_id, decision_context JSON, created_at. JSON фіксує класифікацію, фактично вибраний пул/механізм і причину fallback на момент події; попередній перелік окремих діагностичних колонок більше не вважається прийнятим |
| ADR-018 | Зафіксовано scoped baseline етапу 1 | [Taxonomy/rubric 1.0.0](../content/taxonomy-v1.md): duration→WHEN, explicit selection→CHOICE, pay→MONEY, career/team organization→WORK, technical operation→TECH; multiple questions→first recoverable; Where→WHAT. Авторські labels, без незалежної human validation |
| ADR-019 | Реалізовано етап 1 | Unknown enum/неповна схема/невалідні числа→invalid_response; probabilities повні й sum tolerance 0.001, choice має бути maximum (tie allowed). Confidence threshold відсутній; допуск є локальною schema policy, не calibration |
| ADR-020 | Уточнення Serhii, реалізовано | Один JEV_API_KEY для малого проєкту й evaluation, тільки `.env.local`/environment. Live runner вимагає --live і явний limit; no retries. Development/holdout окремі; метрики failures не називаються empty-catalog fallback |

## Наступні рішення, які не потребують очікування для поточного дослідження

1. **Taxonomy/content**: baseline v1 зафіксовано; незалежно перевірити labels, holdout quality й придатність контентних пулів. Уточнення v1 пояснені в taxonomy, humor codes лишаються редакторським proposal.
2. **Jev access**: project key налаштовано, доступ до pinned моделі підтверджено двома development live серіями. Фактичний billing/баланс, довготривалі ліміти та account privacy policy ще не перевірено. Ключі не шукати в сторонніх конфігураціях.
3. **Quality gate**: мінімальна точність intent/topic, частка fallback та редакторська оцінка гумору; числа встановити до evaluation, не після результатів.
4. **Brand/domain**: `we-know` — репозиторій; `Instant Answer` / `instant-answer.net` — вихідний концепт. Власність домену не підтверджена.
5. **Production**: хостинг, PostgreSQL version, region, concurrency, API budget і backups.
6. **Data policy**: retention, видалення результатів, чи індексувати shared pages, правила для чутливих питань і контентна модерація перед public release.
7. **Decision context**: погодити запропонований короткий JSON і nullable eligibility FK з `ON DELETE SET NULL`; це пояснення вибору, не повне відтворення random. Деталі й приклад — [модель даних](data.md).
8. **Humor mechanism**: затвердити контрольований перелік і правило одного механізму на eligibility. Статистику не перетворювати на приховану вагу selector.

## Відкладено свідомо

Chaos driver, lexical/NLP classifier, weighted selection, embeddings/vector DB, generative templates, accounts, chat history, admin dashboard, likes/voting, owner-review analytics, A/B assignment, distributed cache, queues, dynamic OG images, worker runtime. Змінність провайдера реалізується межею адаптера, а не написанням кількох непотрібних реалізацій.

## Робочі дозволи

Користувач явно дозволив виняток із reviewer-only профілю для локального bootstrap і документації we-know; пізніше дозволив дослідження, архітектуру, тестування, субагентів і Git. Наступне явне доручення: окремий агент фіксує основу, уточнює документацію та відправляє зміни до наданого GitHub-репозиторію. Це не дозвіл на deployment чи реалізацію бізнес-функцій. Фактичні перевірки й Git baseline — у [verification.md](../verification.md). Особисті конфігурації Codex/Claude залишаються поза змінами.
