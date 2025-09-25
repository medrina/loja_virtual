$(document).ready(function () {
    let coluna = document.querySelector('html')
    let largura = coluna.offsetWidth
    if(largura < 767) $('#cartao-bandeira').removeClass('text-center')
});