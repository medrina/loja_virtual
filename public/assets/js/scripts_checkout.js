$(document).ready(function () {
    let flagBotaoCartao = true
    let flagBotaoBoleto = true
    let flagBotaoPix = true
    let flagLinkPix = true

    let coluna = document.querySelector('html')
    let largura = coluna.offsetWidth

    if(largura <= 767) {
        $('.mascara-pagamento').css('top', '-100%')
    }

    let coluna3 = document.querySelector('html')
    let largura3 = coluna.offsetWidth

    $('#numero-cartao').mask("9999 9999 9999 9999")
    $('#mes-ano-cartao').mask("99/9999")
    $('#cod-seguranca').mask('999')
    $('#cpf-titular').mask("999.999.999-99")
    $('#mask-dados-pessoais').removeClass('mascara-dados-pessoais')
                
    $('#botao-dados-pessoais-checkout').on('click', () => {
        let dados = $('#form-dados-pessoais-checkout').serialize()
        $.ajax({
            type: "POST",
            url: "/checkout/cliente",
            data: dados,
            dataType: 'json',
            success: (resposta) => {
                if(resposta) {
                    let html = `<p class="text-center titulo dados">Dados de Identificação</p>
                                    <div class="dados">email: ${resposta['cliente'].email}</div>
                                    <div class="dados">Nome: ${resposta['cliente'].nome}</div>
                                    <div class="dados">Telefone: ${resposta['cliente_asaas'].telefone}</div>
                                    `
                    $('.caixa-entrega').css('display', 'block')
                    $('#resumo-dados-pessoais').html(html)
                    $('#mask-entrega').removeClass('mascara-entrega')
                    $('#mask-dados-pessoais').addClass('mascara-dados-pessoais').css('top', '-27%')
                    if(largura <= 767) $('.mascara-dados-pessoais').css('top', '-100%').css('width', '105%')
                }
            },
            error: (erro) => {
                console.log(`erro: ${erro}`)
            }
        });
    })

    $('#link-dados-pessoais').on('click', () => {
        $('.caixa-entrega').css('display', 'none')
        $('#mask-entrega').addClass('mascara-entrega').css('bottom', '93%').css('height', '95%')
        $('#mask-dados-pessoais').removeClass('mascara-dados-pessoais')
    })

    $('#opcao-cartao-credito').on('click', () => {
        if(flagBotaoBoleto || flagBotaoPix) {
            $('#botao-cartao-credito').removeClass('btn-outline-success')
            $('#botao-cartao-credito').addClass('btn-success')
            $('#botao-boleto').removeClass('btn-success')
            $('#botao-boleto').addClass('btn-outline-success')
            $('#botao-pix').removeClass('btn-success')
            $('#botao-pix').addClass('btn-outline-success')
            $('#cartao-credito').css('display', 'block')
            $('#boleto').css('display', 'none')
            $('#pix').css('display', 'none')
            flagBotaoCartao = true
            flagBotaoBoleto = false
            flagBotaoPix = false
            flagLinkPix = true
        }
    })

    $('#opcao-boleto').on('click', () => {
        if(flagBotaoCartao || flagBotaoPix) {
            $('#botao-boleto').removeClass('btn-outline-success')
            $('#botao-boleto').addClass('btn-success')
            $('#botao-cartao-credito').removeClass('btn-success')
            $('#botao-cartao-credito').addClass('btn-outline-success')
            $('#botao-pix').removeClass('btn-success')
            $('#botao-pix').addClass('btn-outline-success')
            $('#boleto').css('display', 'block')
            $('#cartao-credito').css('display', 'none')
            $('#pix').css('display', 'none')
            flagBotaoBoleto = true
            flagBotaoCartao = false
            flagBotaoPix = false
            flagLinkPix = true
        }
    })

    $('#opcao-pix').on('click', () => {
        if(flagBotaoCartao || flagBotaoBoleto) {
            if(!flagLinkPix) {
                $('#parte2-3-pix').css('display', 'block')
                flagLinkPix = true
            }
            $('#botao-pix').removeClass('btn-outline-success')
            $('#botao-pix').addClass('btn-success')
            $('#botao-cartao-credito').removeClass('btn-success')
            $('#botao-cartao-credito').addClass('btn-outline-success')
            $('#botao-boleto').removeClass('btn-success')
            $('#botao-boleto').addClass('btn-outline-success')
            $('#pix').css('display', 'block')
            $('#cartao-credito').css('display', 'none')
            $('#boleto').css('display', 'none')
            flagBotaoPix = true
            flagBotaoBoleto = false
            flagBotaoCartao = false
        }
    })

    $('#cpf-titular').on('blur', () => {
        var strCPF = $('#cpf-titular').val()
        strCPF = strCPF.replace('.', '')
        strCPF = strCPF.replace('.', '')
        strCPF = strCPF.replace('-', '')
        var Soma = 0;
        var Resto;
        if ($('#cpf-titular').val() === '') {
            $('#resp-cpf').html('')
        }
        else {
            for(i=1; i<=9; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (11 - i);
            Resto = (Soma * 10) % 11;
            if ((Resto == 10) || (Resto == 11))  Resto = 0;
            if (Resto != parseInt(strCPF.substring(9, 10))) $('#resp-cpf').html('<span class="text-danger">CPF Inválido!</span>')
            Soma = 0;
            for(i = 1; i <= 10; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (12 - i);
            Resto = (Soma * 10) % 11;
            if ((Resto == 10) || (Resto == 11))  Resto = 0;
            if (Resto != parseInt(strCPF.substring(10, 11))) {
                $('#resp-cpf').html('<span class="text-danger">CPF Inválido!</span>')
                flagCPF = false
            }
            else {
                let cpf = $('#cpf-titular').val()
                cpf = cpf.replace('.', '')
                cpf = cpf.replace('.', '')
                cpf = cpf.replace('-', '')
                
                $('#resp-cpf').html('<span class="text-success">CPF Válido!</span>')
                flagCPF = true
            }
        }
    })

    $('#botao-selecionar-pgto-boleto').on('click', () => {
        let dados = $('#form-boleto').serialize()
        $('#forma-pagamento-resultado').html(`<div class="dados-forma-pgto">Forma de Pagamento:<br>Boleto Bancário</div>`)
        $.ajax({
            type: "POST",
            url: "/checkout/cliente/selec-pgto",
            data: dados,
            dataType: 'json',
            success: (resposta) => {
                if(resposta) {
                    $('#mask-pagamento').addClass('mascara-pagamento').css('bottom', '240px')
                    $('#botao-confirmar1').html(`<hr>
                                                    <button id="botao-confirmar-compra" type="button" class="btn btn-primary form-control botao-confirmacao botao-final-compra" onclick="finalizarPagamento()">FINALIZAR COMPRA</button>
                                                </div>
                                                <hr>`)
                }
                colocarMascaraEntrega()
                let pag = document.getElementById('mask-pagamento')
                pag = pag.getBoundingClientRect().height
                $('.mascara-entrega').css('height', pag)
                if(largura > 1399) {
                    $('.mascara-pagamento').css('bottom', '236px')
                }
                else if(largura >= 1200 && largura <= 1399) {
                    $('.mascara-pagamento').css('bottom', '236px')
                }
                else if(largura >= 992 && largura <= 1199) {
                    $('.mascara-pagamento').css('bottom', '236px')
                }
                else if(largura >= 768 && largura <= 991) {
                    $('.mascara-pagamento').css('bottom', '260px')
                }
            },
            error: (erro) => {
                console.log(`erro: ${erro}`)
            }
        });
    })

    $('#botao-selecionar-pgto-pix').on('click', () => {
        let dados = $('#form-pix').serialize()
        $('#forma-pagamento-resultado').html(`<div class="dados-forma-pgto">Forma de Pagamento:<br>PIX</div>`)
        $.ajax({
            type: "POST",
            url: "/checkout/cliente/selec-pgto",
            data: dados,
            dataType: 'json',
            success: (resposta) => {
                if(resposta) {
                    $('#mask-pagamento').addClass('mascara-pagamento').css('bottom', '540px')
                    $('#botao-confirmar1').html(`<hr>
                                                    <button id="botao-confirmar-compra" type="button" class="btn btn-primary form-control botao-confirmacao" onclick="finalizarPagamento()">FINALIZAR COMPRA</button>
                                                </div>
                                                <hr>`)
                }
                colocarMascaraEntrega()
                let pag = document.getElementById('mask-pagamento')
                pag = pag.getBoundingClientRect().height
                $('.mascara-entrega').css('height', pag)
                if(largura > 1399) {
                    $('.mascara-pagamento').css('bottom', '470px')
                }
                else if(largura >= 1200 && largura <= 1399) {
                    $('.mascara-pagamento').css('bottom', '486px')
                }
                else if(largura >= 992 && largura <= 1199) {
                    $('.mascara-pagamento').css('bottom', '507px')
                }
                else if(largura >= 768 && largura <= 991) {
                    $('.mascara-pagamento').css('bottom', '540px')
                }
                else if(largura <= 767) $('#botao-confirmar-compra').addClass('botao-final-compra')
            },
            error: (erro) => {
                console.log(`erro: ${erro}`)
            }
        });
    })

    $('#gerar-cod-pix').on('click', () => {
        let codigo1 = (Math.floor(Math.random() * (9999999999 - 1000000000 + 1)) + 1000000000).toString()
        let codigo2 = (Math.floor(Math.random() * (9999999999 - 1000000000 + 1)) + 1000000000).toString()
        let codigo3 = (Math.floor(Math.random() * (9999999999 - 1000000000 + 1)) + 1000000000).toString()
        let codigoPix = codigo1 + codigo2 + codigo3
        $('#resp-codigo-pix').html('<i class="fa-solid fa-spinner fa-spin-pulse spinner-checkout"></i>')
        setTimeout(() => {
            $('#resp-codigo-pix').html(`<p><b>Código abaixo:</b><br><span>${codigoPix}</span></p><div><p>Copiar Pix: <a id="copiar_cod_pix" href="#gerar-cod-pix" onclick="copiarCodPix()"><i id="icone-copiar-pix" class="fa-solid fa-copy fa-beat" title="Código PIX"></i></a></p></div>`)
            $('#codigo-pix').val(codigoPix)
            $('#parte2-3-pix').css('display', 'block')
        }, 3000)
    })

    $('#numero-cartao').on('keyup', (e) => {
        let numero = e.target.value
        if(numero[6] != '_') {
            let numero2 = numero.substring(0, 7)
            numero2 = numero2.replace(' ', '')
            switch(numero2[0]) {
                case '4': $('#bandeira-logo').html('<i class="fa-brands fa-cc-visa bandeira-simbolo"></i>')
                            $('#id-bandeira').val(1)
                            break
                case '5': $('#bandeira-logo').html('<i class="fa-brands fa-cc-mastercard bandeira-simbolo"></i>')
                            $('#id-bandeira').val(2)
                            break
                case '6': $('#bandeira-logo').html('<i class="fa-brands fa-cc-amex bandeira-simbolo"></i>')
                            $('#id-bandeira').val(3)
                            break
                case '7': $('#bandeira-logo').html('<i class="fa-brands fa-cc-diners-club bandeira-simbolo"></i>')
                            $('#id-bandeira').val(4)
                            break
                case '8': $('#bandeira-logo').html('<i class="fa-brands fa-cc-discover bandeira-simbolo"></i>')
                            $('#id-bandeira').val(5)
                            break
                default: $('#bandeira-logo').html('<span id="erro-cartao" class="text-danger">Nº INVÁLIDO</span>')
            }
        }
    })

    $('#numero-cartao').on('blur', () => {
        let numeroCartao = $('#numero-cartao').val()
        if(numeroCartao === '') $('#bandeira-logo').html('')
    })

    if(largura < 992) $('.mascara-pagamento').css('bottom', '260px')

});

let email = $('#checkout-email').val()
let valorPedido = parseFloat($('#valor-total-pedido-oculto').val())
let tel = ''
let flagTelefone = false
let x = ''
let y = ''
let fone = ''
let cont = 1
let contEndereco = 1
let frete = 0
let totalComFrete = 0
let coluna = document.getElementById('coluna-entrega')
let altura = coluna.getBoundingClientRect().height
let largura = coluna.getBoundingClientRect().width
let topo = coluna.getBoundingClientRect().top
let left = coluna.getBoundingClientRect().left
let bottom = coluna.getBoundingClientRect().bottom
let right = coluna.getBoundingClientRect().right
flagTop = 1

function colocarMascaraEntrega() {
    $('.mascara-entrega')
        .css('left', left)
        .css('right', right)
        .css('top', topo)
        .css('bottom', bottom)
        .css('width', largura)
        .css('height', altura)
}
colocarMascaraEntrega()
resumoPedido()

let coluna2 = document.querySelector('html')
let largura2 = coluna.offsetWidth

function telefone(e) {
    let input = e.target
    input.value = mascaraTelefone(input.value)
}

function testarTelefone() {
    let telefone = $('#telefone-editar').val()
    if(telefone.length < 15) {
        $('#resposta-telefone').html('<span class="text-danger">Telefone Inválido!</span>')
        flagTelefone = false
    }
    else {
        let telefone = $('#telefone-editar').val()
        telefone = telefone.replace('(', '')
        telefone = telefone.replace(')', '')
        telefone = telefone.replace(' ', '')
        telefone = telefone.replace('-', '')
        $('#telefone-editar_').val(telefone)
        y = telefone
        $('#resposta-telefone').html('<span class="text-success">Telefone Válido!</span>')
        flagTelefone = true
    }
}

function mascaraTelefone(value) {
    if (!value) return ""
    value = value.replace(/\D/g,'')
    value = value.replace(/(\d{2})(\d)/,"($1) $2")
    value = value.replace(/(\d)(\d{4})$/,"$1-$2")
    return value
}

function enderecoRadio(cep, id_endereco) {
    let resp = '[{"id":1,"name":"PAC","price":"20.78","custom_price":"20.78","discount":"3.52","currency":"R$","delivery_time":7,"delivery_range":{"min":6,"max":7},"custom_delivery_time":7,"custom_delivery_range":{"min":6,"max":7},"packages":[{"price":"20.78","discount":"3.52","format":"box","dimensions":{"height":0,"width":8,"length":13},"weight":"0.10","insurance_value":"0.00"}],"additional_services":{"receipt":false,"own_hand":false,"collect":false},"company":{"id":1,"name":"Correios","picture":"https:\/\/sandbox.melhorenvio.com.br\/images\/shipping-companies\/correios.png"}},{"id":2,"name":"SEDEX","price":"45.53","custom_price":"22.53","discount":"3.27","currency":"R$","delivery_time":3,"delivery_range":{"min":4,"max":5},"custom_delivery_time":5,"custom_delivery_range":{"min":4,"max":5},"packages":[{"price":"22.53","discount":"3.27","format":"box","dimensions":{"height":0,"width":8,"length":13},"weight":"0.10","insurance_value":"0.00"}],"additional_services":{"receipt":false,"own_hand":false,"collect":false},"company":{"id":1,"name":"Correios","picture":"https:\/\/sandbox.melhorenvio.com.br\/images\/shipping-companies\/correios.png"}},{"id":3,"name":".Package","price":"15.53","custom_price":"15.53","discount":"0.00","currency":"R$","delivery_time":3,"delivery_range":{"min":2,"max":3},"custom_delivery_time":3,"custom_delivery_range":{"min":2,"max":3},"packages":[{"format":"box","dimensions":{"height":1,"width":1,"length":1},"weight":"0.10","insurance_value":"0.00"}],"additional_services":{"receipt":false,"own_hand":false,"collect":false},"company":{"id":2,"name":"Jadlog","picture":"https:\/\/sandbox.melhorenvio.com.br\/images\/shipping-companies\/jadlog.png"}},{"id":4,"name":".Com","price":"16.76","custom_price":"16.76","discount":"0.00","currency":"R$","delivery_time":3,"delivery_range":{"min":2,"max":3},"custom_delivery_time":3,"custom_delivery_range":{"min":2,"max":3},"packages":[{"format":"box","dimensions":{"height":1,"width":1,"length":1},"weight":"0.10","insurance_value":"0.00"}],"additional_services":{"receipt":false,"own_hand":false,"collect":false},"company":{"id":2,"name":"Jadlog","picture":"https:\/\/sandbox.melhorenvio.com.br\/images\/shipping-companies\/jadlog.png"}},{"id":17,"name":"Mini Envios","price":"14.51","custom_price":"14.51","discount":"0.00","currency":"R$","delivery_time":9,"delivery_range":{"min":8,"max":9},"custom_delivery_time":9,"custom_delivery_range":{"min":8,"max":9},"packages":[{"price":"14.51","discount":"0.00","format":"box","dimensions":{"height":1,"width":8,"length":13},"weight":"0.00","insurance_value":"0.00"}],"additional_services":{"receipt":false,"own_hand":false,"collect":false},"company":{"id":1,"name":"Correios","picture":"https:\/\/sandbox.melhorenvio.com.br\/images\/shipping-companies\/correios.png"}}]'
    resp = JSON.parse(resp)
    let freteFormatado = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(resp[0].price)
    $(`#resposta-frete-id-${id_endereco}`).html('<div class="dados"><b>Calculando Frete...</div></span><i class="fa-solid fa-spinner fa-spin-pulse spinner-checkout"></i>')
    let frete = `
        <div class="espaco-fretes">
        <div id="titulo-frete-id-${id_endereco}" class="dados"><b>Escolha alguma das opções de Frete abaixo:</b></div>
        <div id="frete-1-id_endereco-${id_endereco}" class="mt-2">
            <div class="dados">PREÇO FRETE: ${freteFormatado}</div>
            <div class="dados">TEMPO DE ENTREGA: ${resp[0].delivery_time} DIAS</div>
            <div class="dados">TRANSPORTADORA: ${resp[0].company.name}</div>
            <div class="dados mb-3">TIPO ENTREGA: ${resp[0].name}</div>
            <form id="form-frete-1">
                <input type="hidden" hidden="true" name="frete_id_transportadora" value="${resp[0].company.id}">
                <input id="frete1-valor-id-${id_endereco}" type="hidden" hidden="true" name="frete_valor" value="${resp[0].price}">
                <input type="hidden" hidden="true" name="frete_tempo_entrega" value="${resp[0].delivery_time}">
                <input type="hidden" hidden="true" name="frete_transportadora" value="${resp[0].company.name}">
                <input type="hidden" hidden="true" name="frete_tipo_entrega" value="${resp[0].name}">
                <div id="botao-frete-escolha-confirmar-1-${id_endereco}">
                    <button id="botao-frete-id-${id_endereco}" type="button" class="btn btn-success botao-confirmacao dados mb-3" onclick="enviarFrete(${id_endereco}, 1)">SELECIONAR ESSE FRETE</button>
                </div>
            </form>
        </div>
        <div id="frete-2-id_endereco-${id_endereco}">
            <div class="dados">PREÇO FRETE: R$ ${resp[1].price}</div>
            <div class="dados">TEMPO DE ENTREGA: ${resp[1].delivery_time} DIAS</div>
            <div class="dados">TRANSPORTADORA: ${resp[1].company.name}</div>
            <div class="dados mb-3">TIPO ENTREGA: ${resp[1].name}</div>
            <form id="form-frete-2">
                <input type="hidden" hidden="true" name="frete_id_transportadora" value="${resp[1].company.id}">
                <input id="frete2-valor-id-${id_endereco}" type="hidden" hidden="true" name="frete_valor" value="${resp[1].price}">
                <input type="hidden" hidden="true" name="frete_tempo_entrega" value="${resp[1].delivery_time}">
                <input type="hidden" hidden="true" name="frete_transportadora" value="${resp[1].company.name}">
                <input type="hidden" hidden="true" name="frete_tipo_entrega" value="${resp[1].name}">
                <div id="botao-frete-escolha-confirmar-2-${id_endereco}">
                    <button id="botao-frete-id-${id_endereco}" type="button" class="btn btn-success botao-confirmacao dados mb-3" onclick="enviarFrete(${id_endereco}, 2)">SELECIONAR ESSE FRETE</button>
                </div>
            </form>
        </div>
        </div>`
    setTimeout(() => {
        $(`#resposta-frete-id-${id_endereco}`).html(frete)
    }, 3000)
}

function enviarFrete(id_endereco, opcao) {
    let dados = $(`#form-frete-${opcao}`).serialize()
    frete = parseFloat($(`#frete${opcao}-valor-id-${id_endereco}`).val())
    $.ajax({
        type: "POST",
        url: "/endereco/frete",
        data: {dados},
        dataType: "json",
        success: (response) => {
            if(response) {
                if(opcao == 1) {
                    $(`#frete-1-id_endereco-${id_endereco}`).addClass('marcar-endereco')
                    $(`#frete-2-id_endereco-${id_endereco}`).removeClass('marcar-endereco')
                    $(`#botao-frete-id-${id_endereco}`).css('display', 'none')
                    $(`#botao-frete-escolha-confirmar-1-${id_endereco}`).html(`<button id="botao-confirmar-id-${id_endereco}" type="button" class="btn btn-primary form-control botao-confirmacao dados mb-3" onclick="confirmarEndereco(${id_endereco})">CONFIRMAR</button>`)
                    $(`#botao-frete-escolha-confirmar-2-${id_endereco}`).html(`<button id="botao-frete-id-${id_endereco}" type="button" class="btn btn-success botao-confirmacao dados mb-3" onclick="enviarFrete(${id_endereco}, 2)">SELECIONAR ESSE FRETE</button>`)
                }
                else if(opcao == 2) {
                    $(`#frete-2-id_endereco-${id_endereco}`).addClass('marcar-endereco')
                    $(`#frete-1-id_endereco-${id_endereco}`).removeClass('marcar-endereco')
                    $(`#botao-frete-id-2-${id_endereco}`).css('display', 'none')
                    $(`#botao-frete-escolha-confirmar-2-${id_endereco}`).html(`<button id="botao-confirmar-id-${id_endereco}" type="button" class="btn btn-primary botao-confirmacao dados form-control mb-3" onclick="confirmarEndereco(${id_endereco})">CONFIRMAR</button>`)
                    $(`#botao-frete-escolha-confirmar-1-${id_endereco}`).html(`<button id="botao-frete-id-${id_endereco}" type="button" class="btn btn-success botao-confirmacao dados mb-3" onclick="enviarFrete(${id_endereco}, 1)">SELECIONAR ESSE FRETE</button>`)
                }
                let valorFreteFormatado = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(frete)
                $('#valor-frete').html(` ${valorFreteFormatado}`)
                totalComFrete = (parseFloat($('#pedido-valor-total').val())) + frete
                let valorTotalFormatado = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(totalComFrete)
                $('#valor-total-pedido').html(` ${valorTotalFormatado}`)
            }
            else {
                alert('Erro ao selecionar Frete! Tente novamente')
            }
        },
        error: (erro) => {
            console.log(`erro: ${erro}`)
        }
    });
}

function confirmarEndereco(id_endereco) {
    $.ajax({
        type: "POST",
        url: "/endereco/frete-confirm",
        data: {id_endereco},
        dataType: "json",
        success: (response) => {
            if(response) {
                let pag = document.getElementById('mask-pagamento')
                pag = pag.getBoundingClientRect().height
                $('#mask-pagamento').removeClass('mascara-pagamento')
                $('#mask-entrega').addClass('mascara-entrega').css('bottom', '50%')
                $(`#frete-1-id_endereco-${id_endereco}`).css('display', 'none')
                $(`#frete-2-id_endereco-${id_endereco}`).css('display', 'none')
                $(`#titulo-opcoes-frete-id-${id_endereco}`).css('display', 'none')
                $(`#titulo-frete-id-${id_endereco}`).html('')
                $('.espaco-fretes').html('')
                let data = new Date()
                let dataVencimentoBoleto = new Date(data.getTime() + (7 * 24 * 60 * 60 * 1000))
                $('#data-venc-boleto').text(dataVencimentoBoleto.toLocaleDateString())
                $('#data-vencimento-boleto').val(dataVencimentoBoleto.toLocaleDateString())
                let html = `<p class="text-center dados titulo">Dados de Entrega</p>
                            <div class="dados"><b>Endereço:</b><br>
                            <div class="dados-end">${response['endereco'].logradouro}</div>
                            <div class="dados-end">Nº: ${response['endereco'].numero}</div>
                            <div class="dados-end">Complemento: ${response['endereco'].complemento}</div>
                            <div class="dados-end">Bairro: ${response['endereco'].bairro}</div>
                            <div class="dados-end">Cidade: ${response['endereco'].nome}</div>
                            <div class="dados-end">Estado: ${response['endereco'].uf}</div>
                            <div class="dados-end">CEP: ${response['endereco'].cep}</div><br>
                            <div class="dados-end"><b>Frete:</b></div>
                            <div class="dados-end">Preço: R$ ${response['frete'].valorFrete}</div>
                            <div class="dados-end">Transportadora: ${response['frete'].transportadoraNome}</div>
                            <div class="dados-end">Tipo de Entrega: ${response['frete'].tipoEntrega}</div>
                            <div class="dados-end">Tempo de Entrega: ${response['frete'].tempoEntrega} dias</div>
                            `
                $('#resumo-entrega').html(html)
                let selectParcelas = `<div><label id="parcelas-titulo">Parcelas</label>
                            <select id="numero-parcelas" class="form-control tam-input-fonte dados" name="parcelas-cartao-credito-checkout" onchange="selecionarParcela(this)">
                                <option class="dados-opcoes-pgto value="0">Em quantas parcelas deseja pagar?</option></div>`
                let calculo = 0
                let strCalculo = ''
                if(totalComFrete < 1200) {
                    for(var i = 1; i <= 10; i++) {
                        calculo = totalComFrete / i
                        calculo = calculo.toFixed(2)
                        strCalculo = calculo.toString()
                        strCalculo = strCalculo.replace('.', ',')
                        if(strCalculo.length == 7) {
                            let ponto = '.'
                            let posicao = 1
                            let parte1 = strCalculo.substr(0, posicao)
                            let parte2 = strCalculo.substr(posicao)
                            let novaString  = parte1 + ponto + parte2
                            if(i < 2) {
                                selectParcelas += `<option class="dados-opcoes-pgto" value="1">Pagamento à vista - R$ ${novaString}</option>`
                            }
                            else {
                                selectParcelas += `<option class="dados-opcoes-pgto" value="${i}">${i}x de R$ ${novaString} sem juros</option>`
                            }
                            continue
                        }
                        else if(strCalculo.length == 8) {
                            let ponto = '.'
                            let posicao = 2
                            let parte1 = strCalculo.substr(0, posicao)
                            let parte2 = strCalculo.substr(posicao)
                            let novaString  = parte1 + ponto + parte2
                            if(i < 2) {
                                selectParcelas += `<option class="dados-opcoes-pgto" value="1">Pagamento à vista - R$ ${novaString}</option>`
                            }
                            else {
                                selectParcelas += `<option class="dados-opcoes-pgto" value="${i}">${i}x de R$ ${novaString} sem juros</option>`
                            }
                            continue
                        }
                        selectParcelas += `<option class="dados-opcoes-pgto" value="${i}">${i}x de R$ ${strCalculo} sem juros</option>`                                
                    }
                }
                else {
                    for(var i = 1; i <= 12; i++) {
                        calculo = totalComFrete / i
                        calculo = calculo.toFixed(2)
                        strCalculo = calculo.toString()
                        strCalculo = strCalculo.replace('.', ',')
                        if(strCalculo.length == 7) {
                            let ponto = '.'
                            let posicao = 1
                            let parte1 = strCalculo.substr(0, posicao)
                            let parte2 = strCalculo.substr(posicao)
                            let novaString  = parte1 + ponto + parte2
                            if(i < 2) {
                                selectParcelas += `<option class="dados-opcoes-pgto" value="1">Pagamento à vista - R$ ${novaString}</option>`
                            }
                            else {
                                selectParcelas += `<option class="dados-opcoes-pgto" value="${i}">${i}x de R$ ${novaString} sem juros</option>`
                            }
                            continue
                        }
                        else if(strCalculo.length == 8) {
                            let ponto = '.'
                            let posicao = 2
                            let parte1 = strCalculo.substr(0, posicao)
                            let parte2 = strCalculo.substr(posicao)
                            let novaString  = parte1 + ponto + parte2
                            if(i < 2) {
                                selectParcelas += `<option class="dados-opcoes-pgto value="1">Pagamento à vista - R$ ${novaString}</option>`
                            }
                            else {
                                selectParcelas += `<option class="dados-opcoes-pgto" value="${i}">${i}x de R$ ${novaString} sem juros</option>`
                            }
                            continue
                        }
                        selectParcelas += `<option class="dados-opcoes-pgto" value="${i}">${i}x de R$ ${strCalculo} sem juros</option>`
                    }
                }
                selectParcelas += '</select>'
                $('#selecao-parcelas').html(selectParcelas)
                $('#val-total-com-frete').val(totalComFrete)
                $('#val-total-com-frete-boleto').val(totalComFrete)
                let valorComFreteFormatado = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(totalComFrete)
                $('#valor-total-com-frete-boleto').text(valorComFreteFormatado)
                $('#val-total-com-frete-pix').val(totalComFrete)
                $('#valor-total-com-frete-pix').text(valorComFreteFormatado)
                colocarMascaraEntrega()
                $('.mascara-entrega').css('height', pag)
                let largura4 = resumoPedido()
                if(largura4 <= 767) {
                    $('.mascara-entrega').css('height', '40%')
                }
            }
        },
        error: (erro) => {
            console.log(`erro: ${erro}`)
        }
    });
}

// selecionar pagamento via cartão de crédito
function selecionarPgto() {
    let parcelas = $('#numero-parcelas').val()
    let flag = true
    let erroCartao = $('#bandeira-logo').text()
    if($('#resp-cpf').text() === 'CPF Inválido!') {
        flag = false
        alert('CPF Inválido!')
    }
    else {
        let teste = $('input.campos-cartao')
        for(var i = 0; i < teste.length; i++) {
            if(teste[i].value == '' || parcelas == 0 || parcelas === 'Em quantas parcelas deseja pagar?') {
                alert('Preencha todos os campos!!!')
                $('#erro-cartao').text('')
                flag = false
                break
            }
        }
    }
    if(flag) {
        let dados = $('#form-cartao-credito').serialize()
        let numeroParcelas = $('#numero-parcelas').val()
        let valorParcela = $('#val-parcela').val()
        valorParcela = valorParcela.replace('.', ',')
        $('#forma-pagamento-resultado').html(`<div class="dados-forma-pgto">Forma de Pagamento:<br>Cartão de Crédito<br>Parcelado em ${numeroParcelas} x R$ ${valorParcela}</div>`)
        $.ajax({
            type: "POST",
            url: "/checkout/cliente/selec-pgto",
            data: dados,
            dataType: 'json',
            success: (resposta) => {
                if(resposta) {
                    $('#mask-pagamento').addClass('mascara-pagamento')
                    $('#botao-confirmar1').html(`<hr>
                                                    <button id="botao-confirmar-compra" type="button" class="btn btn-primary form-control botao-confirmacao" onclick="finalizarPagamento()">FINALIZAR COMPRA</button>
                                                </div>
                                                <hr>`)
                    colocarMascaraEntrega()
                    let pag = document.getElementById('mask-pagamento')
                    pag = pag.getBoundingClientRect().height
                    $('.mascara-entrega').css('height', pag)
                    let largura4 = resumoPedido()
                    
                    if(largura4 > 1399) {
                        $('.mascara-pagamento').css('bottom', '500px')
                    }
                    else if(largura4 >= 1200 && largura4 <= 1399) {
                        $('.mascara-pagamento').css('bottom', '500px')
                    }
                    else if(largura4 >= 992 && largura4 <= 1199) {
                        $('.mascara-pagamento').css('bottom', '500px')
                    }
                    else if(largura4 > 768 && largura4 <= 991) {
                        $('.mascara-pagamento').css('bottom', '500px')
                    }
                    else if(largura4 <= 767) {
                        $('#cartao-credito').css('display', 'none')
                        $('.mascara-pagamento').css('height', '100%')
                        $('#botao-confirmar-compra').addClass('botao-final-compra')
                    }
                }
            },
            error: (erro) => {
                console.log(`erro: ${erro}`)
            }
        });
    }
}

function selecionarParcela(event) { 
    let valorParcela = totalComFrete / event.value
    valorParcela = valorParcela.toFixed(2)
    $('#val-parcela').val(valorParcela)
    }

    function finalizarPagamento() {
    window.scrollTo({top: 0, behavior: 'auto'})
    $('#capa').addClass('capa').html(`
                        <div class="container">
                            <div class="row">
                                <div class="col-4">
                                </div>
                                <div class="col-8">
                                    <div>
                                        <h3>Aguarde...<br>Processando o seu pagamento</h3>
                                    </div>
                                    <i class="fa-solid fa-spinner fa-spin-pulse spinner-checkout"></i>
                                </div>
                            </div>
                        </div>`)
    let dados = true
    $.ajax({
        type: "POST",
        url: "/checkout/cliente/pagar",
        data: {dados},
        dataType: 'json',
        success: (resposta) => {
            if(resposta) {
                setTimeout(() => {
                    alert('Parabéns, sua compra foi Autorizada!!!\nAcompanhe o status de entrega do seu pedido no Menu Pedidos no seu Painel Inicial')
                    window.location.assign('/cliente/painel')
                }, 5000)
            }
            else {
                setTimeout(() => {
                    $('#capa').css('display', 'none')
                    $('#principal').html(`<div class="row">
                                                <div class="col-2"></div>
                                                <div class="col-8">
                                                    <h1 class="text-danger">Atenção! Sua compra não foi Autorizada !!!</h1>
                                                    <p class="status-entrega">Seu cartão/ou os seus dados mostraram falha na tentativa de compra. Entre em contato com a operadora do seu cartão, para buscar maiores informações</p>
                                                </div>
                                                <div class="col-2"></div>
                                            </div>`)
                }, 5000)
            }
        },
        error: (erro) => {
            console.log(`erro: ${erro}`)
        }
    });
    }

function handleZipCode(event) {
    let input = event.target
    input.value = zipCodeMask(input.value)
}

function zipCodeMask(value) {
    if (!value) return ""
    value = value.replace(/\D/g,'')
    value = value.replace(/(\d{5})(\d)/,'$1-$2')
    return value
}

function resumoPedido() {
    var coluna = document.querySelector('html')
    var largura = coluna.offsetWidth
    return largura
}

function copiarCodPix() {
    let codPix = $('#codigo-pix').val()
    navigator.clipboard.writeText(codPix).then(function() {
        alert('PIX copiado com sucesso!!!');
        $('#icone-copiar-pix').removeClass('fa-beat')
    }, function(err) {
        alert('ATENÇÃO, não foi possível copiar o PIX. Contate a Loja Virtual!');
    });
}