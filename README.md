# We Know / Instant Answer

**We don't think. We know.**

Сатиричний англомовний сервіс: питання → класифікація → випадкова готова абсурдна відповідь відповідної категорії → постійне посилання.

Репозиторій: https://github.com/guri4ek/we-know

## Поточний стан

Стан на 2026-10-11: Symfony 7.4 LTS / PHP baseline 8.2, Twig та `GET /` зі статичною заглушкою. Етап 1 має taxonomy/rubric v1, 81 синтетичне питання, типізований Jev adapter, offline тести й CLI evaluation runner. Виконані live development runs та обмеження — у [результатах етапу 1](docs/evaluation/stage-1-results.md). Selector, БД, POST fetch та permalink ще не реалізовані.

Почніть із [карти документації](docs/README.md), [контексту вихідної сесії](docs/context.md), [технічного плану](docs/technical-plan.md) та [C4-моделі](docs/architecture/c4.md).

Перед створенням або редагуванням відповідей прочитайте [інструкцію офлайн-авторингу](docs/content/answer-generation-guide.md). Її приклади — редакторські drafts, не готовий seed; [дослідження гумору](docs/research/humor-mechanisms.md) відокремлює першоджерела від практичних гіпотез.

[Модель даних](docs/architecture/data.md) враховує UUID PK/created_at у всіх таблицях і `humor_mechanism` на eligibility без впливу на random. Спрощений VERDICT із діагностичним JSON поки є пропозицією. Development migrations до production можна редагувати для disposable БД; постійні дані потребуватимуть incremental migrations. БД/міграції ще не створені.

## Локальний запуск

PHP 8.2+ і Composer 2. PHP baseline для розв'язання залежностей зафіксований у `composer.json`; фактичне встановлення перевірено на PHP 8.4.10. Розширення: ctype, iconv; повні вимоги залежностей перевіряє Composer. Для майбутньої БД потрібні `pdo_sqlite` локально та `pdo_pgsql` для PostgreSQL.

```sh
rtk proxy composer install --prefer-dist --no-plugins --no-scripts
rtk proxy php bin/console about
rtk proxy php vendor/bin/phpunit
rtk proxy php bin/console app:jev:evaluate
rtk proxy php -S 127.0.0.1:8000 -t public public/index.php
```

Відкрити http://127.0.0.1:8000. Вбудований PHP server призначений лише для розробки. Коли з'являться статичні assets, додати окремий dev router або перейти на Symfony CLI, щоб assets не потрапляли у front controller.

`.env` містить лише локальні defaults. Секрети — у `.env.local` або змінних середовища; `APP_SECRET` поки порожній, оскільки сесії/CSRF/підписані URL не підключено. Перед їх додаванням задати випадковий секрет. Для production потрібні `APP_ENV=prod`, `APP_DEBUG=0`, налаштований web server із document root `public/` і окремий етап підготовки деплою.

Runtime-склад: FrameworkBundle, Console, Dotenv, YAML, TwigBundle та HttpClient 7.4. Для dev додано PHPUnit 11.5, BrowserKit і CssSelector. Bootstrap/config створено явно; Flex, Runtime, автоматичні рецепти та Composer scripts не підключені. Нові пакети потребуватимуть явної конфігурації.

Jev: `JEV_MODEL` у `.env` (default pin `jev-1.13.0`), `JEV_API_KEY` тільки у `.env.local`/environment. `JEV_TIMEOUT=0.60` і `JEV_MAX_DURATION=0.80` configurable у секундах. Один POST із intent/topic; без retry, redirect та confidence threshold. Default evaluation replay й tests працюють без ключа/мережі. Live вимагає `--live` і явний `--limit`; [формат звіту й команди](docs/evaluation/README.md).

Перевірки й межі поточного результату: [verification.md](docs/verification.md).
