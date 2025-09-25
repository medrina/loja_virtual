$(document).ready(function () {
        
        $.ajax({
            type: "GET",
            crossDomain: true,
            url: "/categorias/list",
            contentType: 'json',
            success: function (response) {
                dados = JSON.parse(response)
                if(dados == '') $('#categorias-cadastradas').html('<h5 class="text-danger">Sem Categorias!</h5>');
                else {
                    let html = "<select id='categorias-add' class='form-control borda-preta' size='5'>"
                        dados.forEach(dados => {
                        html += `<option value='${dados.id}'>${dados.nome}</option>`
                    });
                    html += '</select>'
                    $('#categorias-cadastradas').html(html);
                }
            },
            error: (erro) => {
                console.log('erro: ', erro)
            }
        });

        $.ajax({
            type: "GET",
            crossDomain: true,
            url: "/subcategorias",
            contentType: 'json',
            success: function (response) {
                dados = JSON.parse(response)
                if(dados == 0) $('#subcategorias-cadastradas').html('<h5 class="text-danger">Sem SubCategorias!</h5>');
                else {
                    let html = "<select size='5' class='form-control borda-preta'>"
                        dados.forEach(dados => {
                        html += `<option value='${dados.id}'>${dados.nome}</option>`
                    });
                    html += '</select>'
                    $('#subcategorias-cadastradas').html(html);
                }
            },
            error: (erro) => {
                console.log('erro: ', erro)
            }
        });

    });