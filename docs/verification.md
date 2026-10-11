# Фактичний результат і перевірки

Дата: 2026-10-11. Робочий каталог: `/Users/serhii/OwnProjects/we-know`.

## Створено

- Composer project/lock, мінімальні Symfony 7.4.20 FrameworkBundle, Console, Dotenv, YAML та TwigBundle 7.4.19; Twig 3.30.0. Symfony core-компоненти обмежені PHP 8.2 baseline і належать до гілки 7.4; contracts/polyfills мають власні версії.
- Явний bootstrap, kernel, console, HTTP front controller, routes/services/framework/Twig config, GET-заглушка англійською.
- PHPUnit 11.5.57, BrowserKit/CssSelector 7.4, test environment і п'ять executable tests.
- Документація: контекст, план, C4, модель даних, ADR, дослідження Jev і стратегія тестування.

Composer 2.8.10 та PHP 8.4.10 уже були встановлені; глобальні інструменти не перевстановлювалися. Symfony CLI не встановлювався. `vendor/` локально встановлений, не є артефактом для ручного редагування; відновлюється з `composer.lock`.

## Виконані перевірки

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

## Що ще не реалізовано / не перевірено

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

Наступний етап — taxonomy/corpus та Jev spike за [технічним планом](technical-plan.md), з реалізацією й тестами адаптера в окремій задачі. Поточна основа не повинна вважатися готовим продуктом лише тому, що skeleton suite зелений.
