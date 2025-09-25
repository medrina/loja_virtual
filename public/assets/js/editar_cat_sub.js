function editCategoria(id, nome) {
    $('#campo-editar-categoria').html(`<form id="form-editar-categoria">
                                            <input id="campo_categoria" type="text" class="form-control borda-preta" name="nome_categoria" value="${nome}">
                                            <input type="hidden" hidden="true" name="id_categoria" value="${id}">
                                            <button id="botao-atualizar-categoria" type="button" class="btn btn-primary mt-3" onclick="atualizarCategoria()">Atualizar Categoria</button>
                                        </form>`)
}

function editSubCategoria(id, nome) {
    $('#campo-editar-subcategoria').html(`<form id="form-editar-subcategoria">
                                                <input id="campo_subcategoria" type="text" class="form-control borda-preta" name="nome_subcategoria" value="${nome}">
                                                <input type="hidden" hidden="true" name="id_subcategoria" value="${id}">
                                                <button type="button" class="btn btn-primary mt-3" onclick="atualizarSubCategoria()">Atualizar Subcategoria</button>
                                            </form>`)
}

function atualizarCategoria() {
    if($('#campo_categoria').val() == '') alert('Preencha o campo Categoria')
    else {
        let dados = $('#form-editar-categoria').serialize()
        $.ajax({
            type: "POST",
            url: "/cat_sub/cat/update",
            data: dados,
            dataType: "json",
            success: (response) => {
                if(response == 1) {
                    alert('A Categoria foi alterada com sucesso!')
                    $.ajax({
                        type: "GET",
                        crossDomain: true,
                        url: "/categorias/list",
                        contentType: 'json',
                        success: function (dados) {
                            dados = JSON.parse(dados)
                            if(dados == '') $('#categorias-cadastradas-admin').html('<h5 class="text-danger">Sem Categorias!</h5>');
                            else {
                                let html = "<select id='categoria-atualizar-admin' class='form-control borda-preta' size='5'>"
                                    dados.forEach(dados => {
                                        html += `<option value='${dados.id}' onclick="editCategoria('${dados.id}', '${dados.nome}')">${dados.nome}</option>`
                                });
                                html += '</select>'
                                $('#categorias-cadastradas-admin').html(html);
                            }
                        },
                        error: (erro) => {
                            console.log('erro: ', erro)
                        }
                    });
                }
                else {
                    alert('Não foi possível atualizar essa Categoria!')
                }
            },
            error: (error) => {
                console.log(`erro: ${error}`)
            }
        });
    }
}

function atualizarSubCategoria() {
    if($('#campo_subcategoria').val() == '') alert('Preencha o campo Subcategoria')
    else {
        let dados = $('#form-editar-subcategoria').serialize()
        $.ajax({
            type: "POST",
            url: "/cat_sub/sub/update",
            data: dados,
            dataType: "json",
            success: (response) => {
                if(response == 1) {
                    alert('A Subcategoria foi alterada com sucesso!')
                    $.ajax({
                        type: "GET",
                        crossDomain: true,
                        url: "/subcategorias",
                        contentType: 'json',
                        success: function (dados) {
                            dados = JSON.parse(dados)
                            if(dados == '') $('#subcategorias-cadastradas-admin').html('<h5 class="text-danger">Sem Categorias!</h5>');
                            else {
                                let html = "<select id='subcategoria-atualizar-admin' class='form-control borda-preta' size='5'>"
                                    dados.forEach(dados => {
                                        html += `<option value='${dados.id}' onclick="editSubCategoria('${dados.id}', '${dados.nome}')">${dados.nome}</option>`
                                });
                                html += '</select>'
                                $('#subcategorias-cadastradas-admin').html(html);
                            }
                        },
                        error: (erro) => {
                            console.log('erro: ', erro)
                        }
                    });
                }
                else {
                    alert('Não foi possível atualizar essa Categoria!')
                }
            },
            error: (error) => {
                console.log(`erro: ${error}`)
            }
        });
    }
}