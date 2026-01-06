/**
 * Multi Currency Coin Selector - Source Code
 * This file contains the source code for the currency selector functionality
 * Compiled version: /resource/give-multi-currency-coin-selector.js
 */

window.addEventListener("load", function () {
    // Get the div element using its class for GiveWP embed
    const rootDiv = document.querySelector('.root-data-givewp-embed');

    if (rootDiv) {
        // Find the iframe inside this div
        const iframeFormBuilder = rootDiv.querySelector('iframe');

        if (iframeFormBuilder) {
            const iframeDoc = iframeFormBuilder.contentDocument || iframeFormBuilder.contentWindow.document

            if (iframeDoc) {
                const lknAmountCustom = iframeDoc.getElementById('amount-custom')

                if (lknAmountCustom) {
                    lknAmountCustom.addEventListener('keydown', lknPreventSpecificKeys)

                    lknAmountCustom.addEventListener('blur', () => {
                        setTimeout(() => {
                            const hiddenAmount = iframeDoc.getElementsByName('amount')[0]

                            if (hiddenAmount) {
                                const result = lknFormatAndRoundNumber(lknAmountCustom.value)

                                if (result && result > 0) {
                                    hiddenAmount.value = result
                                }
                            }
                        }, 1000)
                    })
                }
            }
        }
    }

    // Initialize for iFrame forms
    let iframe = document.querySelector("#iFrameResizer0");
    if (iframe) {
        let iframeDocument = iframe.contentDocument || iframe.contentWindow.document;
        initializeGiveWP(iframeDocument);
    } else {
        initializeGiveWP();
    }
});

/**
 * Prevent specific keys from being entered in amount fields
 */
function lknPreventSpecificKeys(event) {
    const forbiddenKeys = ['.', ','];

    if (forbiddenKeys.includes(event.key)) {
        event.preventDefault();
        return false;
    }
}

/**
 * Format and round number for amount fields
 */
function lknFormatAndRoundNumber(value) {
    const numValue = parseFloat(value);
    if (isNaN(numValue)) {
        return 0;
    }
    return Math.round(numValue);
}

/**
 * Initialize GiveWP multi-currency functionality
 */
function initializeGiveWP(doc = document) {
    // Multi-currency initialization logic
    const selector = doc.querySelector('#give-mc-select');
    if (selector) {
        selector.addEventListener('change', handleCurrencyChange);
        
        // Initialize with default currency
        if (typeof lknaciMcfgVars !== 'undefined') {
            updateCurrencyDisplay(lknaciMcfgVars.moedaPadrao);
        }
    }
}

/**
 * Handle currency selection change
 */
function handleCurrencyChange(event) {
    const selectedCurrency = event.target.value;
    const currencySymbol = event.target.options[event.target.selectedIndex].getAttribute('simbol');
    
    updateCurrencyDisplay(selectedCurrency, currencySymbol);
    
    // Update hidden field
    const hiddenField = document.querySelector('#give-mc-currency-selected');
    if (hiddenField) {
        hiddenField.value = selectedCurrency;
    }
}

/**
 * Update currency display throughout the form
 */
function updateCurrencyDisplay(currency, symbol) {
    if (typeof lknaciMcfgVars === 'undefined') {
        return;
    }
    
    // Update amount calculations based on exchange rates
    const rates = lknaciMcfgVars.rates;
    if (rates && rates[currency]) {
        const rate = rates[currency];
        // Update amount fields with converted values
        updateAmountFields(rate, symbol);
    }
}

/**
 * Update amount fields with converted values
 */
function updateAmountFields(rate, symbol) {
    const amountFields = document.querySelectorAll('.give-donation-amount input[type="text"], .give-donation-amount input[type="number"]');
    
    amountFields.forEach(field => {
        if (field.value && field.value !== '') {
            const originalAmount = parseFloat(field.value);
            if (!isNaN(originalAmount)) {
                const convertedAmount = Math.round(originalAmount * rate);
                field.setAttribute('data-original-amount', originalAmount);
                field.value = convertedAmount;
            }
        }
    });
}
