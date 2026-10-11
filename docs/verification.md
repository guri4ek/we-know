# Фактичний результат і перевірки

Дата: 2026-10-11. Робочий каталог: `/Users/serhii/OwnProjects/we-know`.

**Актуальний результат:** scoped етап 1 реалізовано й перевірено offline та двома development live серіями. Див. розділ «Етап 1» нижче та [порівняння](evaluation/stage-1-results.md). Розділи skeleton/baseline зберігають історичні перевірки й стан до цього етапу.

## Skeleton: створено

- Composer project/lock, мінімальні Symfony 7.4.20 FrameworkBundle, Console, Dotenv, YAML та TwigBundle 7.4.19; Twig 3.30.0. Symfony core-компоненти обмежені PHP 8.2 baseline і належать до гілки 7.4; contracts/polyfills мають власні версії.
- Явний bootstrap, kernel, console, HTTP front controller, routes/services/framework/Twig config, GET-заглушка англійською.
- PHPUnit 11.5.57, BrowserKit/CssSelector 7.4, test environment і п'ять executable tests.
- Документація: контекст, план, C4, модель даних, ADR, дослідження Jev і стратегія тестування.

Composer 2.8.10 та PHP 8.4.10 уже були встановлені; глобальні інструменти не перевстановлювалися. Symfony CLI не встановлювався. `vendor/` локально встановлений, не є артефактом для ручного редагування; відновлюється з `composer.lock`.

## Skeleton: виконані перевірки

| Перевірка | Результат |
| --- | --- |
| `composer validate --strict --no-check-publish` | Pass, manifest valid |
| `composer check-platform-reqs` | Pass на фактичному PHP 8.4.10 |
| `php bin/console about` | Kernel boot, Symfony 7.4.20 LTS |
| `php vendor/bin/phpunit` | **5 tests, 16 assertions, pass** |
| Реальний HTTP з `APP_ENV=prod APP_DEBUG=0` | GET `/` 200; HEAD `/` 200 без body; GET невідомого URL 404; POST `/` 405 |
| Перевірка production error bodies | Немає stack trace, RouterListener path чи локальних filesystem paths |
| Mermaid 11 parser + jsdom у тимчасовому каталозі | **6 діаграм успішно розібрано**: 5 у C4, 1 ER |
| Локальні Markdown links, JSON code blocks та fences | Перевірено скриптом після завершення документів |

Команди виконувалися через `rtk proxy`. npm-залежності Mermaid/jsdom розміщено тільки у `/private/tmp/we-know-doc-validation`; Node-залежностей у PHP-проєкті немає. Browser automation не використовувалась для цих перевірок.

Application tests доводять SSR/англійський HTML, єдиний main, поточний robots/noindex, HEAD без тіла, 405 для POST і 404 для відсутньої сторінки. Bootstrap regression test перевіряє пріоритет process `APP_ENV=prod` / `APP_DEBUG=0` над локальними `.env` defaults навіть без цих значень у superglobals.

HTTP smoke пройдено через тимчасовий PHP server на `127.0.0.1:18081`, без браузера; після перевірки сервер зупинено. Це функціональна перевірка production-конфігурації через HTTP, не тест PHP-FPM/Nginx або продуктивності. Перший холодний запуск перевищив короткий timeout; повтор із достатнім технічним timeout пройшов. Жодного висновку про цільові P50/P95 з цього запуску немає.

У CLI error renderer Symfony може показувати технічні деталі в консолі; доказ відсутності витоку — саме HTTP smoke із debug off. Під час нього виявлено й виправлено проблему імпорту process environment до Dotenv; після зміни повторено всю PHPUnit suite і HTTP smoke.

## Історичні обмеження після skeleton

- Бізнес-логіка, POST fetch, `/answer`, permalink, каталог, migrations, DBAL, Jev adapter і credentials не створені. Немає прихованого stub, що видає себе за робочий AI.
- Unit-тести бізнес-правил, offline adapter integration і live Jev contract **заплановані, але ще не написані/не запущені**, оскільки відповідної реалізації немає. Деталі — [testing.md](testing.md).
- Ключ/акаунт/баланс TypeSafe не перевірялися, платних API-викликів не було. Офіційні документи досліджено; це не заміна реального integration report.
- SQLite/PostgreSQL repositories і міграції не перевірялися. Локальні PHP extensions `pdo_sqlite` та `pdo_pgsql` присутні, що не означає наявності PostgreSQL server або підключення.
- PHPUnit запущено на PHP 8.4.10; реальний runtime PHP 8.2 не тестувався. Composer platform встановлений у 8.2.0 для dependency resolution, але не замінює matrix run.
- Mermaid syntax валідний; візуальне розташування блоків у браузері не перевірялося. UI заглушки не є фінальним дизайном.
- Немає deployment, performance/quality baseline, перевірки backups/retention чи публічного домену.

## Межі змін

Початковий scaffold створено без Git. За наступним явним дорученням користувача окремий агент перевірив `https://github.com/guri4ek/we-know`: `git ls-remote` не повернув refs. Локальний репозиторій ініціалізовано на `main`, origin вказує на цей URL. Baseline commit: `e0dcdcc9671b9b046cc17ec436d53d60f196a729` — 30 файлів наявного source/config/tests/docs та Composer lock. Використано вже налаштованого Git author, без зміни особистої конфігурації. Користувач окремо дозволив push основи й документації; це не deployment продукту.

Перед baseline перевірено staged paths/content і source URLs 64 lockfile packages: credential patterns та URL credentials не виявлено. `.env` має лише локальні defaults із порожнім APP_SECRET. `vendor/`, `var/` та локальні secret env-файли виключені чинним `.gitignore`; `.idea/` та автоматично створений Symfony `config/reference.php` також додано до ignore разом з уточненням документації, без видалення локальних файлів. IDE/runtime/generated reference не входять до комітів. Особисті Codex/Claude файли та їхні backups не змінювалися.

## Уточнення документації після baseline

- UUID PK/created_at у кожній таблиці, окремий UUID eligibility зі збереженням unique трійки; ANSWER.revision/VERDICT.answer_revision прибрано.
- Прийняте поле humor_mechanism на eligibility не впливає на selector. Один контрольований mechanism string на рядок — пропозиція; статистика/лайки/голосування не реалізовані.
- Спрощений VERDICT із nullable eligibility FK та коротким historical decision_context JSON позначений як пропозиція. Розділено категорії Jev і вибраний пул; зміна/видалення каталогу не переписує текстовий чи діагностичний snapshot.
- Зафіксовано правила редагованих development migrations для disposable БД до production та незмінних incremental migrations після появи спільних/постійних даних. Міграції не створені, жодного reset не виконано.
- README, карта документів, context, ADR, C4, технічний план, Jev research і план тестів узгоджені. Окремі версії rubric/corpus у дослідницьких звітах не означають обов'язкових колонок VERDICT.

Перевірки цього уточнення: перед baseline повторено PHPUnit — **5 tests, 16 assertions, pass**; після змін документації Mermaid parser прийняв **6 діаграм**, перевірено **10 Markdown-файлів, 34 локальні посилання, 4 JSON-блоки та парність fences**. `git diff --check` пройшов. Нові бізнес-тести, live Jev, DB checks і browser automation не запускалися; змін application code після baseline немає.

Після bootstrap було заплановано taxonomy/corpus та Jev spike за [технічним планом](technical-plan.md); його фактичний результат наведено нижче. Skeleton suite сам по собі не є доказом готового продукту.

## Етап 1: Контент і Jev spike

Scoped implementation виконано за новим прямим дорученням; окремо Serhii дозволив дві development live серії по 54 calls. Початкові 300/400 ms дали 41 success / 13 timeout; повтор із 600/800 ms — 54 successes / 0 failures. Після порівняння **Serhii обрав 600/800 ms як поточні defaults**. Rubric/labels між runs не змінювалися, holdout не викликався.

Реалізовано taxonomy/rubric v1, corpus 54 development + 27 holdout, typed `QuestionClassifier`, Jev adapter/strict normalizer/Symfony DI, configurable timeout/model/key, CLI runner і offline suites. Додано тільки HttpClient 7.4.20 та два потрібні транзитивні packages (contracts 3.7.3, polyfill-php83 1.43.0). Application key перенесено з versioned `.env` до вже ігнорованого `.env.local`; значення не виводилося й не копіювалося в reports.

Unit/integration matrix включає success, malformed/empty/oversized JSON, missing fields, invalid enums (включно ANY), числові типи/ranges/non-finite values, incomplete/invalid distributions, model/usage, extra metadata, low confidence без threshold, валідні OTHER/GENERAL, 401/403/422/429/529/500/502/503/redirect, timeout/transport на request/body, exact endpoint/headers/payload, two Choice per call, no retry, DI wiring та deny-network test container. Idle timeout імітується documented MockResponse empty chunk, без sleep.

Runner metrics перевірено deterministic fixtures: змішані correct/wrong/failures, empty/all-failed, nearest-rank P50/P95, matrices, numerator/denominator, known/unknown usage. CLI preflight перевіряє явний opt-in, ключ, limit, split, fixture version/IDs та budget; offline replay проганяє справжній adapter. Тестові services примусово використовують dummy/empty key незалежно від локального secret.

| Фінальна перевірка етапу 1 | Результат |
| --- | --- |
| Default PHPUnit, включно з усіма 5 skeleton tests | **156 tests, 561 assertions, pass**, PHP 8.4.10 |
| Composer validate --strict / check-platform-reqs | Pass; baseline resolution 8.2, фактичний runtime 8.4.10 |
| YAML / lint:container (dev і prod) | Pass, 6 YAML files, DI types compatible |
| PHP syntax | Pass, 27 source/test PHP files |
| Offline CLI replay | 6 attempts, 4 successes, 2 навмисні failures; metrics збігаються з fixtures; defaults 600/800 ms |
| Production offline CLI | 2 bounded replay samples, без мережі; активний policy 600/800 ms |
| Documentation | 16 README/docs Markdown files, 94 local links, 5 JSON code blocks, 8 corpus/report/fixture/Composer JSON files; fences paired |
| Project secret / report consistency | Key тільки в ignored `.env.local`, не знайдений у deliverable source/docs/config/fixtures; два live reports по 54 attempts, matrices sums=54, no retries |

Documentation counts вище охоплюють README/docs; окремий fixture provenance README не входить у 16. Діаграми не змінювалися й повторно не рендерилися. Повторний HTTP server/browser smoke не виконувався: GET/HEAD/404/405 перевірені наявними functional tests, продуктові HTTP routes не змінювалися.

Відтворення offline:

```sh
rtk proxy composer validate --strict --no-check-publish
rtk proxy composer check-platform-reqs
rtk proxy php vendor/bin/phpunit
rtk proxy php bin/console lint:yaml config
rtk proxy php bin/console lint:container
rtk proxy php bin/console app:jev:evaluate
```

Результати live, exact metrics із denominators й commands — [evaluation](evaluation/README.md) та [порівняння двох runs](evaluation/stage-1-results.md). Успішно перевірений живий контракт повертав обидві категорії, distribution/confidence, resolved model і tokens за один call. Друга серія: intent/topic 52/54 (96.3%), joint 50/54 (92.6%), technical failures 0/54, P50/P95 238.4/408.9 ms, input/output 54,826/8,330 tokens. Це synthetic development evidence, не human gold чи production SLA.

Неперевірені: holdout quality, independent human labels/humor review, production region/concurrency, довготривалий latency/failure baseline, фактичний billing та PHP 8.2 runtime. Error statuses не провокувалися live; вони покриті mocks. Для наступних calls потрібен новий явний ліміт і дозвіл на split/model/policy; наявний key працює. Попереднього budget 108 calls дотримано, без retry.

DB/migrations/import/selector/VERDICT/POST/fetch/permalink/admin не реалізовано, `decision_context` не затверджено. Research-owned humor/guide files збережено; нові answer examples — окремі hold drafts. Git commands/delivery, deployment, браузер, Claude files та особисті Codex configuration/backups не використовувалися/не змінювалися.
