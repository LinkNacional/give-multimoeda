window.addEventListener("load", function () {
    let iframe = document.querySelector("iframe");
    if (iframe) {
        var iframeDocument = iframe.contentDocument || iframe.contentWindow.document;
        let select = iframeDocument.querySelector("#give-mc-select");
        if (select) {
            let selectedOption = select.options[select.selectedIndex];
            let simbol = selectedOption.getAttribute("simbol");
            let inputSelect = iframeDocument.querySelector("#give-mc-currency-selected")

            if (simbol) {
                changeCurrencyCoin(iframeDocument, simbol);
            }
            if (inputSelect) {
                inputSelect.value = selectedOption.value;
            }
            select.addEventListener("change", function () {

                let selectedOption = select.options[select.selectedIndex];
                let simbol = selectedOption.getAttribute("simbol");
                if (simbol) {
                    changeCurrencyCoin(iframeDocument, simbol);

                }
                inputSelect.value = selectedOption.value;

            })
        }
    }



})

function changeCurrencyCoin(iframe, value) {
    let btns = iframe.querySelectorAll(".give-donation-level-btn");
    let span = iframe.querySelector(".give-currency-symbol");
    if (btns) {
        btns.forEach(element => {
            let filho = element.querySelector(".currency")
            if (filho) {
                filho.textContent = value;

            }
        });
    }
    if (span) {
        span.innerText = value
    }

    var amount = null
    if (iframe.querySelector("#give-amount")) {
        amount = iframe.querySelector("#give-amount").value;
        const valor = iframe.querySelectorAll('[data-tag="amount"]')[0];
        const valorTotal = iframe.querySelector('th[data-tag="total"]');

        if (valor && valorTotal) {
            valor.firstChild.innerHTML = value + amount;
            valorTotal.innerHTML = value + amount
        }
    }


}