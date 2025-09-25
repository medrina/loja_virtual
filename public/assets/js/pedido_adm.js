$(document).ready(function () {
    let coluna = document.querySelector('html')
    let largura = coluna.offsetWidth
    if(largura < 576) $('#statusPgto').removeClass('w-25').addClass('w-50')
    else if(largura < 767) $('#statusPgto').removeClass('w-25').addClass('w-50')

    $('#botao-alterar-status-entrega').on('click', () => {
        const dados = $('#formulario-alterar-status-entrega').serialize()
        $.ajax({
            type: "POST",
            url: "/admin/pedido/alterar-status",
            data: dados,
            dataType: "json",
            success: (response) => {
                alert('O status foi alterado com sucesso!')
                window.location.reload()
            },
            error: (error) => {
                console.log(`erro: ${error}`)
            }
        });
    })
});