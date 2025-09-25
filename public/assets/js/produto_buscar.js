$(document).ready(function () {
        $('#categoria-admin').on('change', (e) => {
        id = $(e.target).val()
        if(id == 0) {
            alert('Por favor, selecione uma Categoria')
            $('#subcategoria-admin').html('<option>--------</option>')
            $('#lista-produtos').html('')
        }
        else {
            id = $('#categoria-admin').val()
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
                    $('#subcategoria-admin').html(html)
                    $('#lista-produtos').html('')
                },
                error: (erro) => {
                    console.log('erro: ', erro)
                }
            })
        }
    })

    $('#subcategoria-admin').on('change', (e) => {
        id = $(e.target).val()
        $.ajax({
            type: 'GET',
            crossDomain: true,
            url: `/sub/admin/${id}`,
            contentType: 'json',
            success: (dados) => {
                dados = JSON.parse(dados)
                let html = '<div class="row">'
                dados.forEach(element => {
                    html += `<div class="col-xl-3 col-lg-4 col-sm-6">
                                <div class="mt-2 home-produto-caixa borda-azul">
                                    <div class="bg-warning altura-img-buscar-produtos">
                                        <a href="/prod/edt/${element.id}" title="${element.nome}"><img src="./../assets/img/${element.imagem}" height="100%" width="100%"></a>
                                    </div>
                                    <div clas="home-produto-nome">
                                        ${element.nome}
                                    </div>
                                    <div class="home-produto-marca">
                                        ${element.marca}
                                    </div>
                                    <div class="home-produto-preco">
                                        À Vista: R$ ${element.valor}
                                    </div>
                                    <div class="home-produto-condicoes">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span>À Prazo: ${element.nro_parcelas} X R$ ${element.valor_parcela}</span>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="mt-3"><a href="/prod/edt/${element.id}" class="btn btn-primary home-produto-botao-VerProduto">Editar Produto</a></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`
                });
                html += '</row>'
                if(html == '<div class="row"></row>') $('#lista-produtos').html('<span class="text-danger">Não há Produtos Cadastrados nessa Subcategoria!</span>')
                else $('#lista-produtos').html(html)
            },
            error: (erro) => {
                console.log('erro: ', erro)
            }
        })
    })
    });