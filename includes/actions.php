<?php

include_once __DIR__ . '/exchange-rates.php';

/**
 * Give - Multi Moeda Frontend Actions
 *
 * @since 2.5.0
 *
 * @package    Give
 * @copyright  Copyright (c) 2021, Link Nacional
 * @license    https://opensource.org/licenses/gpl-license GNU Public License
 */

// Exit, if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/////// HELPERS

/**
 * This function centralizes the data in one spot for ease mannagment
 *
 * @return array
 */
function give_multi_currency_get_configs() {
	$configs = [];

	$configs['mcEnabled'] = give_multi_currency_get_enabled();
	$configs['mainCurrency'] = give_multi_currency_get_default_currency();
	$configs['activeCurrency'] = give_multi_currency_get_active_currency();
	$configs['defaultCoin'] = give_multi_currency_get_default_coin();

	return $configs;
}

/**
 * Checks if the 'multi currency' is enabled
 *
 * @return string enabled | disabled
 *
 */
function give_multi_currency_get_enabled() {
	$enabled = give_get_option('multi_currency_enabled_setting_field');

	return $enabled;
}

/**
 * Gets the default currency from give
 *
 * @return string enabled | disabled
 *
 */
function give_multi_currency_get_default_currency() {
	$mainCurrency = give_get_option('currency');

	return $mainCurrency;
}

//função para obter moeda padrão definida no admin
function give_multi_currency_get_default_coin() {
	$defaultCoin = give_get_option('multi_currency_default_currency');

	return $defaultCoin;
}

/**
 * Checks the active currency for the plugin
 *
 * @return string|array
 */
function give_multi_currency_get_active_currency() {
	$currency = give_get_option('multi_currency_active_currency');

	// verifica se existe
	if (!empty($currency)) {
		// deixar tudo maíusculo
		for ($c = 0; $c < count($currency); $c++) {
			$currency[$c] = strtoupper($currency[$c]);
		}
	} else {
		return false;
	}

	return $currency;
}

/**
* Define and replace currency on donation.
 *
 * @param string $currency Current currency.
 *
 * @return string
 */
function give_change_multi_currency($currency) {
	// verifica se uma moeda estrangeira foi selecionada e se o gateway não é o paypal donations
	if (!empty($_POST['give-mc-selected-currency']) && $_POST['payment-mode'] !== 'paypal-commerce') {
		$currency = $_POST['give-mc-selected-currency'];
	}

	return $currency;
}
add_filter('give_currency', 'give_change_multi_currency');

/**
 * Fix the thousand separator
 *
 * @param string $separator The thousand separator
 *
 * @return string
 *
 */
function give_multi_currency_thousand_separator($separator) {
	$separator = '.';

	return $separator;
}

add_filter('give_get_price_thousand_separator', 'give_multi_currency_thousand_separator');

/**
 * Fix the decimal separator
 *
 * @param string $separator The decimal separator
 *
 * @return string
 *
 */
function give_multi_currency_decimal_separator($separator) {
	$separator = ',';

	return $separator;
}

add_filter('give_get_price_decimal_separator', 'give_multi_currency_decimal_separator');

/**
 * Fix the decimal count
 *
 * @param string $separator The decimal count
 *
 * @return string
 *
 */
function give_multi_currency_decimal_count($count) {
	$count = 0;

	return $count;
}

add_filter('give_sanitize_amount_decimals', 'give_multi_currency_decimal_count');

/**
 * Builds the Multi Currency Front-end
 *
 * @param int $form_id
 *
 * @param array $args
 *
 * @return bool|void
 *
 */
function give_multi_currency_selector($form_id, $args) {
	$id_prefix = !empty($args['id_prefix']) ? $args['id_prefix'] : '';
	$configs = give_multi_currency_get_configs();
	$pluginEnabled = $configs['mcEnabled'];
	$mainCurrency = $configs['mainCurrency'];
	$mainCurrencyName = give_get_currency_name($mainCurrency);
	$activeCurrencyNames = [];
	$activeCurrency = $configs['activeCurrency'];
	$hasValidGateway = 'false';
	$activeSymbolArr = give_multi_currency_get_symbols($activeCurrency);
	$defaultCoin = $configs['defaultCoin'];
	$html = null;

	$statusGlobal = get_post_meta($form_id, 'lkn_multi_currency_fields_status', true);
	if ($statusGlobal === 'disabled') {
		$defaultCoin = get_post_meta($form_id, 'lkn_multi_currency_fields_default_currency', true);
		$activeCurrency = get_post_meta($form_id, 'lkn_multi_currency_fields_active_currency', true);
		$activeSymbolArr = give_multi_currency_get_symbols($activeCurrency);
	}

	// pega todos os gateways de pagamento
	$gateways = give_get_payment_gateways();
	// Procura pelo array pelas chaves correspondentes as configurações do plugin
	// E salva o nome delas
	$gateways = array_keys($gateways);
	for ($c = 0; $c < count($gateways); $c++) {
		// caso exista uma licença retira o link de divulgação do plugin
		$optionName = give_get_option($gateways[$c] . '_setting_field');
		if ($optionName) {
			$hasValidGateway = 'true';

			break;
		}
	}

	// Adiciona o nome de cada moeda ativa de acordo com o GiveWP
	for ($c = 0; $c < count($activeCurrency); $c++) {
		$activeCurrencyNames[] = give_get_currency_name($activeCurrency[$c]);
	}

	if ($pluginEnabled !== 'enabled' || $activeCurrency == false) {
		// caso não haja moedas selecionadas ou não esteja ativado
		// não cria nada no front-end
		return false;
	} elseif ($mainCurrency !== 'BRL') {
		Give()->notices->print_frontend_notice(
			sprintf(
				'<strong>%1$s</strong> %2$s',
				esc_html__('Erro:', 'give'),
				esc_html__('Plugin Multi Moedas só funciona com a moeda base reais (R$).', 'give')
			)
		);
	} elseif (give_get_option('number_decimals') > 0) {
		Give()->notices->print_frontend_notice(
			sprintf(
				'<strong>%1$s</strong> %2$s',
				esc_html__('Erro:', 'give'),
				esc_html__('Remova as casas decimais do valor da doação', 'give')
			)
		);
	} elseif (!in_array($configs['defaultCoin'], $activeCurrency) && $configs['defaultCoin'] !== 'BRL') {
		Give()->notices->print_frontend_notice(
			sprintf(
				'<strong>%1$s</strong> %2$s',
				esc_html__('Erro:', 'give'),
				esc_html__('Moeda padrão não está ativa no Multi Moedas', 'give')
			)
		);
	} else {
		// se prepara para fazer a conversão caso o paypal ecommerce esteja ativo
		if (give_is_gateway_active('paypal-commerce')) {
			$exchangeRate = give_multi_currency_get_exchange_rates($activeCurrency);
		} else {
			$exchangeRate = json_encode('disabled');
		}

		// Implementação front-end em EOT

		// Para passar os atributos para o javascript corretamente é necessário converter para JSON
		$activeCurrency = json_encode($activeCurrency);
		$activeCurrencyNames = json_encode($activeCurrencyNames);

		$html = <<<HTML

        <script>

        // atributos globais que não dependem de elementos HTML
        var currencySymbolArray = $activeSymbolArr;
        var exRate = $exchangeRate;
        var hasValidGateway = '$hasValidGateway';

        /**
         * Muda o label do valor total final do formulário legado
         * @TODO Necessário verificar melhor método para detectar quando um método de pagamento
         * estiver carregado para mudar o label corretamente
         * 
         * @return void
         */
        function changeLabelFinalAmountLegacy() {
            setTimeout(() => {
                var giveFinalPrice = document.getElementsByClassName('give-final-total-amount')[0];
                var currencyCode = document.getElementById('give-mc-select');

                if(giveFinalPrice) {
                    var simboloAntigo = giveFinalPrice.textContent;
                    
                    // Percorre toda a string para encontrar e substituir o símbolo da moeda
                    for(let d = 0; d < simboloAntigo.length; d++){
                        if(/\d/.test(simboloAntigo.charAt(d)) === true){
                            simboloAntigo = simboloAntigo.substr(0, d);
                            break;
                        }

                        if(d > 100) {
                            console.log('erro loop infinito');
                            break;
                        }

                    }
                    
                    // Muda o texto dentro do html caso seja um símbolo de moeda diferente
                    // ao esgotar o timeout
                    if(simboloAntigo !== giveFinalPrice.textContent){
                        giveFinalPrice.textContent = giveFinalPrice.textContent.replace(simboloAntigo, currencySymbolArray[currencyCode.value]);
                    }
                }
            }, 4000);
        }

        /**
         * Muda o label dos níveis do formulário legado
         * 
         * @return void
         */
        function changeLabelLegacy() {

            var priceList = document.getElementById('give-donation-level-button-wrap');
            var currencyCode = document.getElementById('give-mc-select');

            var giveFinalPrice = document.getElementsByClassName('give-final-total-amount')[0];
            var simboloAntigo = giveFinalPrice.textContent;
                
                // Percorre toda a string para encontrar e substituir o símbolo da moeda
                for(let d = 0; d < simboloAntigo.length; d++){
                    if(/\d/.test(simboloAntigo.charAt(d)) === true){
                        simboloAntigo = simboloAntigo.substr(0, d);
                        break;
                    }

                    if(d > 100) {
                        console.log('erro loop infinito');
                        break;
                }

            }
                
            // Substitui o símbolo de moeda antigo pelo selecionado dentro da tag html
            if(simboloAntigo !== giveFinalPrice.textContent){
                giveFinalPrice.textContent = giveFinalPrice.textContent.replace(simboloAntigo, currencySymbolArray[currencyCode.value]);
            }

            if(priceList) {
                var elemPriceList = priceList.getElementsByTagName('li'); 
                for(let c = 0; c < elemPriceList.length; c++){

                        var nodeChild = elemPriceList[c].children;
                        simboloAntigo = nodeChild[0].textContent;

                        for(let i = 0; i < simboloAntigo.length; i++){
                            if(/\d/.test(simboloAntigo.charAt(i)) === true){
                                simboloAntigo = simboloAntigo.substr(0, i);
                                break;
                            }

                            if(i > 100) {
                                console.log('erro loop infinito');
                                break;
                            }

                        }
                        if(simboloAntigo !== nodeChild[0].textContent){
                            nodeChild[0].textContent = nodeChild[0].textContent.replace(simboloAntigo, currencySymbolArray[currencyCode.value]);
                        }

                    }

            }else{
                return null;
            }

            
        }

        /**
         * @function
         * 
         * faz a conversão da moeda caso o gateway seja o paypal donations
         * 
         * @return void
         * 
         */
        function conversionCurrency() {

            // atributos necessários para fazer a conversão do paypal donations
            var form = document.getElementById('give-form-$id_prefix');
            var paypalCheckbox = document.getElementById('give-gateway-paypal-commerce-$id_prefix');
            var currencyCode = document.getElementById('give-mc-select');
            var amountLabel = document.getElementById('give-amount');
            var amount = document.getElementById('give-mc-amount');
            var giveTier = document.getElementsByName('give-price-id')[0];
            var totalFinal = document.getElementsByClassName('give-final-total-amount');

            // faz a verificação se o paypal donations está selecionado, é uma moeda estrangeira e se existe taxa de conversão
            if(exRate !== 'disabled' && paypalCheckbox.checked && currencyCode.value !== 'BRL') {

                form.setAttribute('data-currency_symbol', 'R$');
                form.setAttribute('data-currency_code', 'BRL');

                // para fazer a conversão coloca o valor convertido escondido do valor mostrado
                amountLabel.removeAttribute('name');
                amount.setAttribute('name', 'give-amount');
                // retira pontos e vírgulas para fazer a conversão
                amount.value = amountLabel.value.replace(/\D/gm,'');
                amount.value = amount.value / exRate[currencyCode.value];
                amount.value = Math.round(amount.value);

                // caso exista um 'totalPrice' ele muda para o amount.value
                if(totalFinal[0]) {
                    totalFinal[0].setAttribute('data-total', amount.value); 
                }

                // caso existam níveis de doação faz a mudança dos mesmos para validação do givewp
                if(giveTier){
                    giveTier.value = 'custom';
                
                    // Verifica se o valor convertido faz parte de algum tier de doação
                    var tierButtons = document.getElementsByClassName('give-donation-level-btn');
                    if(tierButtons) {
                        for(c = 0; c < tierButtons.length; c++) {
                            if(amount.value == tierButtons.item(c).value) {
                                // caso o valor convertido seja um tier de doação passa o id do tier para o give validar
                                giveTier.value = tierButtons.item(c).getAttribute('data-price-id');
                            }
                            // pega exceção de loop infinito
                            if(c > 100) {
                                console.log('caught exception infinite loop');
                                break;
                            }
                        }
                    }
                }

            } else { // caso for BRL ou outro gateway diferente do paypal donations não faz conversão e
                // reseta o nível da doação para o botão selecionado caso exista
                amountLabel.setAttribute('name', 'give-amount');
                amount.removeAttribute('name');
                if(giveTier){
                    var tierButtons = document.getElementsByClassName('give-donation-level-btn');
                    // verifica se existem níveis de doação
                    if(tierButtons) {
                        for(c = 0; c < tierButtons.length; c++) {
                            // faz o reset do give-price-id pro tier selecionado
                            if(amountLabel.value == tierButtons.item(c).value) {
                                giveTier.value = tierButtons.item(c).getAttribute('data-price-id');
                            }
                            // pega exceção de loop infinito
                            if(c > 100) {
                                console.log('caught exception infinite loop');
                                break;
                            }
                        }
                    }
                }
            }

        }

        /**
         * @function
         * 
         * Muda o símbolo da moeda ao selecionar uma opção
         * 
         * @return void
         * 
         */
        function currencyChange () {

            var currencyCode = document.getElementById('give-mc-select');
            var iframeLoader = parent.document.getElementsByClassName('iframe-loader')[0];
            var form = document.getElementById('give-form-$id_prefix');

            // caso for um formulário legado altera também os atributos do formulário para validação do giveWP
            if(!iframeLoader) { // verifica a existência do iframe loader que é específico do formulário novo
                form.setAttribute('data-currency_symbol', currencySymbolArray[currencyCode.value]);
                form.setAttribute('data-currency_code', currencyCode.value);
                changeLabelLegacy();
            }

            // todos os atributos dependentes de elementos html
            var giveInputCurrencySelected = document.getElementById('give-mc-currency-selected');
            var currencySymbolLabel = document.getElementsByClassName('give-currency-symbol')[0];
            var currencyCodeButtons = document.getElementsByClassName('currency');
            
            // altera o label para o código de moeda selecionado
            currencySymbolLabel.textContent = currencySymbolArray[currencyCode.value];
            giveInputCurrencySelected.value = currencyCode.value;

            // símbolo da moeda em todos os botões a partir do classname
            if(currencyCodeButtons) {
                for(c = 0; c < currencyCodeButtons.length; c++) {
                    currencyCodeButtons[c].textContent = currencySymbolArray[currencyCode.value];
                }
            }

        }

        /**
        * Seleciona a moeda padrão
        * 
        */
        function updateCoin(){
            var mcSelect = document.getElementById('give-mc-select');
            var defaultCoin = '$defaultCoin';

            // faz uma seleção dinâmica a partir das moedas ativas e a moeda padrão selecionada
            for(var c = 0; c < mcSelect.options.length; c++){
                if(mcSelect.options[c].value == defaultCoin) {
                    mcSelect.options[c].setAttribute('selected', defaultCoin);
                }
                // trata exceção de loop infinito
                if(c > 10) {
                    console.log('caught exception infinite loop');
                    break;
                }
            }

            // faz a mudança visual
            currencyChange();
            // faz conversão caso seja paypal donations
            conversionCurrency();
        }

        // a função só irá ser executada após a página carregar completamente
        document.addEventListener('DOMContentLoaded', function() {

            var c = 0;
            var activeCurrency = '$activeCurrency';
            activeCurrency = JSON.parse(activeCurrency);
            var activeCurrencyNames = '$activeCurrencyNames';
            activeCurrencyNames = JSON.parse(activeCurrencyNames);

            // Pega o objeto do link e o esconde se tiver uma licença válida
            var linkMultiMoedas = document.getElementById('link-multi-moedas');
            if(hasValidGateway == 'true') {
                linkMultiMoedas.classList.add('hidden-lkn');
                linkMultiMoedas.classList.remove('show-lkn');
            }else{
                linkMultiMoedas.classList.remove('hidden-lkn');
                linkMultiMoedas.classList.add('show-lkn');
            }

            // atributos para verificação
            var mcSelect = document.getElementById('give-mc-select');
            var listaValores = document.getElementById('give-donation-level-button-wrap');
            var gatewayList = document.getElementById('give-gateway-radio-list');
            var btnContinuar = document.getElementsByClassName('advance-btn');

            // Popula com o elemento <option></option> o objeto html select
            for (c = 0; c < activeCurrency.length; c++) {
                // lógica para popular seletor
                var opt = document.createElement('option');
                opt.value = activeCurrency[c];
                opt.innerHTML = activeCurrencyNames[c];
                mcSelect.appendChild(opt);
                if(c > 100) {
                    console.log('exception caught infinite loop');

                    break;
                }
            }

            // caso exista mais de um botão de continuar só chama a função no botão que o multi-moedas aparece
            if(btnContinuar.length > 1) {
                btnContinuar[1].addEventListener('click', function(){
                    conversionCurrency();
                }, false)
            } else if(btnContinuar.length == 1) { // caso só exista um botão adiciona um evento click nesse botão
                btnContinuar[0].addEventListener('click', function(){
                    conversionCurrency();
                },false);
            } else { // caso seja um formulário legado faz a conversão ao selecionar outra moeda
                conversionCurrency();
                mcSelect.addEventListener('change', function(){
                    conversionCurrency();
                } , false);
                if(listaValores){
                    var elemenListaValores = listaValores.getElementsByTagName('button');
                    for(let i = 0; i < elemenListaValores.length; i++){
                        elemenListaValores[i].addEventListener('click', function(){
                            setTimeout(() => {  conversionCurrency(); }, 300);
                        }, false);
                    }
                }
            }

            // adiciona um evento para executar a função de conversão caso mude o gateway de pagamento
            gatewayList.addEventListener('change', function(){
                conversionCurrency();
                changeLabelFinalAmountLegacy();
            }, false);

            //adicionar a funcao
            updateCoin();
        }, false);

        </script>

        <style>
        #give-mc-select{
            font-size: 18px;
        }
        #link-multi-moedas {
            justify-content: center;
            align-items: center;
            text-align: center;
            margin: 5px auto;
            font-size:11px;
            font-weight: 600;
            padding: 5px;
        }
        .hidden-lkn{
            display: none;
        }
        .show-lkn{
            display: block;
        }
        </style>

        <input type="hidden" id="give-mc-amount">
        <input type="hidden" id="give-mc-currency-selected" name="give-mc-selected-currency">

        <select id="give-mc-select" class="give-donation-amount" onchange="currencyChange()">

        <option value=$mainCurrency>$mainCurrencyName</option>

        </select>

        <a id="link-multi-moedas" href="https://www.linknacional.com.br/wordpress/givewp/multimoeda" target="_blank" rel="nofollow">Plugin Multi Moeda</a>

HTML;
	}
	echo $html;
}

add_action('give_before_donation_levels', 'give_multi_currency_selector', 10, 3);
