/**
 * Multi Currency Coin Selector - Source Code
 * This file contains the source code for the currency selector functionality
 * Compiled version: /resource/give-multi-currency-coin-selector.js
 */

(function () {
    'use strict';

    /**
     * Read the localized configuration published by give_import_script_method().
     */
    function getConfig() {
        if (typeof window.lknaciMcfgVars === 'undefined' || !window.lknaciMcfgVars) {
            return null;
        }

        var raw = window.lknaciMcfgVars;
        var symbols = raw.moedas;

        if (typeof symbols === 'string') {
            try {
                symbols = JSON.parse(symbols);
            } catch (error) {
                symbols = {};
            }
        }

        return {
            base: raw.moedaPadrao || 'BRL',
            rates: raw.rates || {},
            symbols: symbols || {},
            decimalSeparator: raw.decimalSeparator || ',',
            thousandSeparator: raw.thousandSeparator || '.',
            currencyPosition: raw.currencyPosition || 'before'
        };
    }

    /**
     * Exchange rate of a currency relative to the base currency. The base is always 1.
     */
    function rateFor(currency, config) {
        var rate = parseFloat(config.rates[currency]);

        if (!rate || rate <= 0) {
            return 1;
        }

        return rate;
    }

    /**
     * Convert an amount between two currencies using the base-relative rates.
     */
    function convert(amount, from, to, config) {
        if (from === to || !amount) {
            return amount;
        }

        return (amount / rateFor(from, config)) * rateFor(to, config);
    }

    /**
     * Format an amount using the separators configured in GiveWP.
     *
     * Converted amounts keep up to two decimals so small base values do not
     * collapse to zero; whole values still render without a decimal part.
     */
    function formatAmount(amount, config) {
        var rounded = Math.round(amount * 100) / 100;
        var parts = rounded.toFixed(2).split('.');
        var integerPart = parts[0];
        var decimalPart = parts[1];

        var formattedInteger = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, config.thousandSeparator);

        if (decimalPart === '00') {
            return formattedInteger;
        }

        return formattedInteger + config.decimalSeparator + decimalPart;
    }

    /**
     * Apply the currency symbol respecting the currency position setting.
     */
    function withSymbol(amountText, symbol, config) {
        if (!symbol) {
            return amountText;
        }

        return config.currencyPosition === 'after'
            ? amountText + ' ' + symbol
            : symbol + ' ' + amountText;
    }

    /**
     * Prevent specific keys from being entered in amount fields.
     */
    function lknPreventSpecificKeys(event) {
        var forbiddenKeys = ['.', ','];

        if (forbiddenKeys.indexOf(event.key) !== -1) {
            event.preventDefault();
            return false;
        }
    }

    /**
     * Read the numeric amount held by an element.
     */
    function readAmount(element) {
        var value = element.value !== undefined && element.value !== ''
            ? element.value
            : element.textContent;

        var amount = parseFloat(String(value).replace(/[^\d,.-]/g, '').replace(',', '.'));

        return isNaN(amount) ? 0 : amount;
    }

    /**
     * Amount elements that must follow the selected currency.
     */
    function amountElements(doc) {
        return {
            symbol: doc.querySelectorAll('.give-currency-symbol'),
            amountText: doc.querySelector('#give-amount-text'),
            hiddenAmount: doc.querySelector('#give-amount'),
            levels: doc.querySelectorAll(
                '.give-donation-level-btn, .give-radio-input-level, .give-donation-level'
            )
        };
    }

    /**
     * Store the base currency amount of every element so switching back is lossless.
     */
    function rememberBaseAmounts(doc, config, currency) {
        var elements = amountElements(doc);

        if (elements.amountText && elements.amountText.dataset.lknBaseAmount === undefined) {
            elements.amountText.dataset.lknBaseAmount = String(convert(readAmount(elements.amountText), currency, config.base, config));
        }

        if (elements.hiddenAmount && elements.hiddenAmount.dataset.lknBaseAmount === undefined) {
            elements.hiddenAmount.dataset.lknBaseAmount = String(convert(readAmount(elements.hiddenAmount), currency, config.base, config));
        }

        Array.prototype.forEach.call(elements.levels, function (level) {
            if (level.dataset.lknBaseAmount === undefined) {
                level.dataset.lknBaseAmount = String(convert(readAmount(level), currency, config.base, config));
            }
        });
    }

    /**
     * Recompute every displayed amount for the selected currency.
     */
    function renderAmounts(doc, config, currency) {
        var elements = amountElements(doc);
        var symbol = config.symbols[currency];
        var rate = rateFor(currency, config);

        Array.prototype.forEach.call(elements.symbol, function (node) {
            if (symbol) {
                node.textContent = symbol;
            }
        });

        if (elements.amountText && elements.amountText.dataset.lknBaseAmount !== undefined) {
            var baseTextAmount = parseFloat(elements.amountText.dataset.lknBaseAmount) * rate;

            elements.amountText.textContent = formatAmount(baseTextAmount, config);
        }

        if (elements.hiddenAmount && elements.hiddenAmount.dataset.lknBaseAmount !== undefined) {
            var baseHiddenAmount = parseFloat(elements.hiddenAmount.dataset.lknBaseAmount) * rate;

            elements.hiddenAmount.value = String(Math.round(baseHiddenAmount * 100) / 100);
        }

        Array.prototype.forEach.call(elements.levels, function (level) {
            if (level.dataset.lknBaseAmount === undefined) {
                return;
            }

            var baseLevelAmount = parseFloat(level.dataset.lknBaseAmount) * rate;
            var formattedLevel = formatAmount(baseLevelAmount, config);
            var labelledLevel = withSymbol(formattedLevel, symbol, config);

            if (level.value !== undefined && level.tagName !== 'SPAN') {
                level.value = String(Math.round(baseLevelAmount * 100) / 100);
            }

            if (level.tagName === 'OPTION' || level.tagName === 'BUTTON') {
                level.textContent = labelledLevel;
            } else if (level.nextElementSibling && level.nextElementSibling.tagName === 'LABEL') {
                level.nextElementSibling.textContent = labelledLevel;
            }
        });
    }

    /**
     * Refresh the stored base amount after the donor edits the custom amount.
     */
    function syncBaseFromDom(doc, config, currency) {
        var elements = amountElements(doc);

        if (elements.amountText) {
            elements.amountText.dataset.lknBaseAmount = String(
                convert(readAmount(elements.amountText), currency, config.base, config)
            );
        }

        if (elements.hiddenAmount) {
            elements.hiddenAmount.dataset.lknBaseAmount = String(
                convert(readAmount(elements.hiddenAmount), currency, config.base, config)
            );
        }
    }

    /**
     * Initialize GiveWP multi-currency functionality.
     */
    function initializeGiveWP(doc) {
        doc = doc || document;

        var config = getConfig();
        var selector = doc.querySelector('#give-mc-select');
        var hiddenField = doc.querySelector('#give-mc-currency-selected');

        if (!config || !selector) {
            return;
        }

        var currency = config.base;

        rememberBaseAmounts(doc, config, currency);

        selector.addEventListener('change', function (event) {
            currency = event.target.value;

            if (hiddenField) {
                hiddenField.value = currency;
            }

            renderAmounts(doc, config, currency);
        });

        // When the donor edits the custom amount, rebuild the base amount from the
        // value currently displayed, otherwise switching currency twice would drift.
        ['#give-amount', '#give-amount-text', '.give-amount-top'].forEach(function (selectorText) {
            var field = doc.querySelector(selectorText);

            if (!field) {
                return;
            }

            field.addEventListener('change', function () {
                syncBaseFromDom(doc, config, currency);
            });
        });

        // GiveWP rewrites the amount when a preset level is clicked, so re-render
        // right after it to keep the amount in the selected currency.
        Array.prototype.forEach.call(doc.querySelectorAll('.give-donation-level-btn'), function (button) {
            button.addEventListener('click', function () {
                window.setTimeout(function () {
                    syncBaseFromDom(doc, config, currency);
                    renderAmounts(doc, config, currency);
                }, 0);
            });
        });

        renderAmounts(doc, config, currency);
    }

    /**
     * Keep the embedded form amount usable and rounded.
     */
    function initializeEmbeddedForm() {
        var rootDiv = document.querySelector('.root-data-givewp-embed');

        if (!rootDiv) {
            return;
        }

        var iframeFormBuilder = rootDiv.querySelector('iframe');

        if (!iframeFormBuilder) {
            return;
        }

        var iframeDoc = iframeFormBuilder.contentDocument || iframeFormBuilder.contentWindow.document;

        if (!iframeDoc) {
            return;
        }

        var lknAmountCustom = iframeDoc.getElementById('amount-custom');

        if (!lknAmountCustom) {
            return;
        }

        lknAmountCustom.addEventListener('keydown', lknPreventSpecificKeys);

        lknAmountCustom.addEventListener('blur', function () {
            window.setTimeout(function () {
                var hiddenAmount = iframeDoc.getElementsByName('amount')[0];

                if (!hiddenAmount) {
                    return;
                }

                var value = parseFloat(lknAmountCustom.value);
                var result = isNaN(value) ? 0 : Math.round(value);

                if (result && result > 0) {
                    hiddenAmount.value = result;
                }
            }, 1000);
        });
    }

    window.addEventListener('load', function () {
        initializeEmbeddedForm();

        var iframe = document.querySelector('#iFrameResizer0');

        if (iframe) {
            initializeGiveWP(iframe.contentDocument || iframe.contentWindow.document);
        } else {
            initializeGiveWP(document);
        }
    });

    // Exposed for the test suite.
    window.lknMultiCurrency = {
        getConfig: getConfig,
        rateFor: rateFor,
        convert: convert,
        formatAmount: formatAmount,
        withSymbol: withSymbol,
        initializeGiveWP: initializeGiveWP
    };
})();
