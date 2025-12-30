/**
 * PayPal Commerce Gateway Integration - Source Code
 * This file contains the source code for PayPal Commerce integration with multi-currency
 * Compiled version: /resource/payPalCommerceGateway.js
 */

import { useState, useEffect, useContext, createContext } from 'react';

/**
 * PayPal Commerce Gateway for Multi-Currency
 * Handles PayPal payments with currency conversion
 */
class MultiCurrencyPayPalGateway {
    constructor() {
        this.id = 'paypal-commerce';
        this.settings = null;
    }

    /**
     * Initialize the gateway with settings
     */
    initialize() {
        this.settings = window.giveMultiCurrencySettings || {};
    }

    /**
     * Handle payment creation with currency conversion
     */
    async beforeCreatePayment(paymentData) {
        try {
            // Get selected currency from multi-currency selector
            const selectedCurrency = this.getSelectedCurrency();
            const convertedAmount = this.convertAmount(paymentData.amount, selectedCurrency);
            
            return {
                ...paymentData,
                amount: convertedAmount,
                currency: selectedCurrency,
                originalCurrency: paymentData.currency,
                originalAmount: paymentData.amount
            };
        } catch (error) {
            console.error('Multi-Currency PayPal Error:', error);
            throw new Error('Currency conversion failed');
        }
    }

    /**
     * Get the currently selected currency
     */
    getSelectedCurrency() {
        const currencySelect = document.querySelector('#give-mc-select');
        if (currencySelect) {
            return currencySelect.value;
        }
        return this.settings.defaultCurrency || 'USD';
    }

    /**
     * Convert amount based on exchange rates
     */
    convertAmount(amount, targetCurrency) {
        if (typeof mcfgPayPal !== 'undefined' && mcfgPayPal.rates) {
            const rate = mcfgPayPal.rates[targetCurrency];
            if (rate) {
                return Math.round(amount * rate);
            }
        }
        return amount;
    }

    /**
     * Render PayPal payment fields with currency support
     */
    Fields() {
        const { useFormData } = window.givewp.form.hooks;
        const formData = useFormData();
        
        return React.createElement(
            PayPalFieldsComponent,
            {
                formData: formData,
                gateway: this
            }
        );
    }
}

/**
 * PayPal Fields React Component
 */
const PayPalFieldsComponent = ({ formData, gateway }) => {
    const [selectedCurrency, setSelectedCurrency] = useState('USD');
    const [exchangeRate, setExchangeRate] = useState(1);

    useEffect(() => {
        // Monitor currency changes
        const currencySelect = document.querySelector('#give-mc-select');
        if (currencySelect) {
            const handleCurrencyChange = () => {
                const newCurrency = currencySelect.value;
                setSelectedCurrency(newCurrency);
                
                // Update exchange rate
                if (typeof mcfgPayPal !== 'undefined' && mcfgPayPal.rates) {
                    const rate = mcfgPayPal.rates[newCurrency] || 1;
                    setExchangeRate(rate);
                }
            };

            currencySelect.addEventListener('change', handleCurrencyChange);
            
            // Initial setup
            handleCurrencyChange();

            return () => {
                currencySelect.removeEventListener('change', handleCurrencyChange);
            };
        }
    }, []);

    return React.createElement(
        'div',
        { className: 'mcfg-paypal-fields' },
        React.createElement(
            'div',
            { className: 'mcfg-currency-info' },
            `Currency: ${selectedCurrency} (Rate: ${exchangeRate})`
        ),
        React.createElement(PayPalButtonsWrapper, {
            currency: selectedCurrency,
            exchangeRate: exchangeRate,
            formData: formData
        })
    );
};

/**
 * PayPal Buttons Wrapper Component
 */
const PayPalButtonsWrapper = ({ currency, exchangeRate, formData }) => {
    return React.createElement(
        'div',
        { id: 'mcfg-paypal-buttons-container' },
        // PayPal buttons will be rendered here by the compiled script
    );
};

// Register the gateway
if (window.givewp && window.givewp.gateways) {
    const gateway = new MultiCurrencyPayPalGateway();
    window.givewp.gateways.register(gateway);
}

export default MultiCurrencyPayPalGateway;
