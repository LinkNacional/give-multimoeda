# GiveWP multi-moeda

O GiveWP Multi-Moeda é um plugin para a plataforma de doação GiveWP que tem o objetivo de fazer a conversão da moeda estrangeira para a moeda nacional (BRL) a fim de realizar um determinado pagamento internacional e o mesmo ser reconhecido pelos processadores de pagamento do Brasil.

## Dependências

O plugin Give-Multi-Moedas é dependente do plugin GiveWP, por favor certifique-se que o GiveWP esteja instalado e devidamente configurado antes de iniciar a instalação do Give-Multi-Moedas.

O plugin Give-Multi-Moedas só irá funcionar de forma adequada em formulários que estejam utilizando o 'Multi-step-donation-form' e com a opção 'quantia personalizada' ativada. É possível utilizá-lo com o formulário 'legacy-form' porém o plugin não terá a funcionalidade completa.

O plugin Give-Multi-Moedas precisa que os valores sejam números inteiros para funcionar corretamente, certifique-se que a quantidade de casas decimais do GiveWP seja 0. Essa opção está nas configurações do GiveWP na aba moedas como mostrado na imagem abaixo:

![Captura de tela de 2021-03-03 10-46-48](https://user-images.githubusercontent.com/74307223/109816811-ec8dc600-7c0f-11eb-9f68-c025bea125b6.png)

Lembre-se de salvar as alterações.

## Instalação

1) Faça o upload dos arquivos para a pasta /urldoseusite.com/wp-content/plugins/give-multimoeda/ caso a pasta do give-multimoeda não exista é necessário criá-la.

2) Após o upload vá para a área de administrador do seu wordpress e selecione a opção 'plugins'

3) Procure pelo plugin de nome 'Give - Multi Moedas'

4) Clique em ativar

5) Agora vá para o menu de configurações do GiveWP

6) Selecione a opção 'moedas'

7) Procure pela opção 'Habilitar Multi Moedas'

8) Clique em salvar

9) Novas opções de moeda irão aparecer é necessário que selecione ao menos uma moeda

10) Clique em salvar

Pronto Agora o plugin do Give-Multi-Moedas está ativo e em funcionamento.

##CHANGELOS

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

