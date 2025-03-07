window.addEventListener("load", function () {

    // Get the div element using its class
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

    let iframe = document.querySelector("#iFrameResizer0");
    if (iframe) {
        let iframeDocument = iframe.contentDocument || iframe.contentWindow.document;
        initializeGiveWP(iframeDocument);
    } else {
        initializeGiveWP();
    }
});

function lknPreventSpecificKeys(event) {
    // Check for the keys that should be prevented
    const forbiddenKeys = ['.', ','];

    // If the pressed key matches any of the forbidden keys
    if (forbiddenKeys.includes(event.key)) {
        event.preventDefault(); // Prevent the default action
    }
}

function lknFormatAndRoundNumber(input) {
    // Remove todas as letras, deixando apenas números e vírgula
    let onlyNumbersAndComma = input.replace(/[^\d,]/g, '');

    // Substituir vírgula por ponto
    let numberWithDot = onlyNumbersAndComma.replace(',', '.');

    // Converter para número e arredondar para inteiro
    let roundedNumber = Math.round(parseFloat(numberWithDot));

    return roundedNumber;
}

function initializeGiveWP(iframeDocument = null) {
    if (!iframeDocument) {
        iframeDocument = document;
    }
    let select = iframeDocument.querySelector("#give-mc-select");
    let inputSelect = iframeDocument.querySelector("#give-mc-currency-selected");
    let give_amount = iframeDocument.querySelector("#give-amount");
    let give_purchase_buttons = iframeDocument.querySelectorAll(".give-donation-level-btn");

    const rootDiv = document.querySelector('.root-data-givewp-embed');
    if (rootDiv) {
        const iframeFormBuilder = rootDiv.querySelector('iframe');

        if (iframeFormBuilder) {
            const iframeDoc = iframeFormBuilder.contentDocument || iframeFormBuilder.contentWindow.document
            const paypalGateway = iframeDoc.querySelector('.givewp-fields-gateways__gateway__fields div .paypal-buttons')

            if (paypalGateway) {
                handlePaypalGateway(iframeDoc)
            }
        }
    }

    if (select) {
        handleSelectChange(select, inputSelect, iframeDocument);
        addEventListeners(select, inputSelect, give_amount, give_purchase_buttons, iframeDocument);

        const paypalGateway = this.document.getElementById('give-gateway-option-paypal-commerce')
        if (paypalGateway) {
            handlePaypalGateway(document)
        }
    }
}

function handlePaypalGateway(iframeDoc) {
    observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            mutation.addedNodes.forEach(node => {
                if (node.nodeType === 1) { // Garante que o node é um elemento
                    let paypalIframe = iframeDoc.querySelector?.('iframe[title="PayPal"]');

                    if (paypalIframe) {
                        let retries = 0;
                        const maxRetries = 20;

                        const checkIframeLoaded = setInterval(() => {
                            if (paypalIframe.contentWindow) {
                                clearInterval(checkIframeLoaded);

                                if (!paypalIframe.dataset.fetchModified) {
                                    paypalIframe.dataset.fetchModified = "true";

                                    const originalFetch = paypalIframe.contentWindow.parent.fetch;

                                    paypalIframe.contentWindow.parent.fetch = function (url, options = {}) {
                                        const paymentGatewayValidade = options.body.get('gatewayId');
                                        const paymentGatewayCreateOrder = options.body.get('give_payment_mode') || options.body.get('payment-mode');
                                        let currencyConverted = 0;
                                        let currencyValue = 1

                                        if (options.body.get('amount') || options.body.get('give-amount')) {
                                            const currency = iframeDoc.querySelector?.('input[name="currency"]') || iframeDoc.getElementById('give-mc-select')

                                            const amount = iframeDoc.querySelector('input[name="amount"]') || iframeDoc.querySelector('.give-final-total-amount');

                                            const amountValue = amount.value || amount.getAttribute('data-total')

                                            // Obtém o valor selecionado
                                            currencyValue = varsPhp.rates[currency.value];

                                            currencyConverted = (amountValue / currencyValue).toFixed(0);

                                            // Force update
                                            console.log()
                                        }

                                        if (url.includes('givewp-route=validate') && paymentGatewayValidade === 'paypal-commerce') {
                                            options.body.set('amount', currencyConverted);
                                            options.body.set('currency', varsPhp.moedaPadrao);
                                        } else if (url.includes('give_paypal_commerce_create_order') && paymentGatewayCreateOrder === 'paypal-commerce') {
                                            options.body.set('give-amount', currencyConverted);
                                            options.body.set('give-cs-form-currency', varsPhp.moedaPadrao);
                                            options.body.set('give-mc-selected-currency', varsPhp.moedaPadrao);
                                        } else if (url.includes('give_paypal_commerce_approve_order') && paymentGatewayCreateOrder === 'paypal-commerce') {
                                            options.body.set('give-amount', currencyConverted);
                                            options.body.set('give-cs-form-currency', varsPhp.moedaPadrao);
                                            options.body.set('give-mc-selected-currency', varsPhp.moedaPadrao);
                                        } else if (url.includes('givewp-route=donate') && paymentGatewayValidade === 'paypal-commerce') {
                                            options.body.set('amount', currencyConverted);
                                            options.body.set('currency', varsPhp.moedaPadrao);
                                        } else if (url.includes('wp-admin/admin-ajax.php') && options.body.get('give-amount')) {
                                            const inputtest = iframeDoc.querySelector('input[name="give-price-id"]')
                                            inputtest.value = "custom"
                                            const legacyAmount = iframeDoc.getElementById('give-amount')
                                            if (legacyAmount) {
                                                legacyAmount.value = currencyConverted
                                                legacyAmount.dispatchEvent(new Event('click', { bubbles: true }));
                                            }
                                        }

                                        return originalFetch(url, options);
                                    };
                                }
                            } else {
                                retries++;
                                if (retries >= maxRetries) {
                                    clearInterval(checkIframeLoaded);
                                }
                            }
                        }, 500);
                    }
                }
            });
        });
    });

    // Inicia o observer no body
    observer.observe(iframeDoc.body, { childList: true, subtree: true });
}

function handleSelectChange(select, inputSelect, iframeDocument) {
    let selectedOption = select.options[select.selectedIndex];
    let simbol = selectedOption.getAttribute("simbol");

    if (simbol) {
        changeCurrencyCoin(iframeDocument, simbol);
    }

    if (inputSelect) {
        inputSelect.value = selectedOption.value;
    }
}

function addEventListeners(select, inputSelect, give_amount, give_purchase_buttons, iframeDocument) {
    select.addEventListener("change", function () {
        updateCurrency(select, inputSelect, iframeDocument);
    });

    if (give_amount) {
        give_amount.addEventListener('change', function () {
            updateCurrency(select, inputSelect, iframeDocument);
        });
    }

    if (give_purchase_buttons) {
        give_purchase_buttons.forEach(button => {
            button.addEventListener("click", function () {
                updateCurrency(select, inputSelect, iframeDocument);
            });
        });
    }
}

function updateCurrency(select, inputSelect, iframeDocument) {
    let selectedOption = select.options[select.selectedIndex];
    let simbol = selectedOption.getAttribute("simbol");

    if (simbol) {
        changeCurrencyCoin(iframeDocument, simbol);
    }

    if (inputSelect) {
        inputSelect.value = selectedOption.value;
    }
}

function changeCurrencyCoin(iframe, value) {
    updateButtonSymbols(iframe, value);
    updateCurrencySymbol(iframe, value);
    updateAmounts(iframe, value);
}

function updateButtonSymbols(iframe, value) {
    let buttons = iframe.querySelectorAll(".give-donation-level-btn .currency");
    if (buttons.length > 0) {
        buttons.forEach(button => {
            button.textContent = value;
        });
    } else {
        buttons = iframe.querySelectorAll(".give-donation-level-btn:not([data-price-id='custom'])");
        buttons.forEach(button => {
            const price = button.getAttribute("value");
            button.innerHTML = `${value}${price}`;
        });
    }
}

function updateCurrencySymbol(iframe, value) {
    let currencySymbol = iframe.querySelector(".give-currency-symbol");
    if (currencySymbol) {
        currencySymbol.innerText = value;
    }

    let inputAmount = iframe.querySelector("#give-amount");
    let finalTotalAmount = iframe.querySelector(".give-final-total-amount");
    let give_purchase_buttons = iframe.querySelectorAll(".give-donation-level-btn");
    let paymentModeElements = iframe.querySelectorAll('[name="payment-mode"]');
    if (paymentModeElements) {
        paymentModeElements.forEach(element => {
            element.addEventListener("change", function () {
                setTimeout(() => {
                    finalTotalAmount = iframe.querySelector(".give-final-total-amount");
                    amountCell = iframe.querySelector('td[data-tag="amount"]');
                    totalCell = iframe.querySelector('th[data-tag="total"]');
                    if (amountCell && totalCell) {
                        updateTotalSibol(amountCell, value);
                        updateTotalSibol(totalCell, value);
                    }
                    updateTotalSibol(finalTotalAmount, value);
                }, 1000);
            });
        });
    }

    if (inputAmount) {
        inputAmount.onchange = () => {
            if (finalTotalAmount) {
                setTimeout(() => {
                    updateTotalSibol(finalTotalAmount, value);
                }, 1);
            }
        }
    }

    give_purchase_buttons.forEach(button => {
        button.addEventListener("click", function () {
            setTimeout(() => {
                updateTotalSibol(finalTotalAmount, value);
            }, 1);
        });
    });

    updateTotalSibol(finalTotalAmount, value);
}

function updateTotalSibol(finalTotalAmount, value) {
    if (finalTotalAmount) {
        let amount = finalTotalAmount.innerText.trim();
        let newAmount = amount.replace(/^[^\d]+/, value);
        finalTotalAmount.innerText = newAmount;
    }
}

function updateAmounts(iframe, value) {
    let inputAmount = iframe.querySelector("#give-amount");
    if (inputAmount) {
        let amount = inputAmount.value;
        let amountCell = iframe.querySelector('td[data-tag="amount"]');
        let totalCell = iframe.querySelector('th[data-tag="total"]');

        if (amountCell && totalCell) {
            setTimeout(() => {
                amountCell.innerText = value + amount;
                totalCell.innerHTML = value + amount;
            }, 3000);
        }
    }
}