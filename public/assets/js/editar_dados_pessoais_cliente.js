$(document).ready(function () {
    let flagTelefone = false
    let flagCPF = false
    let telefone = $('#telefone-editar').val()
    let cpf = $('#cpf-editar').val()

    if(telefone.length == 15) flagTelefone = true
    if(cpf.length == 14) flagCPF = true

    $('#botao-salvar-dados').on('click', () => {
        let flag = true
        let teste = $('input')
        for(var i = 0; i < teste.length; i++) {
            if(teste[i].value == '') {
                alert('Preencha todos os campos!!!')
                flag = false
                break;
            }
        }
        if(flag && (!flagTelefone || !flagCPF))
            alert('Telefone ou CPF inválidos!')
        else if(flag) {
            let dados = $('#form-editar-dados').serialize()
            $.ajax({
                type: "POST",
                url: "/cliente/painel/salvar-dados",
                data: dados,
                dataType: "json",
                success: function (response) {
                    if(response == 0) $('#erro-senhas').html('<span class="text-danger">Senhas não conferem!!!<br>Digite as duas senhas iguais</span>')
                    else if(response == 1) {
                        alert('Dados atualizados com sucesso!!!')
                        window.location.assign('/cliente/painel/dados-pessoais')
                    }
                },
                error: function (error) {
                    console.log(`erro: ${error}`)
                }
            });
        }
    })

    $('#opcao-alterar-senha').on('click', () => {
        let chave = $('#opcao-alterar-senha')
        let html = ''
        if(chave[0].checked) {
            $('#campos-alterar-senhas').html(
                `<h5>Alteração de Senha</h5>
                <label>Nova Senha</label>
                <input type="password" class="form-control" name="senha">
                <label>Confirmar Senha</label>
                <input type="password" class="form-control" name="senha-confirmar">
                <div id="erro-senhas"></div>`
            )
        }
        else $('#campos-alterar-senhas').html('')
    })

    $('#telefone-editar').on('keyup', (e) => {
        let input = e.target
        input.value = mascaraTelefone(input.value)
    })

    $('#telefone-editar').on('blur', () => {
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
            $('#resposta-telefone').html('<span class="text-success">Telefone Válido!</span>')
            flagTelefone = true
        }
    })

    $('#cpf-editar').on('keyup', (e) => {
        let input = e.target
        input.value = mascaraCPF(input.value)
    })

    $('#cpf-editar').on('blur', () => {
        var strCPF = $('#cpf-editar').val()
        strCPF = strCPF.replace('.', '')
        strCPF = strCPF.replace('.', '')
        strCPF = strCPF.replace('-', '')
        var Soma = 0;
        var Resto;
        if (strCPF == "00000000000") $('#resposta-cpf').html('<span class="text-danger">CPF Inválido!</span>')
        for(i=1; i<=9; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (11 - i);
        Resto = (Soma * 10) % 11;
        if ((Resto == 10) || (Resto == 11))  Resto = 0;
        if (Resto != parseInt(strCPF.substring(9, 10))) $('#resposta-cpf').html('<span class="text-danger">CPF Inválido!</span>')
        Soma = 0;
        for(i = 1; i <= 10; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (12 - i);
        Resto = (Soma * 10) % 11;
        if ((Resto == 10) || (Resto == 11))  Resto = 0;
        if (Resto != parseInt(strCPF.substring(10, 11))) {
            $('#resposta-cpf').html('<span class="text-danger">CPF Inválido!</span>')
            flagCPF = false
        }
        else {
            let cpf = $('#cpf-editar').val()
            cpf = cpf.replace('.', '')
            cpf = cpf.replace('.', '')
            cpf = cpf.replace('-', '')
            $('#cpf-editar_').val(cpf)
            $('#resposta-cpf').html('<span class="text-success">CPF Válido!</span>')
            flagCPF = true
        }
    })
});

function mascaraTelefone(value) {
    if (!value) return ""
    value = value.replace(/\D/g,'')
    value = value.replace(/(\d{2})(\d)/,"($1) $2")
    value = value.replace(/(\d)(\d{4})$/,"$1-$2")
    return value
}

function mascaraCPF(numero){
    v = numero
    v=v.replace(/\D/g,"")
    v=v.replace(/(\d{3})(\d)/,"$1.$2")
    v=v.replace(/(\d{3})(\d)/,"$1.$2")
    v=v.replace(/(\d{3})(\d{1,2})$/,"$1-$2")
    return v
}