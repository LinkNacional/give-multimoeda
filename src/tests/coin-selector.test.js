/**
 * Tests for the compiled multi-currency coin selector.
 *
 * Run with: npm test
 */

const test = require('node:test');
const assert = require('node:assert');
const fs = require('node:fs');
const path = require('node:path');
const {JSDOM} = require('jsdom');

const BUNDLE = path.resolve(__dirname, '../../resource/give-multi-currency-coin-selector.js');
const SOURCE = fs.readFileSync(BUNDLE, 'utf8');

const MARKUP = `<!doctype html><html><body>
  <input type="hidden" id="give-mc-currency-selected" name="give-mc-selected-currency">
  <select id="give-mc-select">
    <option value="BRL" simbol="R$">Brazilian Real</option>
    <option value="USD" simbol="$">US Dollars</option>
    <option value="EUR" simbol="€">Euros</option>
  </select>
  <div class="set-price give-donation-amount">
    <span class="give-currency-symbol give-currency-position-before">R$</span>
    <span id="give-amount-text" class="give-text-input give-amount-top">100</span>
  </div>
  <input type="hidden" id="give-amount" name="give-amount" value="100">
  <ul class="give-donation-levels-wrap">
    <li><button type="button" class="give-donation-level-btn" data-price-id="0" value="100">R$ 100</button></li>
    <li><button type="button" class="give-donation-level-btn" data-price-id="1" value="250">R$ 250</button></li>
  </ul>
</body></html>`;

function boot(overrides = {}) {
    const dom = new JSDOM(MARKUP, {runScripts: 'outside-only', url: 'http://localhost/'});
    const {window} = dom;

    window.lknaciMcfgVars = {
        moedas: JSON.stringify({BRL: 'R$', USD: '$', EUR: '€'}),
        moedaPadrao: 'BRL',
        rates: {BRL: 1, USD: 0.1721, EUR: 0.1568},
        decimalSeparator: ',',
        thousandSeparator: '.',
        currencyPosition: 'before',
        ...overrides
    };

    window.eval(SOURCE);

    return {window, doc: window.document};
}

test('initializeGiveWP wires the select and renders base amounts', () => {
    const {window, doc} = boot();

    window.lknMultiCurrency.initializeGiveWP(doc);

    assert.strictEqual(doc.querySelector('#give-amount-text').textContent, '100');
    assert.strictEqual(doc.querySelector('.give-currency-symbol').textContent, 'R$');
});

test('changing the select converts the custom amount and the symbol', () => {
    const {window, doc} = boot();
    window.lknMultiCurrency.initializeGiveWP(doc);

    const select = doc.querySelector('#give-mc-select');
    select.value = 'USD';
    select.dispatchEvent(new window.Event('change', {bubbles: true}));

    assert.strictEqual(doc.querySelector('#give-mc-currency-selected').value, 'USD');
    assert.strictEqual(doc.querySelector('.give-currency-symbol').textContent, '$');
    // 100 BRL * 0.1721 = 17.21, cents preserved
    assert.strictEqual(doc.querySelector('#give-amount-text').textContent, '17,21');
    assert.strictEqual(doc.querySelector('#give-amount').value, '17.21');
});

test('switching back to the base currency restores the original amount', () => {
    const {window, doc} = boot();
    window.lknMultiCurrency.initializeGiveWP(doc);

    const select = doc.querySelector('#give-mc-select');
    select.value = 'USD';
    select.dispatchEvent(new window.Event('change', {bubbles: true}));
    select.value = 'BRL';
    select.dispatchEvent(new window.Event('change', {bubbles: true}));

    assert.strictEqual(doc.querySelector('#give-amount-text').textContent, '100');
    assert.strictEqual(doc.querySelector('#give-amount').value, '100');
});

test('donation levels are converted and keep their multiplier', () => {
    const {window, doc} = boot();
    window.lknMultiCurrency.initializeGiveWP(doc);

    const select = doc.querySelector('#give-mc-select');
    select.value = 'EUR';
    select.dispatchEvent(new window.Event('change', {bubbles: true}));

    const levels = doc.querySelectorAll('.give-donation-level-btn');
    assert.strictEqual(levels[0].value, '15.68'); // 100 * 0.1568
    assert.strictEqual(levels[1].value, '39.2'); // 250 * 0.1568
});

test('a custom amount typed by the donor becomes the new base amount', () => {
    const {window, doc} = boot();
    window.lknMultiCurrency.initializeGiveWP(doc);

    const hidden = doc.querySelector('#give-amount');
    hidden.value = '250';
    hidden.dispatchEvent(new window.Event('change', {bubbles: true}));

    const select = doc.querySelector('#give-mc-select');
    select.value = 'USD';
    select.dispatchEvent(new window.Event('change', {bubbles: true}));

    assert.strictEqual(doc.querySelector('#give-amount').value, '43.03'); // 250 * 0.1721
});

test('formatAmount uses the configured thousand separator', () => {
    const {window} = boot({thousandSeparator: '.'});

    assert.strictEqual(window.lknMultiCurrency.formatAmount(1234, window.lknMultiCurrency.getConfig()), '1.234');
});

test('withSymbol respects the currency position', () => {
    const {window} = boot({currencyPosition: 'after'});
    const config = window.lknMultiCurrency.getConfig();

    assert.strictEqual(window.lknMultiCurrency.withSymbol('17', '$', config), '17 $');
});

test('convert handles a payload with a missing currency as base relative', () => {
    const {window} = boot();

    // Unknown currency falls back to rate 1 (base relative).
    assert.strictEqual(window.lknMultiCurrency.convert(100, 'CHF', 'BRL', window.lknMultiCurrency.getConfig()), 100);
});

test('rates exposed by the PHP payload keep the base at 1', () => {
    const {window} = boot();

    assert.strictEqual(window.lknMultiCurrency.rateFor('BRL', window.lknMultiCurrency.getConfig()), 1);
    assert.strictEqual(window.lknMultiCurrency.rateFor('USD', window.lknMultiCurrency.getConfig()), 0.1721);
});
