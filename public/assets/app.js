const trimQuestion = value => value.replace(/^\p{White_Space}+|\p{White_Space}+$/gu, '');
const countQuestion = value => Array.from(trimQuestion(value)).length;
const uncertainMessage = 'We could not confirm receipt of your answer. Your question is still here. Submitting again may create a new result.';

function enableSharing(section) {
    const button = section.querySelector('[data-copy-link]');
    const status = section.querySelector('[data-share-status]');
    const fallback = section.querySelector('[data-share-fallback]');
    const field = section.querySelector('[data-share-url]');
    button.hidden = false;
    button.addEventListener('click', async () => {
        const url = new URL(button.dataset.url, window.location.origin).href;
        try {
            await navigator.clipboard.writeText(url);
            if (new URL(button.dataset.url, window.location.origin).href !== url) return;
            fallback.hidden = true;
            status.textContent = 'Link copied. Certainty is better shared.';
        } catch {
            if (new URL(button.dataset.url, window.location.origin).href !== url) return;
            field.value = url;
            fallback.hidden = false;
            status.textContent = 'Clipboard access is unavailable. Select and copy the link below.';
            field.focus();
            field.select();
        }
    });
}

document.querySelectorAll('[data-verdict]').forEach(enableSharing);

const form = document.querySelector('[data-question-form]');
if (form) {
    const input = form.querySelector('[name="question"]');
    const submit = form.querySelector('[type="submit"]');
    const submitLabel = form.querySelector('[data-submit-label]');
    const counter = form.querySelector('[data-count]');
    const error = form.querySelector('[data-form-error]');
    const composer = document.querySelector('[data-composer]');
    const hero = document.querySelector('[data-hero]');
    const result = document.querySelector('[data-verdict]');
    const suggestions = document.querySelector('[data-suggestions]');
    const examples = suggestions.querySelectorAll('button');
    let pending = false;
    let retryAt = 0;
    let retryTimer;

    function clearError() {
        error.hidden = true;
        error.textContent = '';
        input.removeAttribute('aria-invalid');
    }

    function showError(message, fieldError = false) {
        error.textContent = message;
        error.hidden = false;
        if (fieldError) {
            input.setAttribute('aria-invalid', 'true');
            input.focus();
        }
    }

    function updateCounter() {
        const count = countQuestion(input.value);
        counter.textContent = String(count);
        counter.parentElement.classList.toggle('over-limit', count > 500);
    }

    function updateControls() {
        const remaining = Math.max(0, Math.ceil((retryAt - Date.now()) / 1000));
        submit.disabled = pending || remaining > 0;
        input.readOnly = pending;
        examples.forEach(button => { button.disabled = pending; });
        form.setAttribute('aria-busy', String(pending));
        submitLabel.textContent = pending ? 'Getting your answer…' : remaining > 0 ? `Try again in ${remaining}s` : 'Get answer';
        if (remaining === 0 && retryTimer) {
            clearInterval(retryTimer);
            retryTimer = undefined;
            showError('You can try again now. Your question is still here.');
        }
    }

    function showResult(data) {
        result.querySelector('[data-result-question]').textContent = data.question;
        result.querySelector('[data-result-answer]').textContent = data.answer;
        result.querySelector('[data-copy-link]').dataset.url = data.url;
        result.querySelector('[data-saved-link]').setAttribute('href', data.url);
        result.querySelector('[data-share-status]').textContent = '';
        result.querySelector('[data-share-fallback]').hidden = true;
        composer.hidden = true;
        result.hidden = false;
        hero.classList.add('has-result');
        document.querySelector('[data-home-footnote]').hidden = true;
        result.querySelector('[data-result-heading]').focus();
    }

    function validResult(data) {
        return data && typeof data.id === 'string'
            && /^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/.test(data.id)
            && typeof data.question === 'string' && countQuestion(data.question) > 0
            && typeof data.answer === 'string' && data.answer.trim().length > 0
            && data.url === `/a/${data.id}`;
    }

    input.addEventListener('input', () => {
        updateCounter();
        // Keep request/rate errors visible; only field validation is cleared by editing.
        if (input.hasAttribute('aria-invalid')) clearError();
    });

    examples.forEach(button => button.addEventListener('click', () => {
        if (pending) return;
        input.value = button.dataset.example;
        clearError();
        updateCounter();
        input.focus();
    }));

    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (pending || Date.now() < retryAt) return;
        clearError();
        const question = trimQuestion(input.value);
        const count = Array.from(question).length;
        if (count < 1 || count > 500 || question.includes('\0')) {
            showError('Enter a question between 1 and 500 characters.', true);
            return;
        }

        pending = true;
        updateControls();
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 15000);
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                credentials: 'same-origin',
                redirect: 'error',
                body: JSON.stringify({ question }),
                signal: controller.signal,
            });
            if (response.status === 201) {
                if (!/^application\/json\b/i.test(response.headers.get('Content-Type') || '')) throw new Error('Unexpected response');
                const data = await response.json();
                if (!validResult(data)) throw new Error('Invalid result');
                showResult(data);
            } else if (response.status === 429) {
                const seconds = Number(response.headers.get('Retry-After'));
                const wait = Number.isSafeInteger(seconds) && seconds > 0 ? seconds : 60;
                retryAt = Date.now() + wait * 1000;
                showError(`Too many questions at once. Wait ${wait} seconds, then try again.`);
                retryTimer = setInterval(updateControls, 1000);
            } else if (response.status === 422) {
                showError('Enter a question between 1 and 500 characters.', true);
            } else if (response.status === 413) {
                showError('This question is too large to submit. Shorten it and try again.', true);
            } else if (response.status === 403) {
                showError('This page could not submit the question. Check that you are using the correct site address, then try again.');
            } else if (response.status === 503) {
                showError('An answer is temporarily unavailable. Your question is still here; please try again later.');
            } else if (response.status === 400 || response.status === 415) {
                showError('The question could not be submitted. Your text is still here. Please try again.');
            } else {
                showError(uncertainMessage);
            }
        } catch {
            showError(uncertainMessage);
        } finally {
            clearTimeout(timeout);
            pending = false;
            updateControls();
        }
    });

    result.querySelector('[data-ask-another]').addEventListener('click', () => {
        result.hidden = true;
        result.querySelector('[data-copy-link]').dataset.url = '';
        composer.hidden = false;
        hero.classList.remove('has-result');
        document.querySelector('[data-home-footnote]').hidden = false;
        input.value = '';
        clearError();
        updateCounter();
        input.focus();
    });

    input.disabled = false;
    suggestions.hidden = false;
    updateCounter();
    updateControls();
}
