# GiveWP multi-moeda

O [GiveWP Multi-Moeda](https://www.linknacional.com.br/wordpress/givewp/multimoeda/) é um plugin para a plataforma de doação GiveWP que tem o objetivo de fazer a conversão da moeda estrangeira para a moeda nacional (BRL) a fim de realizar um determinado pagamento internacional e o mesmo ser reconhecido pelos processadores de pagamento do Brasil.

## Dependências

O plugin Give-Multi-Moedas é dependente do plugin GiveWP, por favor certifique-se que o GiveWP esteja instalado e devidamente configurado antes de iniciar a instalação do Give-Multi-Moedas.

O plugin Give-Multi-Moedas precisa que os valores sejam números inteiros para funcionar corretamente, certifique-se que a quantidade de casas decimais do GiveWP seja 0. Essa opção está nas configurações do GiveWP na aba moedas como mostrado na imagem abaixo:

![Captura de tela de 2021-03-03 10-46-48](https://user-images.githubusercontent.com/74307223/109816811-ec8dc600-7c0f-11eb-9f68-c025bea125b6.png)

Lembre-se de salvar as alterações.

## Instalação

1) Procure na barra lateral a área de plugins do Wordpress;

2) Em plugins instalados procure pela opção 'adicionar novo' no cabeçalho;

3) Clique na opção de 'enviar plugin' no título da página e faça o upload do plugin multi-currency-for-give.zip;

4) Clique no botão 'instalar agora' e depois ative o plugin instalado;

5) Agora clique na opção 'Configurações' que fica ao lado do botão 'desativar';

6) Certifique-se que as seguintes configurações estejam preenchidas: 
- Moeda seja: Real brasileiro (R$);
- Posição da moeda como: Antes - R$10;
- Separador de milhares como: . ;
- Separador de decimal como: , ;
- Número de casas decimais como: 0;

7) Clique na opção 'Habilitar' na sessão 'Habilitar multi moedas';

8) Clique em salvar;

9) Ainda em 'Moeda' novas opções irão aparecer, marque a moeda padrão e habilite as moedas que seu formulário pode aceitar;

Pronto! Agora o plugin do Give-Multi-Moedas está ativo e em funcionamento.

## Modo de uso

1) Entre em um formulário de doação criado via Give WP;

2) No cabeçalho do formulário terá um seletor com a moeda padrão selecionada, geralmente será "Real Brasileiro" mas pode mudar de acordo com a moeda padrão definida;

3) Caso deseje doar em outra moeda clique no seletor e um drop-down com as opções de moedas ativas irá aparecer;

4) Escolha uma moeda;

5) Continue o processo de doação escolhendo valor e método de pagamento;

6) Clique em 'Doar';

Pronto! Você realizou sua primeira doação via plugin do multimoedas.

## Changelog

### 3.1.3 - 27/06/2025
* Adição de rotas de fallback em caso de erro na API.

### 3.1.2 - 02/05/2025
* Correção no action.

### 3.1.1 - 23/04/2025
* Atualização do script Paypal.

### 3.1.0 - 12/03/2025
* Conversão de moeda durante o processamento de pagamento Paypal.

### 3.0.3 - 29/11/2024
* Add new currency Swiss Franc(CHF)

### 3.0.2 - 26/09/2024
* Adição de tratamento para valores com decimal.

### 3.0.1 - 27/08/2024
* Correção e melhoria visual na exibição do plugin

### 3.0.0 - 16/08/2024
* Adição de suporte ao formulario GiveWP 3.0.0
* Correção de bugs
* Limpeza no código

### 2.7.0 - 13/06/2024
* Adição de suporte a peso mexicano;
* Adição de notificação para plugins instalados e inativos da Link Nacional.

### 2.6.0 - 23/12/2023
* Adição de changelogs;
* Adição de suporte a moeda Rial Saudito;
* Atualização de API de consulta de cotações.

### v2.5.2
- Atualização de endpoint de atualizações;
- Correção de notices de ativação do plugin;
- Adição de licença GPL 2.0.

### v2.5.1
- Atualização de método de consulta de cotação;
- Atualização de método de cálculo de preço convertido.

### v2.5.0

- Adição de compatibilidade com template 'Classic';
- Adição de compatibilidade com 'sumário da doação';
- Correções de bugs nos formulários de iframe;
- Refatoração do código e padronização dos comentários.

### v2.0.0
- não faz mais a conversão de valores exceto para o PayPal Donations;
- altera o 'countryCode' e o Gateway que faz a conversão;
- Compatibilidade básica com o formulário legado;

#### ATENÇÃO ESSA ATUALIZAÇÃO QUEBRA COMPATIBILIDADE COM PLUGINS DE FORMA DE PAGAMENTO DESATUALIZADOS CONFIRA NA LISTA AS VERSÕES COMPATÍVEIS:

* Facilpay v1.1.0+;
* Cielo 3DS v1.1.0+;
* Cielo v1.2.0+;
* Give Google Pay v1.1.0+;
* Give Visa Checkout v1.2.0+;

