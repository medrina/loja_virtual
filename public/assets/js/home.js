$(document).ready(function () {
            $('#filtros-valor').on('click', (e) => {
                if(e.target.value != 0) {
                    let dados = e.target.value.split('-')
                    let opcao = dados[0]
                    let subcategoria = dados[1]
                    $.ajax({
                        type: "GET",
                        crossDomain: true,
                        url: "/prod/preco",
                        data: {opcao, subcategoria},
                        contentType: "json",
                        success: (dados) => {
                            dados = JSON.parse(dados)
                            let html = `<div class="row">`
                            dados.forEach(element => {
                                let precoTotalFormatado = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(element.valor)
                                let precoParcelaFormatado = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(element.valor_parcela)
                                html += `<div class="col-xl-3 col-lg-4 col-sm-6 col-6">
                                        <div class="mt-2 home-produto-caixa">
                                            <div>
                                                <a href="/produto/${element.id}" title="${element.produto}"><img src="./assets/img/${element.imagem}" height="100%" width="100%"></a>
                                            </div>
                                            <div class="home-produto-nome">
                                                ${element.produto}
                                            </div>
                                            <div class="home-produto-marca">
                                                ${element.marca}
                                            </div>
                                            <div class="home-produto-preco">
                                                À Vista: ${precoTotalFormatado}
                                            </div>
                                            <div class="home-produto-condicoes">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <span>À Prazo: ${element.nro_parcelas} X ${precoParcelaFormatado}</span>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="mt-3"><a href="/produto/${element.id}" class="btn btn-primary home-produto-botao-VerProduto">Ver Produto</a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`
                            });
                            html += '</row>'
                            if(html == '<div class="row"></row>') {
                                $('#mostrar-produtos').html('<span class="text-danger">Não há Produtos Cadastrados nessa Subcategoria!</span>')
                            }
                            else {
                                $('#mostrar-produtos').html(html)
                            }
                        },
                        error: (erro) => {
                            console.log(`erro: ${erro}`)
                        }
                    });
                }
            })
        });

        function marca(id, id_subcategoria, nome) {
            $.ajax({
                type: "GET",
                crossDomain: true,
                url: `/marca/produtos`,
                data: {id, id_subcategoria},
                contentType: "json",
                success: (dados) => {
                    dados = JSON.parse(dados)
                    let html = `<div class="row">`
                    dados.forEach(element => {
                        let precoTotalFormatado = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(element.valor)
                        let precoParcelaFormatado = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(element.valor_parcela)
                        html += `<div class="col-xl-3 col-lg-4 col-sm-6 col-6">
                                    Marca: ${element.marca}
                                    <div class="mt-2 home-produto-caixa">
                                        <div>
                                            <a href="/produto/${element.id_produto}" title="${element.produto}"><img src="./assets/img/${element.imagem}" height="100%" width="100%"></a>
                                        </div>
                                        <div class="home-produto-nome">
                                            ${element.produto}
                                        </div>
                                        <div class="home-produto-marca">
                                            ${element.marca}
                                        </div>
                                        <div class="home-produto-preco">
                                            À Vista: ${precoTotalFormatado}
                                        </div>
                                        <div class="home-produto-condicoes">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <span>À Prazo: ${element.nro_parcelas} X ${precoParcelaFormatado}</span>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mt-3"><a href="/produto/${element.id_produto}" class="btn btn-primary home-produto-botao-VerProduto">Ver Produto</a></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>`
                        });
                        html += '</row>'
                        if(html == '<div class="row"></row>') $('#mostrar-produtos').html('<span class="text-danger">Não há Produtos Cadastrados nessa Subcategoria!</span>')
                        else $('#mostrar-produtos').html(html)
                        setTimeout(() => {
                            $('input').prop('checked', false)
                        },300)
                },
                error: (erro) => {
                    console.log(`erro: ${erro}`)
                }
            });
        }