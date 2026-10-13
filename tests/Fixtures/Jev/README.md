# Synthetic Jev fixtures

Schema source: [official TypeSafe API reference](https://docs.typesafe.ai/api) and [Choice](https://docs.typesafe.ai/primitives/choice), checked 2026-10-11. These are author-written **offline fixtures, not recorded live traffic**. No real keys, headers or questions are stored here.

`success-v2.json` covers the documented model/answers/usage response, both Choice distributions and integer token counters. `ResponseFixture` derives deliberate schema violations for unit and MockHttpClient integration tests. Provider confidence is validated as finite numeric [0,1]; tests do not assume exact formula equality or introduce an acceptance threshold.

`evaluation-v2.json` replays six v2 development IDs through the real adapter: two jointly correct predictions, one wrong intent, one wrong topic and two HTTP 500 failures. Known fixture totals: intent 3/6, topic 3/6, joint 2/6, technical failures 2/6, tokens 400 input / 80 output from four successes. Timing is mock execution time, not provider performance. These invented outputs establish no model classifications. Fixture metadata binds corpus/taxonomy/rubric 2.0.0.

The original `success.json` and `evaluation-v1.json` remain historical synthetic files. They are not accepted as current responses or relabeled as current classification evidence. Strict response validation now requires all 14 intent and 13 topic probabilities.

`DenyNetworkClient` is the default test DI client; an unplanned call throws immediately. Default tests inject explicit response fixtures and never use a live API credential.
