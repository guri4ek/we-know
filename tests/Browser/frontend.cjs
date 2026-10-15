// Run with Node 22+ and Playwright available through node_modules or NODE_PATH.
const { test, before, after } = require('node:test');
const assert = require('node:assert/strict');
const { spawn } = require('node:child_process');
const { once } = require('node:events');
const { setTimeout: delay } = require('node:timers/promises');
const { chromium } = require('playwright');
const path = require('node:path');

const root = path.resolve(__dirname, '../..');
const origin = 'http://127.0.0.1:8137';
const savedPath = '/a/10000000-0000-4000-8000-000000000099';
let server;
let browser;

before(async () => {
    server = spawn('php', ['-S', '127.0.0.1:8137', '-t', 'public', 'tests/Fixtures/Frontend/router.php'], {
        cwd: root,
        env: { ...process.env, APP_ENV: 'test', APP_DEBUG: '1', JEV_API_KEY: '' },
        stdio: 'ignore',
    });
    let ready = false;
    for (let attempt = 0; attempt < 60; attempt++) {
        if (server.exitCode !== null) throw new Error('Browser fixture server could not start.');
        try {
            const response = await fetch(origin);
            if (response.ok) { ready = true; break; }
        } catch {}
        await delay(100);
    }
    assert.ok(ready, 'Fixture server responds successfully');
    browser = await chromium.launch({ headless: true, channel: process.env.PLAYWRIGHT_CHANNEL || 'chromium' });
});

after(async () => {
    await browser?.close();
    if (server && server.exitCode === null) {
        server.kill();
        await once(server, 'exit');
    }
});

async function withPage(run, options = {}) {
    const context = await browser.newContext(options);
    const page = await context.newPage();
    page.setDefaultTimeout(4000);
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    try {
        await page.goto(origin);
        await run(page, context);
        assert.deepEqual(errors, [], 'No uncaught frontend errors');
    } finally { await context.close(); }
}

async function mockAnswer(page, response, status = 201, headers = {}) {
    await page.route('**/answer', route => route.fulfill({
        status, contentType: 'application/json', headers, body: JSON.stringify(response),
    }));
}

const result = { id: savedPath.slice(3), question: 'Server-trimmed question 🦉', answer: '<img src=x onerror=alert(1)> Synthetic answer.', url: savedPath };

test('real assets load and one pending submit displays server text safely, focuses result and resets', async () => {
    await withPage(async page => {
        assert.match((await page.request.get(`${origin}/assets/app.css`)).headers()['content-type'], /text\/css/);
        let requests = 0;
        let release;
        const gate = new Promise(resolve => { release = resolve; });
        await page.route('**/answer', async route => {
            requests++;
            assert.deepEqual(route.request().postDataJSON(), { question: 'Original question' });
            assert.equal(route.request().headers()['content-type'], 'application/json');
            await gate;
            await route.fulfill({ status: 201, contentType: 'application/json', body: JSON.stringify(result) });
        });
        await page.locator('#question').fill('Original question');
        await page.getByRole('button', { name: 'Get answer', exact: true }).click();
        await page.waitForFunction(() => document.querySelector('form').getAttribute('aria-busy') === 'true');
        await page.locator('form').evaluate(form => form.dispatchEvent(new Event('submit', { cancelable: true })));
        assert.equal(requests, 1);
        assert.equal(await page.locator('#question').getAttribute('readonly'), '');
        release();
        await page.locator('[data-verdict]:visible').waitFor();
        assert.equal(await page.locator('[data-result-question]').textContent(), result.question);
        assert.equal(await page.locator('[data-result-answer]').textContent(), result.answer);
        assert.equal(await page.locator('[data-verdict] img').count(), 0);
        assert.equal(await page.locator('[data-result-heading]').evaluate(el => el === document.activeElement), true);
        assert.equal(await page.getByRole('link', { name: 'Open saved answer' }).getAttribute('href'), savedPath);
        assert.equal(page.url(), `${origin}/`);
        await page.getByRole('button', { name: 'Ask another question' }).click();
        assert.equal(await page.locator('#question').inputValue(), '');
        assert.equal(await page.locator('#question').evaluate(el => el === document.activeElement), true);
        assert.equal(requests, 1);
    });
});

test('validation and counter match trimmed Unicode code points without UTF-16 maxlength', async () => {
    await withPage(async page => {
        let requests = 0;
        await page.route('**/answer', async route => { requests++; await route.fulfill({ status: 503, contentType: 'application/json', body: '{}' }); });
        await page.getByRole('button', { name: 'Get answer', exact: true }).click();
        await page.locator('#question[aria-invalid="true"]').waitFor();
        await page.locator('#question').fill('\u0085 \u2003');
        assert.equal(await page.locator('[data-count]').textContent(), '0');
        await page.locator('#question').fill('🦉'.repeat(501));
        assert.equal(await page.locator('[data-count]').textContent(), '501');
        await page.getByRole('button', { name: 'Get answer', exact: true }).click();
        assert.equal(requests, 0);
        await page.locator('#question').fill('\u0085' + '🦉'.repeat(500) + '\u0085');
        assert.equal(await page.locator('[data-count]').textContent(), '500');
        await page.getByRole('button', { name: 'Get answer', exact: true }).click();
        await page.locator('[data-form-error]:visible').waitFor();
        assert.equal(requests, 1);
    });
});

test('rate limit blocks manual resubmission until Retry-After and never retries itself', async () => {
    await withPage(async page => {
        let requests = 0;
        await page.route('**/answer', route => {
            requests++;
            return route.fulfill({ status: 429, contentType: 'application/json', headers: { 'Retry-After': '2' }, body: '{"error":{"code":"rate_limited"}}' });
        });
        await page.locator('#question').fill('Keep this question');
        await page.getByRole('button', { name: 'Get answer', exact: true }).click();
        await page.locator('[data-form-error]:visible').waitFor();
        assert.equal(await page.locator('button[type="submit"]').isDisabled(), true);
        assert.equal(await page.locator('#question').inputValue(), 'Keep this question');
        await page.waitForFunction(() => !document.querySelector('button[type="submit"]').disabled);
        assert.equal(requests, 1);
        assert.match(await page.locator('[data-form-error]').textContent(), /try again/i);
    });
});

test('HTTP failures, malformed success and network loss preserve the draft without inventing a result', async () => {
    await withPage(async page => {
        for (const status of [400, 403, 413, 415, 422, 500, 503]) {
            await page.unroute('**/answer');
            await mockAnswer(page, { error: { message: 'INTERNAL SECRET' } }, status);
            await page.locator('#question').fill('Keep this draft');
            await page.locator('button[type="submit"]').click();
            await page.locator('[data-form-error]:visible').waitFor();
            assert.equal(await page.locator('#question').inputValue(), 'Keep this draft');
            assert.doesNotMatch(await page.locator('[data-form-error]').textContent(), /INTERNAL SECRET/);
            assert.equal(await page.locator('[data-verdict]').isVisible(), false);
        }
        for (const body of ['<html>Error</html>', '{}', JSON.stringify({ ...result, url: 'https://evil.example/' }), JSON.stringify({ ...result, id: 'wrong-id' })]) {
            await page.unroute('**/answer');
            await page.route('**/answer', route => route.fulfill({ status: 201, contentType: 'application/json', body }));
            await page.locator('button[type="submit"]').click();
            await page.locator('[data-form-error]:visible').waitFor();
            assert.match(await page.locator('[data-form-error]').textContent(), /could not confirm/i);
            assert.equal(await page.locator('#question').inputValue(), 'Keep this draft');
        }
        await page.unroute('**/answer');
        await page.route('**/answer', route => route.abort());
        await page.locator('button[type="submit"]').click();
        await page.locator('[data-form-error]:visible').waitFor();
        assert.match(await page.locator('[data-form-error]').textContent(), /new result/i);
    });
});

test('copy success and denied clipboard fallback use the saved URL', async () => {
    await withPage(async page => {
        await mockAnswer(page, result);
        await page.evaluate(() => { Object.defineProperty(navigator, 'clipboard', { configurable: true, value: { writeText: async text => { window.copiedLink = text; } } }); });
        await page.locator('#question').fill('Copy this result');
        await page.locator('button[type="submit"]').click();
        await page.locator('[data-verdict]:visible').waitFor();
        await page.getByRole('button', { name: 'Copy link', exact: true }).click();
        await page.waitForFunction(() => Boolean(window.copiedLink));
        assert.equal(await page.evaluate(() => window.copiedLink), `${origin}${savedPath}`);
        await page.evaluate(() => { Object.defineProperty(navigator, 'clipboard', { configurable: true, value: { writeText: async () => { throw new Error('Denied'); } } }); });
        await page.getByRole('button', { name: 'Copy link', exact: true }).click();
        await page.locator('[data-share-url]:visible').waitFor();
        assert.equal(await page.locator('[data-share-url]').inputValue(), `${origin}${savedPath}`);
        assert.equal(await page.locator('[data-share-url]').evaluate(el => el.selectionEnd - el.selectionStart), `${origin}${savedPath}`.length);
    });
});

test('saved results and informative pages work without JavaScript', async () => {
    await withPage(async page => {
        assert.equal(await page.locator('button[type="submit"]').isDisabled(), true);
        assert.match(await page.locator('noscript').textContent(), /JavaScript/);
        await page.goto(origin + savedPath);
        assert.match(await page.locator('[data-result-answer]').textContent(), /Synthetic saved answer/);
        assert.equal(await page.locator('script:not([src])').count(), 0);
        assert.equal(await page.locator('a[href="/"]').count() > 0, true);
        assert.equal(await page.locator('link[rel="canonical"]').getAttribute('href'), 'http://localhost' + savedPath);
        for (const url of ['/about', '/privacy']) assert.equal((await page.goto(origin + url)).status(), 200);
    }, { javaScriptEnabled: false });
});

test('320px layout, long saved text and reduced motion keep content within the viewport', async () => {
    await withPage(async page => {
        for (const url of ['/', savedPath, '/about', '/privacy', '/a/missing', '/missing']) {
            await page.goto(origin + url);
            assert.equal(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth), true, url);
        }
        await page.goto(origin);
        await page.keyboard.press('Tab');
        assert.equal(await page.locator('.skip-link').evaluate(el => el === document.activeElement), true);
        await page.keyboard.press('Enter');
        assert.equal(await page.locator('main').evaluate(el => el === document.activeElement), true);
    }, { viewport: { width: 320, height: 720 }, reducedMotion: 'reduce' });
});

test('examples only fill the input, Enter inserts a newline and keyboard activation submits once', async () => {
    await withPage(async page => {
        let requests = 0;
        await page.route('**/answer', route => {
            requests++;
            return route.fulfill({ status: 201, contentType: 'application/json', body: JSON.stringify(result) });
        });
        await page.getByRole('button', { name: 'Should I quit my job?' }).click();
        assert.equal(await page.locator('#question').inputValue(), 'Should I quit my job?');
        assert.equal(requests, 0);
        await page.keyboard.press('End');
        await page.keyboard.press('Enter');
        assert.match(await page.locator('#question').inputValue(), /\n$/);
        assert.equal(requests, 0);
        await page.locator('button[type="submit"]').focus();
        await page.keyboard.press('Space');
        await page.locator('[data-verdict]:visible').waitFor();
        assert.equal(requests, 1);
    });
});

test('rendered text, field boundary and keyboard focus have sufficient contrast', async () => {
    await withPage(async page => {
        const checks = await page.evaluate(() => {
            const luminance = color => {
                const rgb = color.match(/[\d.]+/g).slice(0, 3).map(Number).map(v => v / 255).map(v => v <= .04045 ? v / 12.92 : ((v + .055) / 1.055) ** 2.4);
                return rgb[0] * .2126 + rgb[1] * .7152 + rgb[2] * .0722;
            };
            const ratio = (a, b) => (Math.max(luminance(a), luminance(b)) + .05) / (Math.min(luminance(a), luminance(b)) + .05);
            const canvas = getComputedStyle(document.documentElement).backgroundColor;
            const surface = getComputedStyle(document.querySelector('form')).backgroundColor;
            const text = ['h1', '.hero-description', '.storage-notice', '.suggestion-label', '.home-footnote', '.site-footer'].map(selector => {
                const element = document.querySelector(selector);
                return [selector, ratio(getComputedStyle(element).color, selector === '.storage-notice' ? surface : canvas)];
            });
            const textarea = getComputedStyle(document.querySelector('textarea'));
            const button = getComputedStyle(document.querySelector('button[type="submit"]'));
            return { text, boundary: ratio(textarea.borderTopColor, surface), button: ratio(button.color, button.backgroundColor) };
        });
        for (const [selector, contrast] of checks.text) assert.ok(contrast >= 4.5, selector);
        assert.ok(checks.boundary >= 3);
        assert.ok(checks.button >= 4.5);
        await page.locator('#question').focus();
        const focus = await page.locator('#question').evaluate(el => ({ width: getComputedStyle(el).outlineWidth, style: getComputedStyle(el).outlineStyle }));
        assert.equal(focus.style, 'solid');
        assert.equal(focus.width, '2px');
    });
});

test('200% zoom and a long unbroken answer wrap without horizontal scrolling', async () => {
    await withPage(async page => {
        await mockAnswer(page, { ...result, question: '🦉'.repeat(500), answer: 'x'.repeat(1500) });
        await page.evaluate(() => { document.body.style.zoom = '2'; });
        await page.locator('#question').fill('A synthetic layout question');
        await page.locator('button[type="submit"]').click();
        await page.locator('[data-verdict]:visible').waitFor();
        assert.equal(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth), true);
        assert.equal((await page.locator('[data-result-answer]').textContent()).length, 1500);
    }, { viewport: { width: 1280, height: 900 } });
});
