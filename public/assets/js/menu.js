$(function() {

    $('.submenu a').on('click', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let nome = $(this).text();
        $.ajax({
            type: 'GET',
            crossDomain: true,
            url: `/sub/${id}`,
            contentType: 'json',
            success: (dados) => {
                dados = JSON.parse(dados)
                let html = `<div class="row">`
                dados.forEach(element => {
                    let precoTotalFormatado = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(element.valor)
                    let precoParcelaFormatado = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(element.valor_parcela)
                    html += `<div class="col-xl-3 col-lg-4 col-sm-6 col-6">
                                <div class="mt-2 home-produto-caixa">
                                    <div>
                                        <a href="/produto/${element.id}" title="${element.nome}"><img src="./assets/img/${element.imagem}" height="100%" width="100%"></a>
                                    </div>
                                    <div class="home-produto-nome">
                                        ${element.nome}
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
                    $('#subcategoria-nome').html(`<h1 class="text-center">${nome}</h1>`)
                    $('#tabela-produtos').html('<span class="text-danger">Não há Produtos Cadastrados nessa Subcategoria!</span>')
                }
                else {
                        $('#subcategoria-nome').html(`<h1 class="text-center">${nome}</h1>`)
                    $('#mostrar-produtos').html(html)
                }
                $.ajax({
                    type: "GET",
                    crossDomain: true,
                    url: `/marcas-lista`,
                    data: {id},
                    dataType: 'json',
                    success: (response) => {
                        let html = '<h5>Marcas</h5>';
                        response.forEach(element => {
                            html += `<div><input id="marca-${element.id}" type="checkbox" onclick="marca(${element.id}, ${id})"> ${element.nome}</div>`
                        });
                        $('#marcas').html(html)
                    },
                    error: (erro) => {
                        console.log(`erro: ${erro}`)
                    }
                });
                $.ajax({
                    type: "GET",
                    crossDomain: true,
                    url: `/produtos/cores`,
                    data: {id},
                    dataType: 'json',
                    success: (response) => {
                        $('#filtros-valor').html(`<div class="row">
                                                    <div class="col-12">
                                                        <div class="float-end">
                                                            <select id="filtros-valores" name="filtros-valor">
                                                                <option value="0">Selecione</option>
                                                                <option value="2-${id}">Maior Preço</option>
                                                                <option value="1-${id}">Menor Preço</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>`)
                    },
                    error: (erro) => {
                        console.log(`erro: ${erro}`)
                    }
                });
            },
            error: (erro) => {
                console.log('erro: ', erro)
            }
        })
    });
});