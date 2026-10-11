# We Know / Instant Answer

**We don't think. We know.**

Сатиричний англомовний сервіс: питання → класифікація → випадкова готова абсурдна відповідь відповідної категорії → постійне посилання.

Репозиторій: https://github.com/guri4ek/we-know

## Поточний стан

Старт проєкту, 2026-10-11. Встановлено мінімальні Symfony 7.4 LTS та Twig. Є лише `GET /` зі статичною серверною заглушкою. Бізнес-логіку, POST fetch, інтеграцію Jev, базу даних і permalink ще не реалізовано.

Почніть із [карти документації](docs/README.md), [контексту вихідної сесії](docs/context.md), [технічного плану](docs/technical-plan.md) та [C4-моделі](docs/architecture/c4.md).

[Модель даних](docs/architecture/data.md) враховує UUID PK/created_at у всіх таблицях і `humor_mechanism` на eligibility без впливу на random. Спрощений VERDICT із діагностичним JSON поки є пропозицією. Development migrations до production можна редагувати для disposable БД; постійні дані потребуватимуть incremental migrations. БД/міграції ще не створені.

## Локальний запуск

PHP 8.2+ і Composer 2. PHP baseline для розв'язання залежностей зафіксований у `composer.json`; фактичне встановлення перевірено на PHP 8.4.10. Розширення: ctype, iconv; повні вимоги залежностей перевіряє Composer. Для майбутньої БД потрібні `pdo_sqlite` локально та `pdo_pgsql` для PostgreSQL.

```sh
composer install --prefer-dist --no-plugins --no-scripts
php bin/console about
php vendor/bin/phpunit
php -S 127.0.0.1:8000 -t public public/index.php
```

Відкрити http://127.0.0.1:8000. Вбудований PHP server призначений лише для розробки. Коли з'являться статичні assets, додати окремий dev router або перейти на Symfony CLI, щоб assets не потрапляли у front controller.

`.env` містить лише локальні defaults. Секрети — у `.env.local` або змінних середовища; `APP_SECRET` поки порожній, оскільки сесії/CSRF/підписані URL не підключено. Перед їх додаванням задати випадковий секрет. Для production потрібні `APP_ENV=prod`, `APP_DEBUG=0`, налаштований web server із document root `public/` і окремий етап підготовки деплою.

Runtime-склад мінімальний: FrameworkBundle, Console, Dotenv, YAML, TwigBundle. Для dev додано PHPUnit 11.5, BrowserKit і CssSelector. Bootstrap/config створено явно; Flex, Runtime, автоматичні рецепти та Composer scripts не підключені. Нові пакети потребуватимуть явної конфігурації. Це дозволяє стартувати без додаткових генераторів, Git-автоматизації та зайвих пакетів.

Перевірки й межі поточного результату: [verification.md](docs/verification.md).
