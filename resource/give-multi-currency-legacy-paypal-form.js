window.addEventListener("load", function () {
    const paypalGateway = this.document.getElementById('give-gateway-option-paypal-commerce')
    if (paypalGateway) {
        observer = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType === 1) { // Garante que o node é um elemento
                        let paypalIframe = document.querySelector?.('iframe[title="PayPal"]');

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
                                            const paymentGatewayCreateOrder = options.body.get('give_payment_mode');
                                            let currencyConverted = 0;

                                            if (options.body.get('amount') || options.body.get('give-amount')) {
                                                const currency = document.querySelector?.('input[name="currency"]');
                                                const amount = document.querySelector?.('input[name="amount"]');

                                                const amountValue = amount.value
                                                const currencyValue = varsPhp.rates[currency.value] || 1;

                                                currencyConverted = (amountValue / currencyValue).toFixed(0);
                                            }

                                            if (url.includes('givewp-route=validate') && paymentGatewayValidade === 'paypal-commerce') {
                                                options.body.set('amount', currencyConverted);
                                                options.body.set('currency', varsPhp.moedaPadrao);
                                            } else if (url.includes('give_paypal_commerce_create_order') && paymentGatewayCreateOrder === 'paypal-commerce') {
                                                options.body.set('give-amount', currencyConverted);
                                                options.body.set('give-cs-form-currency', varsPhp.moedaPadrao);
                                            } else if (url.includes('givewp-route=donate') && paymentGatewayValidade === 'paypal-commerce') {
                                                options.body.set('amount', currencyConverted);
                                                options.body.set('currency', varsPhp.moedaPadrao);
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
                            }, 200);
                        }
                    }
                });
            });
        });

        // Inicia o observer no body
        observer.observe(document.body, { childList: true, subtree: true });
    }
})