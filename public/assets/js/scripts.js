$(document).ready(function () {
    let id = 0
    
    $('#categoria').on('change', (e) => {
        id = $(e.target).val()
        if(id == 0) {
            alert('Por favor, selecione uma Categoria')
            $('#subcategoria').html('<option>--------</option>')
            $('#tabela-produtos').html('')
        }
        else {
            id = $('#categoria').val()
            $.ajax({
                type: 'GET',
                crossDomain: true,
                url: `/catsub/${id}`,
                contentType: 'json',
                success: (dados) => {
                    dados = JSON.parse(dados)
                    let html = "<option value='0'>--- Selecione ---</option>"
                    dados.forEach(element => {
                        html += `<option value='${element.id}'>${element.nome}</option>`
                    });
                    $('#subcategoria').html(html)
                    $('#tabela-produtos').html('')
                },
                error: (erro) => {
                    console.log('erro: ', erro)
                }
            })
        }
    })

    $('#subcategoria').on('change', (e) => {
        id = $(e.target).val()
        $.ajax({
            type: 'GET',
            crossDomain: true,
            url: `/sub/${id}`,
            contentType: 'json',
            success: (dados) => {
                dados = JSON.parse(dados)
                let html = '<div class="row">'
                dados.forEach(element => {
                    html += `<div class="col-xl-3 col-lg-4 col-sm-6" style="border: 1p solid;">
                                <div class="mt-2" style="padding: 2px; border: 1px solid blue;">
                                    <div class="" style="height: 120px;">
                                        <a href="/produto/${element.id}" title="${element.nome}" target="_blank"><img src="./assets/img/${element.imagem}" height="100%" width="100%"></a>
                                    </div>
                                    <div style="font-size: 14px;">
                                        ${element.nome}
                                    </div>
                                    <div style="font-size: 12px;">
                                        ${element.marca}
                                    </div>
                                    <div style="font-size: 12px;">
                                        À Vista: R$ ${element.valor}
                                    </div>
                                    <div style="font-size: 10px;">
                                        <span>À Prazo: ${element.nro_parcelas} X R$ ${element.valor_parcela}</span>
                                        <span class=""><a href="/produto/${element.id}" class="btn btn-primary" style="font-size: 10px;" target="_blank">Ver Produto</a></span>
                                    </div>
                                </div>
                            </div>`
                });
                html += '</row>'
                if(html == '<div class="row"></row>') $('#tabela-produtos').html('<span class="text-danger">Não há Produtos Cadastrados nessa Subcategoria!</span>')
                else $('#tabela-produtos').html(html)
            },
            error: (erro) => {
                console.log('erro: ', erro)
            }
        })
    })

    $('#botao-add-carrinho').on('click', () => {
        if($('#link-logoff').val() == 'ola') {
            let dados = $('#form-adicionar-produto').serialize()
            $.ajax({
                type: "POST",
                data: dados,
                url: `/produtos/add`,
                dataType: "json",
                success: function (response) {
                    if(response) {
                        alert('Produto foi adicionado ao carrinho!');
                    }
                },
                error: (erro) => {
                    console.log(`erro: ${erro}`)
                }
            });
        }
        else {
            $('#modalSignin').css('display', 'block').css('position', 'absolute').css('top', '100px').css('left', '50px')
            $('#resp-modal').html('')
        }
    })

    $('#fechar-modal-login').on('click', () => {
        $('#modalSignin').css('display', 'none')
    })

    $('#botao-login-modal').on('click', (e) => {
        let dados = $('#form-modal').serialize()
        $.ajax({
            type: "POST",
            data: dados,
            url: "/login/validar-modal",
            dataType: 'json',
            error: (erro) => {
                if(erro.responseText == 'erro') {
                    $('#resp-modal').html('<div class="bg-danger-subtle text-danger-emphasis p-3 mt-3">ATENÇÃO: e-mail ou senha inválidos!!! Por favor, informe o seu login e senha corretos!</div>')
                }
                else {
                    $('#resp-modal').html('')
                    $('#modalSignin').css('display', 'none')
                    window.location.reload()
                }
            }
        });
    })

    $('#botao-deletar-produto').on('onkeyup', () => {
        let url = $('#deletar-produto').val()
    })
    
    $('#cep').on('keyup', (e) => {
        let cep = $('#cep').val()
        if(cep.length == 8) {
            $('#msg-erro').html('<span class="text-secondary">Aguarde... <i class="fa-solid fa-spinner fa-spin-pulse spinner-cep"></i></span>')
            $.ajax({
                type: "GET",
                url: `https://viacep.com.br/ws/${cep}/json/`,
                dataType: "json",
                success: function (dados) {
                    if(!dados.erro) {
                        $('#rua').val(dados.logradouro)
                        $('#bairro').val(dados.bairro)
                        $('#cidade').val(dados.localidade)
                        $('#uf').val(dados.uf)
                        $('#msg-erro').html('')
                    }
                    else {
                        $('#msg-erro').html('<span class="text-danger">CEP Inválido!</span>')
                        $('#rua').val('')
                        $('#bairro').val('')
                        $('#cidade').val('')
                        $('#uf').val('')
                    }
                },
                error: function (erro) {
                    $('#msg-erro').html('<span class="text-danger">Erro de Conexão!</span>')
                 }
            });
        }
    })

    $('#botao-adicionar-categoria').on('click', () => {
        let nome = $('#cat_nome')
        if(!nome.val()) {
            alert('Por favor, digite uma Categoria!')
        }    
        else {
            let dados = $('#form-categoria').serialize()
            $.ajax({
                type: "POST",
                data: dados,
                url: `/categoria/add`,
                dataType: "json",
                success: function (response) {
                    if(response == 1) {
                        alert('ATENÇÃO! Essa Categoria já foi cadastrada!')
                    }
                    else if(response == 2) {
                        alert('Categoria cadastrada com sucesso!!!')
                        window.location.reload()
                    }
                    else alert('Não foi possível adicionar a Categoria!')
                },
                error: function (error) {
                    console.log(`error: ${error}`)
                }
            });
        }
    })

    $('#botao-adicionar-subcategoria').on('click', () => {
        let categoria_add = $('#categorias-add').val()
        let nome = $('#sub_nome')
        if(!nome.val()) {
            alert('Por favor, digite uma Subcategoria!')
        }
        else if(categoria_add == null) {
            alert('Por favor, selecione uma Categoria Cadastrada!')
        }
        else {
            let nome = $('#sub_nome')
            $.ajax({
                type: "POST",
                data: {nome: nome.val(), categoria: categoria_add},
                url: `/subcategoria/add`,
                dataType: "json",
                success: function (response) {
                    if(response == 1) {
                        alert('ATENÇÃO! Essa Subcategoria já foi cadastrada!')
                    }
                    else if(response == 2) {
                        alert('Subcategoria cadastrada com sucesso!')
                        window.location.reload()                        
                    }
                    else alert('Não foi possível adicionar a Subcategoria!')
                },
                error: function (error) {
                    console.log(`error: ${error}`)
                }
            });
        }
    })

    $('#botao-salvar-dados-admin').on('click', () => {
        let flag = true
        let teste = $('input')
        for(var i = 0; i < teste.length; i++) {
            if(teste[i].value == '') {
                alert('Preencha todos os campos!!!')
                flag = false
                $('#erro-senhas-admin').html('')
                break;
            }
        }
        if(flag) {
            let dados = $('#form-editar-dados-admin').serialize()
            $.ajax({
                type: "POST",
                url: "/admin/salvar-dados",
                data: dados,
                dataType: "json",
                success: function (response) {
                    if(response == 0) $('#erro-senhas-admin').html('<span class="text-danger">Senhas não conferem!!!<br>Digite as duas senhas iguais</span>')
                    else if(response == 1) {
                        alert('Dados Atualizados com Sucesso!!!')
                        window.location.assign('/cliente/painel')
                    }
                },
                error: function (error) {
                    console.log(`erro: ${error}`)
                }
            });
        }
    })

    $('#botao-enviar-editar-dados').on('click', () => {
        let flag = true
        let camposObrigatorios = $('.campo-obrigatorio')
        for(var i = 0; i < camposObrigatorios.length; i++) {
            if(camposObrigatorios[i].value == '') {
                alert('Preencha todos os campos obrigatórios!!!')
                flag = false
                break
            }
        }
        if(flag) {
            let dados = $('#form-editar-endereco').serialize()
            $.ajax({
                type: "POST",
                url: "/cliente/painel/editar-endereco",
                data: dados,
                dataType: "json",
                success: function (response) {
                    if(response == 0) {
                        $('#msg-atualizar-endereco').html('<div class="bg-danger-subtle text-danger-emphasis p-3 mb-3 text-center">Esse Endereço já existe!</div>')
                    }
                    else {
                        $('#msg-atualizar-endereco').html('<div class="bg-success-subtle text-success-emphasis p-3 mb-3 text-center">Endereço Atualizado com Sucesso!!!</span></div>')
                    }
                },
                error: function (error) {
                    console.log(`erro: ${error}`)
                }
            });
        }
    })

    $('#opcao-alterar-senha-admin').on('click', () => {
        let chave = $('#opcao-alterar-senha-admin')
        if(chave[0].checked) {
            $('#campos-alterar-senhas-admin').html(
                `<h5>Alteração de Senha</h5>
                <label>Nova Senha</label>
                <input type="password" class="form-control" name="senha-alterar-admin">
                <label>Confirmar Senha</label>
                <input type="password" class="form-control" name="senha-confirmar-admin">
                <div id="erro-senhas-admin"></div>`
            )
        }
        else $('#campos-alterar-senhas-admin').html('')
    })
        
});

function removerProduto(id) {
    $.ajax({
        type: "POST",
        data: {id: id},
        url: `/remover`,
        dataType: "json",
        success: function (response) {
            window.location.reload()
        },
        error: function (error) {
            window.location.reload()
        }
    });
}

function diminuirQuantidade(prod, car) {
    var quant = ($(`#quantidade-${prod}`).val()) - 1
    var total = $('#valor-total-php').val()
    var valor_unitario = $(`#valor-unitario-php-${prod}`).val()
    if(quant == 0) {
        alert('Atenção, não é permitido diminuir a quantidade')
        quant = 1
    }
    else {
        $.ajax({
            type: "GET",
            url: "/cliente/produto/alterar-quant",
            data: {quant, prod, car},
            dataType: "json",
            success: function (response) {
                alert('Item diminuído com sucesso!')
                let novoValorTotalProduto = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(response.total)
                $(`#valor-total-${prod}`).html(novoValorTotalProduto)
                $(`#quantidade-${prod}`).val(response.quantidade)
                let calculo = total - valor_unitario
                let valorFormatado = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(calculo)
                $('#valor-total-carrinho').html(valorFormatado)
                $('#valor-total-php').val(calculo)
                $('#vlr-total').val(calculo)
            },
            error: function (error) {
                console.log(`erro: ${error}`)
            }
        });
    }
}

function aumentarQuantidade(prod, car) {
    var quant = parseInt($(`#quantidade-${prod}`).val())
    quant += 1
    var total = parseFloat($('#valor-total-php').val())
    var valor_unitario = parseFloat($(`#valor-unitario-php-${prod}`).val())
    $.ajax({
        type: "GET",
        url: "/cliente/produto/alterar-quant",
        data: {quant, prod, car},
        dataType: "json",
        success: function (response) {
            alert('Item aumentado com sucesso!')
            let novoValorTotalProduto = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(response.total)
            $(`#valor-total-${prod}`).html(novoValorTotalProduto)
            $(`#quantidade-${prod}`).val(response.quantidade)
            let calculo = 0
            calculo = total + valor_unitario
            let valorFormatado = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(calculo)
            $('#valor-total-carrinho').html(valorFormatado)
            $('#valor-total-php').val(calculo)
            $('#vlr-total').val(calculo)
        },
        error: function (error) {
            console.log(`erro: ${error}`)
        }
    });
}

function apagarEndereco(id) {
    const resp = confirm('Tem certeza que deseja apagar esse endereço?')
    if(resp) {
        $.ajax({
            type: "POST",
            url: `/cliente/painel/apagar-endereco`,
            data: {id},
            dataType: "json",
            success: function (response) {
                if(response) $(`#endereco-id-${id}`).html('')
            },
            error: function (error) { 
                console.log(`erro: ${error}`)
             }
        });
    }
}