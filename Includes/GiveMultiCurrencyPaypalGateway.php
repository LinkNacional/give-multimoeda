<?php

namespace Lkn\GiveMultimoedas\Includes;

use Give\Helpers\Language;
use Give\PaymentGateways\Gateways\PayPalCommerce\PayPalCommerceGateway;

class GiveMultiCurrencyPaypalGateway extends PayPalCommerceGateway
{
    public function enqueueScript(int $formId)
    {
        $defaultCurrency = give_get_option('currency');
        wp_enqueue_script('paypal-commerce', GIVE_MULTI_CURRENCY_URL . 'resource/payPalCommerceGateway.js', array('react', 'wp-components', 'wp-i18n'), GIVE_MULTI_CURRENCY_VERSION, true);
        wp_localize_script('paypal-commerce', 'mcfgPayPal', [
            'currency' => $defaultCurrency,
        ]);
        Language::setScriptTranslations('paypal');
    }
}
