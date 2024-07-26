window.addEventListener("load", function () {
    let iframe = document.querySelector("#iFrameResizer0");
    if (iframe) {
        let iframeDocument = iframe.contentDocument || iframe.contentWindow.document;
        initializeGiveWP(iframeDocument);
    } else {
        updateCurrencySymbolLegacy();

    }
});

function initializeGiveWP(iframeDocument) {
    let select = iframeDocument.querySelector("#give-mc-select");
    let inputSelect = iframeDocument.querySelector("#give-mc-currency-selected");
    let give_amount = iframeDocument.querySelector("#give-amount");
    let give_purchase_buttons = iframeDocument.querySelectorAll(".give-donation-level-btn");
    if (select && iframeDocument) {
        handleSelectChange(select, inputSelect, iframeDocument);
        addEventListeners(select, inputSelect, give_amount, give_purchase_buttons, iframeDocument);
    }
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
    buttons.forEach(button => {
        button.textContent = value;
    });
}

function updateCurrencySymbol(iframe, value) {
    let currencySymbol = iframe.querySelector(".give-currency-symbol");
    if (currencySymbol) {
        currencySymbol.innerText = value;
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


function updateCurrencySymbolLegacy() {
    // Seleciona os elementos necessários
    const select = document.querySelector("#give-mc-select");
    const simbol = document.querySelector('.give-currency-symbol');
    const finalAmount = document.querySelector('.give-final-total-amount');
    const buttons = document.querySelectorAll('.give-donation-level-btn');
    const amount = document.querySelector("#give-amount");

    if (!select || !simbol || !finalAmount || !buttons || !amount) {
        console.warn("Nem todos os elementos necessários foram encontrados.");
        return;
    }

    // Função para atualizar todos os elementos com o novo símbolo
    const updateElements = () => {
        // Obtém o novo símbolo da moeda
        const newSymbol = select.options[select.selectedIndex].getAttribute("simbol");

        // Atualiza os botões de doação
        buttons.forEach(button => {
            if (button.value === "custom") {
                return;
            }

            const currentPriceValue = button.textContent.replace(/[^0-9.]/g, '');
            button.textContent = `${newSymbol}${currentPriceValue}`;

            // Adiciona o listener de clique apenas uma vez
            button.removeEventListener("click", handleButtonClick); // Remove o listener se já existir
            button.addEventListener("click", handleButtonClick);
        });

        // Atualiza o valor final
        const currentPriceValue = finalAmount.textContent.replace(/[^0-9.]/g, '');
        finalAmount.textContent = `${newSymbol} ${currentPriceValue}`;
        simbol.innerHTML = newSymbol;
    };

    // Função para lidar com o clique nos botões de doação
    function handleButtonClick(event) {
        const clickedButtonText = event.target.textContent;
        finalAmount.textContent = clickedButtonText;

        // Atualiza o símbolo no final após um pequeno atraso
        setTimeout(() => {
            const newSymbol = select.options[select.selectedIndex].getAttribute("simbol");
            finalAmount.textContent = `${newSymbol}${clickedButtonText.replace(/[^0-9.]/g, '')}`;
        }, 500);
    }

    // Listener para mudança no select
    select.addEventListener("change", updateElements);

    // Listener para mudança no input de valor
    amount.addEventListener("change", () => {
        const newSymbol = select.options[select.selectedIndex].getAttribute("simbol");
        const currentPriceValue = finalAmount.textContent.replace(/[^0-9.]/g, '');
        finalAmount.textContent = `${newSymbol}${currentPriceValue}`;
        simbol.innerHTML = newSymbol;
    });

    // Chamada inicial para configurar os elementos com o símbolo inicial
    updateElements();
}
