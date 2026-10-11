# Етап 1: фактичний результат

Дата: 2026-10-11. Offline implementation завершено; **live contract і development baseline фактично перевірено**, тому результат не обмежується mock spike. Це не production acceptance, independently human-verified quality чи завершений продукт.

## Реалізація

- [Taxonomy/rubric v1](../content/taxonomy-v1.md): 9 intents / 7 topics, ambiguity rules, ANY тільки для майбутньої answer eligibility. Рішення, які уточнюють proposal, описані явно.
- [Corpus 1.0.0](README.md): 54 development + 27 holdout, англійський synthetic author-labeled baseline. Independent human review відсутній; holdout не використовувався для налаштування rubric й не викликався live.
- `QuestionClassifier`, string enums, typed success/failure; `JevClassifier`, strict response normalizer, Symfony DI й dedicated plain HttpClient. Один POST із двома Choice, без library/humor metadata, retry або redirects; failure лишається failure, без вигаданих OTHER/GENERAL. No confidence threshold.
- HttpClient **7.4.20**, contracts **3.7.3**, polyfill-php83 **1.43.0** — єдині додані packages. Composer baseline лишається PHP 8.2.
- `app:jev:evaluate`: offline replay через справжній adapter або явно дозволений bounded live run. Model/rubric/corpus versions, count, accuracy/denominators, confusion matrices, technical failures, latency й tokens у JSON. Default suite не потребує ключа/мережі.
- [Приклади відповідей](../content/stage-1-answer-examples.md) підготовлені після читання завершених research/guide; ці два файли не редагувалися. Drafts мають hold і відомі невдалі probes, без import/schema чи оцінки смішності.

## Дві development live серії

Умови: локальний CLI, PHP **8.4.10**, Symfony **7.4.20**, `APP_ENV=prod APP_DEBUG=0`, той самий corpus/rubric/model pin, послідовні calls, один на sample, retries=0. Ключ — у `.env.local`, не в args/logs/reports. Provider network path/production region не вимірювалися; це не endpoint POST `/answer` і не його SLA. Перед запуском очищено старий локальний prod cache.

Serhii явно дозволив перші 54 calls, потім ще 54 для більшого timeout. Загалом **108 API requests, 95 валідних successes, 13 timeout**. Повторні питання не вважаються 108 незалежними corpus samples. Результати між серіями не використовувалися для підлаштування rubric/labels; змінено лише timeout policy за прямим запитом.

| Метрика | [300/400 ms](live-development-v1.json) | [600/800 ms](live-development-v1-600-800.json) |
| --- | --- | --- |
| Requested/resolved model | `jev-1.13.0` / 41 successes | `jev-1.13.0` / 54 successes |
| Corpus/rubric/taxonomy | `1.0.0` / development | `1.0.0` / development |
| Attempted / valid successes | 54 / 41 | 54 / 54 |
| Intent accuracy, all attempts | 38/54 = **70.4%** | 52/54 = **96.3%** |
| Topic accuracy, all attempts | 38/54 = **70.4%** | 52/54 = **96.3%** |
| Joint accuracy, all attempts | 35/54 = **64.8%** | 50/54 = **92.6%** |
| Intent / topic, among successes | 38/41 = **92.7%** кожна | 52/54 = **96.3%** кожна |
| Joint, among successes | 35/41 = **85.4%** | 50/54 = **92.6%** |
| Technical failure rate | 13/54 = **24.1%**, усі timeout | 0/54 = **0%** у цій серії |
| P50 / P95, all 54 attempts | **249.8 / 304.6 ms** | **238.4 / 408.9 ms** |
| Known input / output tokens | **41,628 / 6,325**, usage відомий для 41/54 | **54,826 / 8,330**, usage відомий для 54/54 |

Percentiles — nearest rank над усіма attempts. Перший P95 містить обірвані timeout спроби й **не показує повний час відповіді API для них**. Друга серія успішна, але 54 samples замало для гарантії zero failure чи стабільного P95. Різниця не є контрольованим причинним доказом: мережа/provider load між runs могли змінитися, а склад successes першої серії інший. Це evidence, що 300 ms idle budget у поточному локальному запуску надто агресивний. **Після порівняння Serhii прямо обрав 600/800 ms як поточні defaults** у `.env` та adapter/runner. Початкові 300/400 ms лишаються історичним baseline й відтворюються env override. Рішення не є production SLA; retry/threshold не додані.

Full confusion matrices та per-sample IDs/predictions/confidence/latency/usage — у двох JSON reports. Raw питання/ключ/provider errors у них не зберігаються. Сумарно відомо **96,454 input / 14,655 output tokens**; usage 13 timeout calls невідомий, вони могли бути billed. Billing/dashboard/баланс не читалися.

## Semantic mismatches

У другій серії лишилося чотири joint mismatches:

| Sample | Author label | Jev | Evidence gap |
| --- | --- | --- | --- |
| dev-006: purpose of a morning routine | WHAT / LIFE | WHY / LIFE | Purpose питання близьке до why; авторську WHAT-розмітку має незалежно перевірити людина |
| dev-024: organizing team code reviews | HOW / WORK | HOW / TECH | Межа організації людей й технічного предмета не завжди виконується |
| dev-034: release approval + server repair | WHO / WORK | WHO / TECH | Topic може зміщуватися на другу частину; правило першого питання не гарантоване |
| dev-053: instruction-only joke generator / key request | OTHER / GENERAL | WHAT / GENERAL | Instruction-only форма прочитана як WHAT; output лишився typed label, ключ/жарт не генерувався |

У першій серії також були dev-032 (topic другого питання MONEY замість TECH) і dev-042 (HOW замість CHOICE); у другій вони збіглися з авторськими labels. Це варіативність конкретних predictions, а не підтвердження виправлення rubric. Labels/prompt не змінювалися.

## Sandbox, незавершені перевірки й наступний handoff

На дату перевірки у відкритому [official documentation index](https://docs.typesafe.ai/llms.txt) та API reference **не знайдено документованого окремого sandbox endpoint або спеціальних test keys**. [Quick start](https://docs.typesafe.ai/introduction/quickstart) описує Playground і звичайний endpoint із dashboard key. Це висновок про доступні документи, не доказ відсутності приватної/enterprise можливості. Нові calls у цій задачі не потрібні й не виконуються після дозволених 108.

Лишилося неперевіреним: live holdout; незалежна human review corpus labels; production region/concurrency, довготривалий failure/latency baseline; фактичний billing; реальний PHP 8.2 runtime; доречність/смішність draft фраз. HTTP error matrix перевірена mock, не провокувалася на справжньому provider. За результатом v1 confidence threshold не вводиться.

Для наступного live evaluation достатньо наявного project key, явного дозволу на split/model/timeout та **нового ліміту requests**. Holdout — окремий frozen corpus, 27 calls; до нього треба зафіксувати rubric й policy, не використовувати його для tuning. [Команди відтворення](README.md). Ключі/акаунт уже працюють; нового пошуку секретів чи іншого account не потрібно.

Offline перевірки — [verification](../verification.md). БД, migrations/import, selector, VERDICT/decision_context, POST/fetch, permalink й admin не реалізовані. Git/deployment/Claude/особисті конфігурації не змінювалися; чужі дослідницькі artifacts збережено. Наступні етапи не запускалися.
