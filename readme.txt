=== Give Multi Currency ===
Contributors: linknacional, MarcosAlexandre
Donate link: https://www.linknacional.com.br/wordpress/plugins/
Tags: givewp, donations, multi-currency, currency converter, international payments
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 3.1.4
Requires PHP: 7.4
Requires Plugins: give
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.txt

Transform your GiveWP donation forms with seamless multi-currency support and real-time exchange rates.

== Description ==

**Give Multi Currency** is the ultimate solution for enabling international donations in your GiveWP-powered fundraising campaigns. This powerful plugin automatically converts foreign currencies to Brazilian Real (BRL) for payment processing while displaying donation amounts in the donor's preferred currency, creating a seamless and familiar donation experience for supporters worldwide.

### Why Choose Give Multi Currency?

* **Real-Time Exchange Rates**: Automatic currency conversion using live exchange rates from reliable APIs
* **Multiple Currency Support**: Accept donations in USD, EUR, JPY, GBP, SAR, MXN, CHF, and BRL
* **GiveWP 3.0+ Compatible**: Full support for modern GiveWP forms and legacy templates
* **PayPal Integration**: Special currency conversion handling for PayPal Commerce payments
* **Fallback Protection**: Multiple API sources ensure currency rates are always available
* **Easy Configuration**: Simple form-by-form or global currency settings
* **Professional Interface**: Clean, intuitive currency selector for donors

### Key Features at a Glance

* **8 Supported Currencies**: Brazilian Real, US Dollar, Euro, Japanese Yen, British Pound, Saudi Riyal, Mexican Peso, Swiss Franc
* **Dynamic Currency Selector**: Interactive dropdown for donors to choose their preferred currency
* **Real-Time Conversion**: Live exchange rates with automatic fallback systems
* **Form-Level Control**: Configure different currencies per donation form
* **Global Settings**: Set default currencies across all forms
* **PayPal Compatibility**: Seamless integration with PayPal Commerce Gateway
* **Legacy Support**: Works with both modern and legacy GiveWP form templates
* **Developer Friendly**: Hooks and filters for customization
* **Multilingual Ready**: Full internationalization support with English, Portuguese, and Spanish translations

### How It Works

1. **Currency Selection**: Donors select their preferred currency from an elegant dropdown
2. **Real-Time Conversion**: Plugin fetches current exchange rates and displays amounts
3. **Payment Processing**: Converts foreign currency to BRL for Brazilian payment processors
4. **Seamless Experience**: Donors see familiar currency while payments process correctly

### Perfect For

* **International Nonprofits**: Accept donations from supporters worldwide
* **Brazilian Organizations**: Process international donations through local payment systems
* **Fundraising Campaigns**: Expand reach to global donor base
* **Multi-Regional Causes**: Serve diverse communities with localized currency options

### Technical Highlights

* **API Integration**: Multiple exchange rate sources for reliability
* **Smart Fallback**: Offline rates ensure continuous operation
* **Performance Optimized**: Efficient currency switching and calculation
* **Security First**: Secure API calls and data handling
* **WordPress Standards**: Follows all WordPress coding and security standards

**Note**: This plugin requires GiveWP and is optimized for Brazilian Real (BRL) as the base processing currency.

== Installation ==

### 1. Using WordPress Admin Dashboard (Recommended)
1. Navigate to **Plugins → Add New**
2. Click **Upload Plugin** and select the plugin ZIP file
3. Click **Install Now** and then **Activate**
4. Go to **Donations → Settings → General → Currency Settings** to configure

### 2. Manual Installation via FTP
1. Extract the plugin ZIP file
2. Upload the extracted folder to `wp-content/plugins/`
3. Activate the plugin in **Plugins** dashboard

### 3. WP-CLI Installation
```bash
wp plugin activate give-multi-currency
```

== Configuration ==

### Initial Setup
1. Navigate to **Donations → Settings → General → Currency Settings**
2. Enable **Multi Currency** option
3. Set your **Default Currency** (recommended: BRL)
4. Select **Enabled Currencies** for your donation forms
5. Save settings

### Form-Level Configuration
1. Edit any GiveWP donation form
2. Go to **Currency Options** tab
3. Choose between **Global Options** or form-specific settings
4. Configure **Default Currency** and **Enabled Currencies** for this form
5. Update form

### Requirements
* **GiveWP Plugin**: Version 2.19.2 or higher
* **Currency Setting**: Base currency must be Brazilian Real (BRL)
* **Decimal Places**: Set to 0 in GiveWP currency settings for optimal performance

== Frequently Asked Questions ==

= What currencies are supported? =
The plugin supports 8 major currencies: Brazilian Real (BRL), US Dollar (USD), Euro (EUR), Japanese Yen (JPY), British Pound (GBP), Saudi Riyal (SAR), Mexican Peso (MXN), and Swiss Franc (CHF).

= Does it work with all payment gateways? =
The plugin works with most payment gateways, with special optimization for PayPal Commerce. It converts currencies for processing while maintaining the donor experience.

= Can I set different currencies for different forms? =
Yes! You can configure currencies globally or set specific currencies for individual donation forms.

= What happens if exchange rate APIs are unavailable? =
The plugin includes multiple fallback systems, including offline rates, to ensure continuous operation even if primary APIs are temporarily unavailable.

= Is the plugin compatible with GiveWP 3.0? =
Absolutely! The plugin fully supports both GiveWP 3.0+ modern forms and legacy templates.

= Can I customize the currency selector appearance? =
Yes, the plugin includes CSS classes and hooks for customization. Advanced users can modify the appearance through themes or custom CSS.

= Does it support recurring donations? =
Yes, the plugin works seamlessly with GiveWP's recurring donation features.

= What about transaction fees and conversion rates? =
The plugin displays current market exchange rates. Payment processor fees are handled according to your gateway's standard policies.

= Is technical support available? =
Yes! Visit our [support page](https://www.linknacional.com.br/suporte/) or create a ticket for assistance.

== Screenshots ==

1. **Currency selector on donation form** - Clean dropdown interface for donors
2. **Global settings page** - Configure default currencies and global options
3. **Form-specific settings** - Per-form currency configuration options
4. **Admin currency options** - Complete currency management interface
5. **PayPal integration** - Seamless PayPal Commerce compatibility
6. **Multi-language support** - Interface in multiple languages

== Changelog ==
= 3.1.4 - 2025/11/10 =
* Remove plugin updater.

= 3.1.3 - 2025/06/27 =
* Added fallback routes for API error handling
* Improved reliability with multiple exchange rate sources
* Enhanced error handling and recovery

= 3.1.2 - 2025/05/02 =
* Fixed action hook implementation
* Improved plugin stability

= 3.1.1 - 2025/04/23 =
* Updated PayPal script integration
* Enhanced PayPal Commerce compatibility

= 3.1.0 - 2025/03/12 =
* Added currency conversion during PayPal payment processing
* Improved PayPal Commerce Gateway integration
* Enhanced payment flow for international transactions

= 3.0.3 - 2024/11/29 =
* Added Swiss Franc (CHF) currency support
* Expanded currency options for European donors

= 3.0.2 - 2024/09/26 =
* Added decimal value handling improvements
* Enhanced calculation accuracy

= 3.0.1 - 2024/08/27 =
* Visual improvements in plugin display
* UI/UX enhancements for better user experience

= 3.0.0 - 2024/08/16 =
* Added support for GiveWP 3.0.0 forms
* Major code refactoring and optimization
* Bug fixes and performance improvements
* Cleanup of legacy code

= 2.7.0 - 2024/06/13 =
* Added Mexican Peso (MXN) support
* Added notifications for inactive Link Nacional plugins
* Enhanced plugin ecosystem integration

= 2.6.0 - 2023/12/23 =
* Added Saudi Riyal (SAR) support
* Updated exchange rate API endpoints
* Added comprehensive changelog system

[View complete changelog](https://github.com/LinkNacional/give-multimoeda/blob/main/CHANGELOG.md)

== Upgrade Notice ==
= 3.1.4 =
* Remove plugin updater.

= 3.1.3 =
Important update with improved API reliability and fallback systems. Recommended for all users.

= 3.1.0 =
Major PayPal integration improvements. Essential for sites using PayPal Commerce Gateway.

= 3.0.0 =
Major update with GiveWP 3.0 support. Please test in staging environment before updating production sites.

== Support ==

For technical support, feature requests, or bug reports:

* **Support Portal**: [Link Nacional Support](https://www.linknacional.com.br/suporte/)
* **Documentation**: [Plugin Documentation](https://www.linknacional.com.br/wordpress/givewp/multimoeda/)
* **GitHub**: Report issues on our [GitHub repository](https://github.com/LinkNacional/give-multimoeda)

**Professional WordPress Development**: Need custom modifications or have a special project? [Contact our development team](https://www.linknacional.com.br/wordpress/)

== Recommended Plugins ==

* **[GiveWP](https://wordpress.org/plugins/give/)** - The #1 donation plugin for WordPress (Required)
* **[Give - Recurring Donations](https://givewp.com/addons/recurring-donations/)** - Accept recurring donations
* **[Give - Fee Recovery](https://givewp.com/addons/fee-recovery/)** - Let donors cover transaction fees
* **[Give - Form Field Manager](https://givewp.com/addons/form-field-manager/)** - Customize donation forms

---

**Transform your donation forms into a global fundraising platform with Give Multi Currency today!**
