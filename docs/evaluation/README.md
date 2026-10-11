# Jev evaluation v1

Дата: 2026-10-11. Taxonomy/rubric/corpus version: **1.0.0**. [Таксономія й tie-break rules](../content/taxonomy-v1.md) зафіксовані до live запуску; точний provider payload будує `JevRubric`.

## Corpus

- [development.json](corpus-v1/development.json): **54** синтетичні англомовні питання; для розробки й першого live baseline.
- [holdout.json](corpus-v1/holdout.json): **27** окремих питань; не використовувати для підлаштування rubric. Live holdout у цій задачі не дозволений і не запускався.
- `label_status=author_labels_not_independently_reviewed`: авторська розмітка, **ще без незалежної людської перевірки**. Є всі 9 intents/7 topics, ambiguity, negation, typos, non-question text та instruction attacks. Це малий контрольний corpus, не репрезентативна вибірка production traffic.
- Splits мають різні IDs й тексти. Schema/coverage tests читають обидва, але не підлаштовують rubric за holdout. Будь-яка зміна текстів/labels/split потребує нової corpus версії; порядок також фіксований, `--limit` бере префікс.

## Запуск

Default offline replay використовує шість навмисно змішаних fixtures із `tests/Fixtures/Jev/evaluation-v1.json` та **справжній JevClassifier через MockHttpClient**. Це не оцінка моделі. Очікувано: 4 валідні successes, 2 failures; intent 3/6, topic 3/6, joint 2/6, відомі tokens 400 input / 80 output. Mock latency не є provider latency.

```sh
rtk proxy php vendor/bin/phpunit
rtk proxy php bin/console app:jev:evaluate
rtk proxy php bin/console app:jev:evaluate --limit=2
```

Runner не шукає ключі в особистих конфігураціях. За уточненням Serhii цей малий проєкт використовує **один JEV_API_KEY** для adapter/evaluation. Задати його у вже ігнорованому `.env.local` або process environment, не в CLI argument і не у committed `.env`. Default offline replay не використовує його й не виходить у мережу. `JEV_MODEL` configurable; default pin `jev-1.13.0`. Test container примусово має dummy/empty credentials і deny-network client.

Live вимагає **одночасно `--live`, непорожній JEV_API_KEY/JEV_MODEL та явний `--limit=1..1000`**. Відсутність precondition — exit 2 до мережі. Немає retries, паралельних calls чи sleep; максимум один call на вибране питання. Configurable `JEV_TIMEOUT` / `JEV_MAX_DURATION` у секундах: поточні defaults 0.60 / 0.80 за рішенням Serhii після порівняння; початковий baseline 0.30 / 0.40, позитивний timeout і max_duration >= timeout; redirects 0. Друга дозволена development серія виконана з 0.60 / 0.80; після неї Serhii обрав ці значення як defaults. Policy записується у report. За наявності technical failure live report все одно записується, exit 1; завершений без failures запуск — exit 0. Загальна кількість calls не перевищує limit і sample count вибраного split.

Для існуючого production cache спочатку оновити його локально після зміни services/source. Команди нижче повторно витрачають provider budget і запускаються тільки за явним дозволом:

```sh
rtk proxy env APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear
rtk proxy env APP_ENV=prod APP_DEBUG=0 JEV_TIMEOUT=0.30 JEV_MAX_DURATION=0.40 php bin/console app:jev:evaluate --live --split=development --limit=54 > /private/tmp/jev-development-report.json
# Порівняння з більшим budget, також потребує окремого дозволу на 54 calls:
rtk proxy env APP_ENV=prod APP_DEBUG=0 JEV_TIMEOUT=0.60 JEV_MAX_DURATION=0.80 php bin/console app:jev:evaluate --live --split=development --limit=54 > /private/tmp/jev-development-600-800-report.json
# Окремий наступний запуск, лише після дозволу на holdout:
rtk proxy env APP_ENV=prod APP_DEBUG=0 php bin/console app:jev:evaluate --live --split=holdout --limit=27 > /private/tmp/jev-holdout-report.json
```

Offline fixtures format: object із `corpus_version`, `split`, `responses`; responses keyed by corpus sample ID, кожен має integer `status` та string `body`. `--offline-fixtures=/absolute/path.json` дозволяє іншу локальну replay-вибірку з цього split. Невідомі IDs, невідповідна версія/split чи malformed fixture відхиляються до runner. Не копіювати live Bearer headers/raw traffic у fixtures.

## Знаменники й обмеження метрик

Report містить requested model, **resolved model counts** тільки з валідних responses, taxonomy/rubric/corpus versions, split, UTC timestamp, corpus size і attempted sample count. Кожний sample має ID, expected/predicted labels або failure, latency й відомий usage; raw питання, ключі й provider errors не записуються.

- Основні intent/topic/joint accuracy = correct / **усі attempted samples**, technical failures рахуються incorrect. Додаткові conditional accuracy = correct / **успішні валідні responses**. Кожна метрика містить numerator, denominator, value; для нульового denominator value=`null`.
- Technical failure rate = typed failures / **усі attempted samples**; breakdown за причинами. Це **не fallback rate через порожній каталог**: selector/БД ще немає. HTTP statuses класифікуються окремо від semantic mistakes.
- Confusion matrices: rows=expected, columns=predicted; спеціальний стовпець `TECHNICAL_FAILURE`. Валідний OTHER/GENERAL зберігається як справжній prediction.
- Latency P50/P95: **nearest rank** `ceil(p*N)-1` у відсортованих elapsed milliseconds, усі attempts, включно з failures. Це wall time classifier (HTTP + parsing), не POST/end-to-end SLA. Offline replay latency має окрему явну позначку.
- Token usage: сума **лише валідованих successes** й count із/без відомого usage. Failure може бути billed; ці totals не видаються за повний billing. Mock tokens — синтетичні. Вартість runner не вигадує.

Немає confidence threshold, accepted/rejected coverage policy або автоматичного release gate. Високий development score не доводить holdout quality; labels не є незалежним human gold. Перевірка гумору відокремлена від classification evaluation; content drafts не передаються Jev.

Фактичний live результат і обмеження: [stage-1-results.md](stage-1-results.md). Машинні reports: [300/400 ms](live-development-v1.json) та [600/800 ms](live-development-v1-600-800.json). Offline matrix й відтворювані перевірки — [verification](../verification.md).
