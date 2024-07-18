window.addEventListener("load", function () {
    let iframe = document.querySelector("iframe");
    if (iframe) {
        let iframeDocument = iframe.contentDocument || iframe.contentWindow.document;
        initializeGiveWP(iframeDocument);
    }
});

function initializeGiveWP(iframeDocument) {
    let select = iframeDocument.querySelector("#give-mc-select");
    let inputSelect = iframeDocument.querySelector("#give-mc-currency-selected");
    let give_amount = iframeDocument.querySelector("#give-amount");
    let give_purchase_buttons = iframeDocument.querySelectorAll(".give-donation-level-btn");

    if (select) {
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
