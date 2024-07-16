

window.addEventListener("load",function(){
    let select =this.document.querySelector(".givewp-fields-amount__currency-select");
    if(select){
        // console.log(select.childElementCount)

        setTimeout(() => {
        select.selectedIndex = select.childElementCount -1
            
        }, 3000);
    }
})