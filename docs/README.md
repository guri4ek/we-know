# Технічна документація

Стан: 2026-10-11. Документація українською; продукт і стартова бібліотека відповідей англійською.

| Документ | Призначення |
| --- | --- |
| [Контекст](context.md) | Перенесені рішення, еволюція задуму, аналоги, відкладені ідеї та джерела |
| [Технічний план](technical-plan.md) | Межі MVP, HTTP-контракт, етапи й критерії готовності |
| [C4](architecture/c4.md) | Context, Containers, Components та послідовність запиту |
| [Дані](architecture/data.md) | Пропозиція нормалізації, незмінні результати, SQLite/PostgreSQL |
| [Рішення](architecture/decisions.md) | Прийняті рішення, пропозиції та відкриті питання |
| [Дослідження Jev](research/jev.md) | Перевірений API-контракт, моделі, ліміти, confidence та невизначеності |
| [Механізми гумору](research/humor-mechanisms.md) | Первинні джерела, межі доказів, практичні механізми та відкриті гіпотези |
| [Інструкція авторингу відповідей](content/answer-generation-guide.md) | Офлайн workflow для Codex, proposed codes, rubric, smoke corpus і draft JSON |
| [Таксономія v1](content/taxonomy-v1.md) | Активні intent/topic, правила ambiguity, межі ANY та rubric version |
| [Приклади етапу 1](content/stage-1-answer-examples.md) | Окремі hold drafts форми відповіді й негативні probes |
| [Jev evaluation](evaluation/README.md) | Development/holdout corpus, offline/live runner, знаменники метрик |
| [Результат етапу 1](evaluation/stage-1-results.md) | Фактичний development live baseline та невиконані перевірки |
| [Стратегія тестів](testing.md) | Unit, offline/live Jev integration, БД, HTTP/JS, якість і швидкість |
| [Перевірки](verification.md) | Фактичний стан skeleton і виконані перевірки |

## Правила продовження

**Перед створенням або редагуванням текстів відповідей прочитати [answer-generation-guide.md](content/answer-generation-guide.md) повністю.** Генерація в цьому guide означає офлайн авторинг бібліотеки з людською редактурою; приклади не є готовим seed, а mechanism metadata не впливає на selector.

Окрема дослідницька сесія 2026-10-11 додала локальні матеріали про гумор. Її обсяг — дослідження й документація, без бізнес-реалізації, БД, commit/push або субагентів; попередня історія Git-дозволів нижче не розширює це доручення.

Поточний реалізований обсяг: skeleton, taxonomy/corpus, Jev adapter і CLI runner етапу 1, включно з development live baseline. Документація не є дозволом реалізувати наступні етапи. Відокремлювати реалізовані classification компоненти від майбутнього selector/БД/HTTP сценарію.

Розрізняти **рішення користувача**, **архітектурні пропозиції** та **факти перевіреної реалізації**. C4 показує цільову MVP-архітектуру, а не вже написаний код. Оновлювати статус документів разом зі зміною реалізації.

Останні уточнення схеми: усі таблиці мають UUID PK/created_at; eligibility — окремий UUID, unique answer/intent/topic і `humor_mechanism`, який selector ігнорує; revision-поля прибрано. Спрощений VERDICT із nullable eligibility FK та `decision_context` залишається **пропозицією**. До production дозволено редагувати development migrations і перебудовувати disposable БД; після появи спільних/постійних даних — незмінні incremental migrations. Деталі — [дані](architecture/data.md) і [ADR-014–017](architecture/decisions.md).

Репозиторій: https://github.com/guri4ek/we-know. Serhii спочатку залишив Git собі, згодом явно дозволив Git і субагентів, а потім доручив commit/push основи та уточнень документації. Локальний baseline і перевірки зафіксовані у [verification.md](verification.md). Це історія bootstrap delivery; поточне доручення дозволило тільки scoped етап 1, без Git/deployment та наступних етапів. Особисті Codex/Claude конфігурації не змінювалися.
