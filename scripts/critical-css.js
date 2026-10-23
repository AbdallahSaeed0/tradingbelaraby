/**
 * Generate inline "critical" CSS for a page (see layouts/app.blade.php).
 *
 * Loads the page in headless Chrome and keeps every rule of the layout CSS bundle that
 * matches an element outside the below-the-fold content: the header, the first <main>
 * section (hero), and body-level fixed/hidden elements (bottom nav, offcanvas, modals...).
 * Media queries are kept as-is, so one file serves mobile and desktop.
 *
 * Re-run after changing the layout/home CSS:
 *   npm run critical-css -- https://tradingbelaraby.com/ home-rtl
 * Requires Chrome; set CHROME_PATH if it is not in the default location.
 */
import fs from 'node:fs';
import path from 'node:path';
import puppeteer from 'puppeteer-core';

const [url = 'https://tradingbelaraby.com/', key = 'home-rtl'] = process.argv.slice(2);
const chromePath = process.env.CHROME_PATH
    || (process.platform === 'win32'
        ? 'C:/Program Files/Google/Chrome/Application/chrome.exe'
        : '/usr/bin/google-chrome');

if (!fs.existsSync(chromePath)) {
    console.error(`Chrome was not found at ${chromePath}.
This script only needs to reach the public URL, so run it on a machine with Chrome
(e.g. your development PC), then deploy the generated public/css/critical/${key}.css.
Or set CHROME_PATH to a Chrome/Chromium binary.`);
    process.exit(1);
}

const browser = await puppeteer.launch({ executablePath: chromePath, headless: true });
try {
    const page = await browser.newPage();
    await page.setViewport({ width: 412, height: 823, isMobile: true, deviceScaleFactor: 1.75 });
    await page.goto(url, { waitUntil: 'networkidle2', timeout: 60000 });

    const css = await page.evaluate(() => {
        const main = document.querySelector('main');
        const hero = main && Array.from(main.children).find((el) => el.querySelector('.hero, .hero-stage') || el.matches('.hero'));
        const excluded = [...document.querySelectorAll('footer')];
        if (main && hero) {
            let el = hero.nextElementSibling;
            while (el) { excluded.push(el); el = el.nextElementSibling; }
        }
        const isIncluded = (el) => !excluded.some((root) => root.contains(el));

        // Pseudo-elements and interaction states don't change which element a rule targets
        const toMatchable = (selector) => selector
            .replace(/::?(before|after|placeholder|selection|marker|backdrop|first-letter|first-line|-webkit-[\w-]+|-moz-[\w-]+|-ms-[\w-]+)/g, '')
            .replace(/:(hover|focus-visible|focus-within|focus|active|visited|target)/g, '')
            .trim() || '*';

        const ruleMatches = (selectorText) => selectorText.split(',').some((selector) => {
            try {
                return Array.from(document.querySelectorAll(toMatchable(selector))).some(isIncluded);
            } catch (e) {
                return false;
            }
        });

        const collect = (rules) => {
            let out = '';
            for (const rule of rules) {
                if (rule instanceof CSSStyleRule) {
                    if (ruleMatches(rule.selectorText)) out += rule.cssText + '\n';
                } else if (rule instanceof CSSMediaRule || rule instanceof CSSSupportsRule) {
                    const inner = collect(rule.cssRules);
                    if (inner) out += `@${rule instanceof CSSMediaRule ? 'media' : 'supports'} ${rule.conditionText} {\n${inner}}\n`;
                } else if (rule instanceof CSSFontFaceRule || rule instanceof CSSKeyframesRule) {
                    out += rule.cssText + '\n';
                }
            }
            return out;
        };

        const bundle = Array.from(document.styleSheets).find((s) => s.href && s.href.includes('/cache/css/bundle-'));
        if (!bundle) throw new Error('Layout CSS bundle not found on the page');
        return `/* critical css for ${location.href} from ${bundle.href.split('/').pop()} */\n` + collect(bundle.cssRules);
    });

    const outFile = path.resolve('public/css/critical', `${key}.css`);
    fs.mkdirSync(path.dirname(outFile), { recursive: true });
    fs.writeFileSync(outFile, css);
    console.log(`Wrote ${outFile} (${(css.length / 1024).toFixed(1)} KiB)`);
} finally {
    await browser.close();
}
