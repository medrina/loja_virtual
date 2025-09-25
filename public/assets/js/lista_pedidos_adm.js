$(document).ready(function () {

        $('#calendario').on('change', (e) => {
            let teste = $(e.target).val()
            $('#lista_pedidos').html('')
        })
        
        $('#botao-buscar-pedidos').on('click', () => {
            let data = $('#calendario').val()
            $.ajax({
                type: "GET",
                url: "/admin/pedidos",
                data: {data},
                dataType: "json",
                success: ((response) =>  {
                    if(response) {
                        let listaPedidos = ''
                        for(let i = 0; i < response['pedidos'].length; i++) {
                            let precoTotalFormatado = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(response['pedidos'][i].valor)
                            listaPedidos += `<div class="col-xl-4 col-md-6 col-12 mb-3 caixa-pedidos-adm">
                                        Pedido Nº: ${response['pedidos'][i].id_pedido} <br>
                                        Data: ${response['pedidos'][i].data_compra}<br>
                                        Cliente: ${response['pedidos'][i].cliente}<br>
                                        Email: ${response['pedidos'][i].email}<br>
                                        Status Pagamento: ${response['pedidos'][i].status_pgto}<br>
                                        Valor: ${precoTotalFormatado}
                                        <div class="d-flex">
                                            <form action="/admin/pedido" method="GET">
                                                <input type="hidden" hidden="true" name="id" value="${response['pedidos'][i].id_pedido}">
                                                <button type="submit" class="btn btn-success">Detalhar Pedido</button>
                                            </form>
                                        </div>
                                    </div>`
                        }
                        $('#lista_pedidos').html(listaPedidos)
                    }
                }),
                error: ((erro) => {
                    console.log(`erro: ${erro}`)
                })
            });
        })
        
    })