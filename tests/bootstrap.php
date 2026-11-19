<?php

/**
 * PHPUnit bootstrap.
 *
 * The plugin classes bail out when ABSPATH is undefined, so it is declared here
 * to allow the pure units (rate math, formatting helpers) to load without a
 * full WordPress install.
 */

if (! defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Includes/GiveMultiCurrencyRates.php';
