# Стратегія тестування

Стан: 2026-10-11. Це план перевірок цільового MVP. Фактично виконані перевірки, встановлені інструменти та їхні результати фіксуються у [verification.md](verification.md). Майбутні тести нижче не означають, що відповідні функції вже реалізовані.

Jev — зовнішній класифікатор, а не тестовий фреймворк і не генератор відповідей. PHP-тести плануються на PHPUnit. Інтеграцію з Jev перевіряємо окремо через локальні HTTP-підміни та явно запущені контрактні звернення до сервісу.

## Межі та інваріанти

1. Класифікатор повертає лише дозволені intent/topic; готову відповідь обирає сервер із редакторської бібліотеки.
2. Вибір відбувається в першому непорожньому придатному пулі. Відсутність точного збігу не дозволяє довільно брати відповідь із чужого intent.
3. Один успішний запит створює збережений результат. Його permalink читає snapshot і не викликає ні Jev, ні random повторно.
4. Редагування чи деактивація фрази в бібліотеці не змінює вже виданий результат.
5. Помилка Jev приводить до визначеної деградації; помилка запису в БД не видає удавано успішний permalink.
6. Серверна валідація працює незалежно від JavaScript. Жоден секрет провайдера не потрапляє у HTML, JSON для браузера, exceptions чи тестові артефакти.
7. У всіх таблицях UUID PK і `created_at`; eligibility має окрему unique трійку answer/intent/topic. Механізм гумору не змінює доступність чи ймовірність вибору відповіді.

Наступні перевірки `decision_context` і nullable eligibility FK умовні: цей спрощений контракт збереження ще є пропозицією. Після погодження перевіряти historical snapshot окремо від поточного стану каталогу, за [моделлю даних](architecture/data.md).

Очікувані URL, поля та статуси звіряти з [технічним планом](technical-plan.md); його затверджений HTTP-контракт має перевагу над прикладами тут. Назви майбутніх інтерфейсів у цьому документі — пропозиції.

## Рівні перевірок

| Рівень | Що реально виконується | Зовнішні залежності | Коли запускати |
| --- | --- | --- | --- |
| Skeleton smoke | Boot kernel, маршрути, Twig, відповіді існуючого GET | Немає | Зараз та при змінах bootstrap |
| Unit | Валідація, fallback, selector, оркестрація зі spies/fakes | Немає | При кожній зміні відповідної логіки |
| Jev adapter integration | Справжній адаптер + `MockHttpClient` + JSON fixtures | Немає | При змінах адаптера, prompts, taxonomy |
| Kernel integration | DI, конфігурація, мапінг помилок | Немає мережі | При змінах wiring |
| Persistence integration | Справжні міграції, repository, транзакції | Ізольовані SQLite і PostgreSQL | При змінах схеми/запитів; перед релізом |
| HTTP application | Контролер → application service → тестова БД; fake classifier | Лише тестова БД | При змінах сценарію створення/читання |
| Browser E2E | Окремий тестовий браузер, DOM, fetch, навігація | Локальний сервер, fake classifier | Коли з'явиться JavaScript |
| Live Jev contract | Справжній адаптер та мала синтетична вибірка | Jev, тестовий ключ, мережа | Явно перед прийняттям інтеграції/зміною моделі |
| Quality/performance eval | Розмічений корпус і вимірювання | Залежить від експерименту | Окремий дослідницький запуск |

У Symfony для інтеграційного рівня є `KernelTestCase`, для HTTP application — `WebTestCase`. Тестовий kernel потребує явної конфігурації `test` environment. У цьому проєкті Flex відсутній, тому bootstrap/config не можна вважати автоматично створеними. [Symfony 7.4: testing](https://symfony.com/doc/7.4/testing.html).

## Що перевірити на skeleton зараз

Поточна корисна перевірка — реальна загрузка застосунку та rendering, а не unit test, який повторює один виклик `render()` у контролері:

- `GET /`: `200`, HTML content type, `lang="en"`, назва продукту, основний заголовок, рівно один `main`.
- HTML містить зрозумілий стан заглушки; robots `noindex, nofollow` відповідає поточному неопублікованому стану. При запуску продукту цю вимогу свідомо змінити.
- `HEAD /`: `200` з headers сторінки та без body. Невідомий URL: `404`; `POST /`: `405`, без запуску бізнес-сценарію.
- Kernel завантажується в `test`, а production smoke з `APP_DEBUG=0` не розкриває debug stack trace.
- Поточний smoke не потребує Jev, БД, API key або browser automation.

Пакети PHPUnit/BrowserKit/CssSelector та конфігурацію додавати явно, з урахуванням PHP baseline 8.2. Не копіювати номер найновішого PHPUnit без перевірки його PHP-вимог. Фактичний lock file та [verification.md](verification.md) визначають встановлені версії.

## Unit: детермінована бізнес-логіка

Залежності, які варто інжектувати за потребою: `QuestionClassifier`, `AnswerRepository`, `RandomIndexSource`, `VerdictRepository`, clock та генератор public ID. Не додавати абстракцію виключно заради тесту, якщо існуючий простий контракт вже дозволяє підміну.

| Об'єкт перевірки | Приклади та очікування |
| --- | --- |
| Question validation | Порожній рядок, лише пробіли, `null`, масив замість рядка, 1/500/501 Unicode code points, NUL, malformed UTF-8, невідомі поля, багатобайтний текст. Ліміт 8 KiB тіла та ліміт символів питання перевіряються окремо |
| Нормалізація | Поведінка пробілів/newlines узгоджена з контрактом; punctuation та зміст не зникають. Якщо зберігаються original і normalized, тестувати обидва поля |
| Taxonomy | Усі дозволені значення; невідомий intent/topic чи `ANY` у відповіді Jev відхиляється адаптером як invalid_response. Application переводить typed failure у OTHER/GENERAL fallback-контекст, зберігаючи failure status |
| Fallback | Точний intent/topic → intent/GENERAL → ANY/GENERAL; на кожному кроці непорожній пул завершує пошук; GENERAL не шукається двічі; неактивні та іншомовні фрази виключаються; IDs дедуплікуються |
| Selector | Пули з 1 і кількох елементів; fake random обирає перший/останній доступний індекс. Помилковий індекс не маскується переходом до сторонньої категорії |
| Humor mechanism | Зміна механізму на eligibility не змінює candidate IDs, порядок правил fallback або ваги; import відхиляє дубль answer/intent/topic навіть з іншим механізмом |
| Decision context, якщо прийнято | Успіх із точним пулом та fallback: категорії Jev не підміняються категоріями вибраного пулу; tier, candidate_count та механізм відповідають фактичному вибору. Timeout не створює вигаданих classifier labels; повний response/кандидати/секрети не зберігаються |
| Порожня бібліотека | Повертається окрема доменна помилка та контрольована HTTP-помилка; не зберігається порожній вердикт |
| Application service | Успіх викликає класифікацію, вибір і запис у визначеному порядку; invalid input не викликає жодного з них |
| Jev unavailable | Використано універсальний погоджений fallback, причина деградації доступна телеметрії; текст не генерується моделлю |
| Persistence failure | Відсутній success response; повторна спроба не використовує неіснуючий permalink |
| Read permalink | Повертається збережений snapshot; spies на classifier/random мають нуль викликів |

Не перевіряти випадковість умовою «два запити обов'язково дадуть різні відповіді»: повтор є законним результатом random. Не робити flaky gate з гістограми частот. У unit перевіряємо допустимість кандидата та використання random boundary; статистичний розподіл за потреби досліджуємо окремо.

## Jev adapter: без мережі

Адаптер використовує Symfony `HttpClientInterface`; у тестах передаємо `MockHttpClient` і `MockResponse`. Callback перевіряє request, response fixtures перевіряють parsing. Symfony підтримує підміни status та transport failures, зокрема timeout. Це перевірка нашої інтеграції, а не доказ доступності живого API. [Symfony 7.4: HTTP Client testing](https://symfony.com/doc/7.4/http_client.html#testing).

Стан офіційного quickstart на 2026-10-11: POST `https://api.typesafe.ai/v1/systemone`, Bearer credential, body з `state`, `model`, `questions`; `choice` підтримує перелік `criteria`. Ця структура придатна для окремих intent/topic у одному зверненні. Точний parser fixture потрібно звірити з актуальним response schema перед реалізацією. [TypeSafe quickstart](https://docs.typesafe.ai/introduction/quickstart).

Обов'язкова матриця adapter tests:

- Метод, фіксований конфігурований origin/path, JSON content type, фіктивний Bearer token, model і версія taxonomy/prompt. Поле питання не може змінювати URL або headers.
- У `state` передається лише погоджений текст питання; бібліотека відповідей, cookies, IP і внутрішні налаштування не відправляються.
- Обидві категорії запитуються одним зверненням; невідомий enum, відсутня категорія чи неправильна форма відповіді — invalid_response. Перехід до OTHER/GENERAL відбувається в application fallback policy, не маскує помилку провайдера під success. Рядок із готовим жартом чи інструкціями не стає відповіддю продукту.
- Валідна відповідь; порожній body; malformed JSON; wrong type; відсутня одна категорія; невідоме значення; зайві поля; невідповідна структура; надмірний response body.
- `401/403/422`, `429`, `500/502/503/529`, redirect, transport exception, timeout до headers і під час читання body.
- Кожна невдала відповідь потрапляє у визначений тип помилки, а не випадково у `200` зі `null`. Auth failure відрізняється в diagnostics від timeout, але секрет не розкривається.
- Для першої версії пропонується одна спроба Jev у foreground без автоматичного retry. Тест перевіряє точну кількість calls, `max_duration` 400 ms та idle timeout не більший за цей бюджет; retry policy не повинна непомітно множити latency/вартість. Ці стартові значення переглядаються разом із технічним планом після вимірювань.
- Redirect не переносить credential на сторонній host. Помилки та логи не містять токена, повного raw request чи персональних питань.

Fixtures — синтетичні, невеликі, з коментарем про джерело схеми/дату. Не зберігати HAR із реальними Authorization headers. Відповідь, вигадана вручну за неперевіреною схемою, позначається як чернетка, а не як запис реального контракту.

## Live Jev contract та якість

Live suite відділений від default runner: наприклад, окремий `phpunit.live.xml` і `tests/Contract/Live/`. Пропонований запуск — лише за `RUN_LIVE_JEV_TESTS=1` плюс наявним server-side credential. Без явного прапорця мережа не викликається; якщо прапорець встановлено, але ключа немає, preflight завершується зрозумілою помилкою, а не показує зелений результат після всіх skipped tests.

Перед першим live запуском потрібні реалізований adapter, тестовий акаунт/ключ, підтверджені endpoint/model та доступний бюджет. Під час початкового scaffold зовнішні платні виклики не виконуються. Ключ передається через середовище/локальний секрет, не через CLI argument, fixture чи committed `.env`.

Малий live smoke: 3–5 синтетичних питань, один виклик на питання, послідовне виконання, обмежений загальний deadline, без повторів при `429`. Перевіряємо HTTP-доступність, сумісність схеми, наявність обох категорій та їх належність allowlist. Вибір конкретного intent для природної мови не робимо жорстким контрактним assertion. Фіксуємо дату, requested/resolved model (якщо API повертає), prompt/taxonomy version, status і durations без raw secrets.

Окремий quality eval, запропонований стартовий обсяг — 180–300 синтетичних англомовних питань:

- Кожен intent, кожна тема, неоднозначні/короткі питання, punctuation, typo, injection-подібний текст, out-of-scope та неанглійські приклади.
- Редакторські labels допускають кілька прийнятних категорій для неоднозначного питання; окремий frozen holdout не використовується для налаштування prompt.
- Звіт: per-intent recall, confusion matrix, macro F1 на однозначній частині, прийнятність на неоднозначній, частка invalid schema, fallback rate, latency P50/P95/P99, кількість/вартість calls за фактичним тарифом.
- Форма відповіді та якість гумору оцінюються окремо: відповідність intent, доречність абсурду, різноманітність, deadpan tone. «Смішно» не замінюється автоматичним unit assertion.
- Початкові пороги якості затверджуються після baseline. Дані й довірчі інтервали мають бути в звіті; випадковий провал одного прикладу не є автоматично regression.

Зміна плаваючої model alias може змінити результат без зміни коду. Повторний eval потрібен після зміни моделі, provider schema, prompt, taxonomy чи бібліотеки; саме порівняння corpus versions повинно бути відтворюваним.

## Persistence: SQLite та PostgreSQL

Перевіряти однаковий repository contract на обох рушіях. SQLite не є доказом сумісності з production PostgreSQL. Для PostgreSQL зафіксувати major version після вибору hosting; до цього результат позначається як перевірка кандидата.

| Сценарій | Перевірка |
| --- | --- |
| Міграції | До production після редагування development migrations перебудована disposable test БД проходить поточний chain на обох рушіях. Коли є спільні/постійні дані — застосовані міграції незмінні; нові проходять upgrade з попередньої версії зі збереженням даних. `schema:create` не замінює перевірку міграцій |
| Цілісність | UUID PK і created_at у всіх таблицях, окремий UUID PK eligibility і UNIQUE (answer_id, intent, topic), foreign keys, NOT NULL та погоджені enum/check constraints реально діють; для SQLite явно перевірити foreign key enforcement |
| Fixtures | У кожного intent є достатній доступний fallback; inactive та duplicate join rows не спотворюють eligible pool |
| Точний вибір | Queries повертають погоджені пари: `(WHY, WORK)` та `(WHEN, TECH)` не дозволяють `(WHY, TECH)`; case sensitivity/collation не змінюють enum lookup; службові IDs не залежать від випадкового порядку DB rows |
| Snapshot | Після створення змінити/деактивувати бібліотечну фразу; старий permalink лишається незмінним без revision-полів. Якщо decision_context/FK прийнято: змінити categories/mechanism eligibility — JSON не змінюється; видалити eligibility — FK стає null, вердикт та snapshots залишаються |
| Catalog import | UUID відповідей/eligibility і created_at стабільні при повторному import; дозволені mechanism strings перевіряються; помилка не залишає частково оновлений каталог |
| Unicode/час | Emoji, лапки, HTML-подібний текст і UTC timestamp проходять round trip; довжина не непомітно обрізається |
| Atomic write | Штучна помилка DBAL insert/commit не лишає частково створеного результату; success відправляється лише після commit; зовнішній HTTP-виклик не утримує DB transaction |
| Public ID collision | Fake generator повертає існуючий ID; контрольований retry має обмеження, не перезаписує результат |
| Concurrency | Два окремих connections/processes створюють записи без втрати даних. SQLite busy/lock і PostgreSQL conflict мають контрольовану обробку |
| Недоступність БД | Контрольована `503` або інший затверджений status; не повертається успішний JSON із недоступним permalink |

За замовчуванням кожен тест використовує ізольовані fixtures і відновлює стан. Migration/concurrency тести запускаються окремо від suite із transaction rollback: один зовнішній rollback не імітує незалежних клієнтів. SQLite in-memory прив'язаний до connection; для kernel reboot і кількох процесів використовувати окремий тимчасовий файл. PostgreSQL — окрема тестова БД/схема на worker. Перед destructive reset перевіряти test environment і allowlisted test DSN; production DSN тестам не доступний.

Це план майбутніх DB-тестів. Під час уточнення документації reset не виконується, БД та міграції ще не створені.

## HTTP та browser acceptance

Після реалізації `POST /answer` перевірити весь ланцюг із fake classifier та реальною тестовою БД: валідний запит → `201`, JSON зі збереженим результатом, правильні `Location` та `Cache-Control: no-store` → `GET /a/{publicId}` → та сама пара питання/відповідь після перезавантаження kernel. Два нові запити можуть мати різні UUIDv4 і однаковий текст відповіді; обидва варіанти коректні. Canonical/OG формуються зі snapshot і довіреного origin, сторонній Host не підміняє share URL.

Негативна HTTP-матриця: некоректний JSON (`400`), неприпустимий Origin/cross-site (`403`), непідтримуваний media type (`415`), завеликий body (`413`), domain validation (`422`), rate limit (`429` із `Retry-After`), невідомий permalink (`404`), тимчасова недоступність БД/контенту (`503`). Статуси відповідають поточній пропозиції HTTP-контракту. Invalid request не створює запис і не викликає Jev. GET permalink не витрачає provider quota. Provider failure з доступним universal pool завершується `201` зі збереженим fallback, а не `503`.

Окремі security cases: HTML/script у питанні екранується Twig та безпечно вставляється в DOM через текст; injection-рядок зберігається як дані; same-origin policy, прийнятні content types та rate limiting діють до платного виклику. Якщо обрано CSRF-token захист, тестувати відсутній/невалідний/дійсний токен; CSRF не замінює захист від автоматизованого abuse. У prod error response відсутні trace, DSN, ключі й raw provider errors. `100% confidence` перевіряється лише як сатиричний copy, не як метрика класифікатора.

Browser E2E запускається в окремому тестовому профілі, без керування особистими вкладками користувача. Вибір runner (наприклад, Playwright) зробити при появі JS; production сайт від цього не стає SPA і не потребує JS framework.

- Enter і click запускають один POST; кнопка та input мають зрозумілий pending state, після success/error доступні знову.
- Network error, timeout, `429`, `503`, HTML замість JSON, malformed JSON: користувач бачить правдиве повідомлення, введений текст збережений.
- Double-click не створює два клієнтські submits. Якщо дозволено новий submit до завершення попереднього, відповіді навмисно переставляються в часі: stale response не перекриває останній результат. `AbortController` не вважається гарантією скасування вже початого server-side запису.
- Reload/back/direct permalink працюють як серверні сторінки; share URL відповідає показаному результату, не останньому довільному response.
- Keyboard focus, label, повідомлення через доступний live region, narrow viewport та відсутність зміщення критичних controls перевіряються на реальному DOM.
- Вимкнений JS: головна сторінка та permalink читаються, форма явно пояснює необхідність JS. Поточний HTTP-план не передбачає form-encoded fallback.

## Продуктивність та спостережуваність

Unit/integration assertions не повинні залежати від wall-clock «менше 20 ms» на довільній машині. Timeout policy перевіряється через контрольований transport/fake clock; реальна latency вимірюється окремо.

Початкові гіпотези зі [збереженого контексту](context.md): backend без провайдера до 20 ms, POST P50 до 300 ms / P95 до 700 ms, permalink server response до 100 ms. Це не прийняті SLA і не підтверджені результати.

Для кожного performance run записувати commit/revision evidence, PHP/DB/model versions, machine/region, warm/cold стан, dataset size, concurrency, count, error/fallback rate та percentiles. Вимірювати окремо validation, provider, pool lookup, write і rendering; відрізняти повний end-to-end час від provider latency. Порівняти normal/fallback та warm/cold DB, перевірити індекси на реалістичному обсязі. Навантаження на Jev не запускати автоматично разом із локальним load test; synthetic delay adapter покриває більшість сценаріїв без provider traffic.

Telemetry tests перевіряють корисні counters/timings і низьку cardinality labels. Питання, токени й permalink IDs не стають metric labels. Логи дозволяють відрізнити invalid provider schema, timeout, fallback та DB failure без зберігання raw input за замовчуванням.

## Порядок впровадження та критерії готовності

1. **Skeleton:** bootstrap/HTTP smoke та явна test config. Результати внести у verification.
2. **Taxonomy + domain:** погоджений fallback, fixture pools, validator/selector/application unit tests; deterministic random boundary.
3. **Jev adapter:** offline request/response/error matrix, DI tests; далі окремий live contract та baseline quality report за наявності preconditions.
4. **Дані + POST/permalink:** міграції на обох рушіях, snapshot/rollback/concurrency, HTTP acceptance.
5. **Vanilla JS:** browser scenarios success/errors/races/accessibility в ізольованому браузері.
6. **Перед релізом:** production config smoke, PostgreSQL acceptance, security cases, performance baseline і актуальний live contract report. Окремо перевірити restore backup в ізольовану БД: snapshot/permalink/кількості записів зберігаються. Після визначення retention/deletion policy перевірити видалення без повторного використання UUID. Заблокований/не виконаний рівень явно зазначається, а не рахується пройденим.

Рекомендована майбутня структура: `tests/Unit`, `tests/Integration/Jev`, `tests/Integration/Persistence`, `tests/Functional`, `tests/Contract/Live`, `tests/Fixtures`; browser tests окремо за правилами обраного runner. Default PHPUnit discovery виключає live directory на рівні конфігурації. Default HTTP dependency — mock/deny-network; невідомий outbound call завершує тест помилкою. Окремий PostgreSQL profile і live config називаються явно, щоб локальний зелений SQLite suite не приховував пропущену перевірку.

Критерій завершення задачі — всі сценарії її рівня пройдено, а винятки й відсутні preconditions перелічено у verification. Відсоток coverage допомагає знайти неперевірені гілки, але не замінює доказу незмінності permalink, коректного fallback чи реального контракту Jev.
