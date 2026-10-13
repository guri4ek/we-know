# Frontend design proposal

2026-10-12. Proposed information architecture, visual direction, and interaction design; not an accepted implementation scope or a completed UI. Product copy is English. The working brand remains Instant Answer; the final name/domain is unresolved.

## Basis and current evidence

The design follows the accepted dark theme, Symfony/Twig SSR, vanilla JS, contextual absurdity, and immutable public-by-link results. Requirements: [context](context.md), [decisions](architecture/decisions.md), [technical plan](technical-plan.md). Endpoint details: [backend contract](backend.md).

Source inspection for this proposal confirmed:

- `/` is a static placeholder in `templates/home/index.html.twig`.
- `POST /answer` returns a persisted result as JSON; it does not accept an ordinary HTML form submission.
- `/a/{publicId}` renders escaped stored text with canonical/textual OG metadata and noindex/nofollow.
- Result-route 404/503 responses are currently plain text, supplied by `BackendExceptionSubscriber`; a Twig redesign alone will not style them.
- About, Privacy, form interactions, and frontend assets do not exist yet.

No browser validation or live classifier calls were performed. Existing historical test evidence is recorded separately in [verification](verification.md). This document does not author answer-library content.

## Product direction

Present the product as a small, excessively confident institution that issues final verdicts. The visual treatment stays restrained; the absurdity comes from the prepared answer. The first screen should let someone ask immediately, and the result should be readable and shareable in seconds.

Use one column, generous whitespace, strong typography, and a clearly bounded question/result area. Avoid a marketing landing-page sequence, chat bubbles, dashboards, animated thinking, and technical classification details. A visible “Satirical entertainment” label establishes the premise without explaining the joke in every interaction.

## Page map

| Route | Purpose | Main content and action | Priority |
| --- | --- | --- | --- |
| `/` | Ask and receive an answer | Short headline, question form, inline result; `Get answer` | MVP |
| `/a/{publicId}` | Reopen or share one saved verdict | Saved question and answer; `Copy link`, `Ask another question` | MVP |
| `/about` | Explain the premise and answer common questions | Short product explanation and FAQ; `Ask a question` | MVP |
| `/privacy` | Explain what happens to submitted questions | Storage, classification provider, public links, preview exposure, confirmed retention/deletion terms | Before public launch |
| Original requested URL, HTTP 404 | Explain a missing page/result | `Result not found` or `Page not found`; return home | MVP error template |
| Original requested URL, HTTP 503/500 | Explain an unavailable page | Truthful service error; reload for GET or return home | MVP error template |

The last two rows are HTTP states, not standalone `/404` or `/error` destinations. Form validation, rate limits, and POST failures stay beside the form. About includes the FAQ; a separate FAQ page is unnecessary at this size. The Privacy page is a proposed addition to the existing plan, not a claim that policy decisions have already been made.

Shared navigation: wordmark linking home; `About` in the header; `About`, `Privacy`, and the satire label in the footer. Two short text links do not need a hamburger menu.

## Home page

Initial hierarchy:

1. Small wordmark and About link.
2. Headline: `We don't think. We know.`
3. One sentence explaining that this is a satirical answer service.
4. Visible `Your question` label and multiline textarea, with `Ask in English` guidance and a character counter.
5. Storage notice immediately before submission, with a Privacy link.
6. Primary `Get answer` button.
7. Quiet footer.

Proposed storage notice: “Your question is sent to Jev for classification and stored with its answer. Anyone with the result link can read both. Avoid personal or sensitive information.” This describes the normal data flow; it does not promise provider retention terms or private storage. The launch copy must reflect the finalized policy.

Desktop composition (schematic, not a rendered prototype):

```text
Instant Answer                                      About

                We don't think.
                We know.

                Satirical entertainment.

                Your question
                +--------------------------------------+
                |                                      |
                |                                      |
                +--------------------------------------+
                Ask in English                   0 / 500

                [Storage notice · Privacy]
                [             Get answer               ]

                   About · Privacy
```

On success, replace the composer area with the verdict card and reduce the hero's visual prominence. Keep only one current result in the page. Render the server-returned question and answer, rather than assuming the submitted draft equals the saved question. The browser URL remains `/`; `Open saved answer` explicitly opens the returned `/a/{publicId}`. This keeps the initial implementation free of client routing and history synchronization. Reloading `/` returns to the empty form; reopening the copied permalink preserves the verdict.

`Ask another question` restores an empty composer and focuses its label/input. No result history or draft is persisted in localStorage. Do not add a regenerate control to the saved verdict: each explicit new submission creates a separate result, even if its question repeats.

## Shared verdict card and saved-result page

Reuse the same presentation for an inline result and its SSR permalink:

1. Small `FINAL VERDICT` eyebrow.
2. Saved question in secondary, readable text.
3. Prepared answer as the strongest element, with natural wrapping and no truncation.
4. `Copy link` action and `Ask another question` action.
5. On home only, a quieter `Open saved answer` link.

The permalink should lead with the verdict rather than repeat the home hero. Its question, answer, and home link work without JS. Copying uses the saved-result URL, never the current home URL. If clipboard access fails, reveal a selectable URL and a clear message. Optional native sharing can follow later; copying a link is sufficient for MVP.

Do not display the full UUID as decoration, model names, categories, selection tiers, or measured-looking confidence indicators. Keep the route's canonical URL and textual OG metadata. A static brand preview image is optional launch polish; dynamic result-image generation remains deferred. Explain in Privacy that sharing a link can expose its question and answer in third-party previews; noindex is not access control.

## About and Privacy

About should fit in a short reading page. Explain that answers are prepared in advance, a classifier categorizes the question, and the application chooses randomly from the first matching eligible pool. Do not imply that AI writes, evaluates, or ranks the final response. Explain that the same question can receive different answers, while a saved link always shows its stored pair. State that the product is satirical entertainment, not factual or professional advice.

FAQ topics: “Is this AI?”, “Why did I get this answer?”, “Can the same question get another answer?”, and “Who can see my question?”. Link the last answer to Privacy rather than duplicating the full policy.

Privacy must distinguish known behavior from unresolved policy: question/answer storage, transmission for classification, public-by-link access, and metadata previews are known. Retention, deletion/contact process, provider data handling, operational logging, and any analytics/cookies must be confirmed before publishing claims. The current backend has no user-facing delete endpoint. Do not design a nonfunctional delete button or claim that data is never retained. This document is a page/content outline, not a completed legal policy.

## Visual system

Proposed starting tokens; contrast and actual rendering require implementation checks:

| Element | Direction |
| --- | --- |
| Canvas | Near-black graphite, `#101114` |
| Surface | Slightly lighter graphite, `#191B20` |
| Primary text | Warm near-white, `#F3F1EB` |
| Secondary text | Cool gray, `#B1B5BE` |
| Primary action | Muted yellow, `#E7D77A`, with `#101114` text |
| Errors | Soft red, `#FF9B9B`, accompanied by text |
| Typography | System sans-serif; monospace only for small verdict labels |
| Geometry | 12–16 px corner radius, restrained borders, no glow or ornamental gradients |
| Layout | Content column around 680 px; shell around 1040 px; 16–24 px mobile gutters |
| Type scale | Hero 40–64 px, answer 28–40 px, body/input at least 16 px |

Use a single dark theme for MVP. Give fields a clearly visible boundary, and provide a separate high-contrast keyboard focus ring. On narrow screens, stack actions, allow long words/URLs to wrap, and keep the page naturally scrollable above the keyboard. Avoid fixed-height screens and a sticky footer. The design should remain useful at 320 px width and 200% zoom. Keep interactive targets at least 44 px high as a design target.

## Interaction and error states

| State | UI behavior |
| --- | --- |
| Empty/editing | Label, counter, notice, and submit are visible; empty submission produces a field error |
| Invalid question | Inline error linked to the textarea; preserve and focus the input |
| Submitting | Lock editing and duplicate submits; `Getting your answer…`, `aria-busy`; no minimum delay or fake progress |
| Success, HTTP 201 | Display the saved pair and link, move focus to the result heading; provider fallback is an ordinary successful answer |
| HTTP 429 | Preserve input; announce the limit and use `Retry-After` to show when manual submission becomes available; never submit automatically |
| HTTP 503 | Preserve input; explain temporary unavailability and offer manual retry |
| Network failure/lost response | Explain that receipt of the result could not be confirmed; manual resubmission may create a new result; no automatic POST retry |
| HTTP 400/413/415/422 | Show a relevant field/request error; never invent a verdict |
| HTTP 403 or unexpected response | Preserve the question and show a truthful submission error; no raw response HTML, provider data, or stack traces |
| Copy success/failure | Brief `Link copied` status, or selectable-link fallback; keep the verdict visible |
| JS unavailable | Home explanation and navigation remain readable; show that JS is required to submit; saved pages remain readable |

The backend limit is 1–500 Unicode code points after its whitespace trimming, not UTF-16 units. Client validation/counter should align with that contract, including astral characters and Unicode whitespace. A native `maxlength=500` alone is insufficient. The server remains authoritative. Enter inserts a newline; the submit button works through ordinary keyboard activation. Optional Ctrl/Cmd+Enter must respect IME composition.

Use semantic form/label/button elements, visible focus, `aria-describedby` for input guidance/errors, and a polite status region. Announce a rate-limit status once rather than reading a countdown every second. Respect reduced-motion preferences; transitions must not delay content. Never insert returned strings as HTML.

## Implementation boundary and acceptance evidence

The proposed implementation uses a shared Twig shell, verdict partial, home/about/privacy templates, error templates, one stylesheet, and a small JS module. No SPA, UI framework, external fonts, or build pipeline is needed for this scope. The existing JSON endpoint and immutable read route suffice for the core interaction. About/Privacy routes and styled exception responses will require explicit server work in the frontend implementation task. Static asset serving must also be addressed; see the [project setup note](../README.md).

Before implementation, express behavior changes in focused failing tests under the project's test-first rule. Future acceptance evidence should cover:

- One deliberate submit makes one POST; duplicate submits, retries, reloads of saved URLs, and copy actions cause no extra classification.
- Server-returned text and permalink are displayed faithfully; identical saved URLs preserve the pair after catalog changes.
- Empty/long/Unicode questions, HTML-like text, long answers, 429, 503, malformed responses, lost responses, and clipboard failure have usable states.
- Keyboard-only input/submission/result focus, screen-reader status, reduced motion, JS-disabled saved pages, narrow layouts, and zoom work.
- Contrast and focus visibility are checked on the actual rendered design.
- Metadata, noindex/no-store behavior, same-origin JSON submission, and private server diagnostics remain intact.
- Browser/HTTP checks use fake classification and approved synthetic fixtures; implementation checks do not require paid live calls.

This proposal was checked against current controllers/templates, the question validator, and the exception subscriber. Local Markdown links were checked for existing targets. Those checks establish specification consistency only, not frontend acceptance.

## Decisions still open

Confirm final branding, accept or revise this visual direction, and finalize retention/deletion/provider disclosures before public launch. An approved production answer library remains a separate content gate; the current synthetic catalog cannot establish visual/content quality for release.

Accounts, history, public answer feeds, topic browsing, ratings, admin UI, pricing, a separate FAQ, theme settings, and dynamic sharing images remain outside the proposed MVP. Revisit them only for a concrete product need.
