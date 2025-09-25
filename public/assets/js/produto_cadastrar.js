$(document).ready(function() {

        let valor = 0

        $.ajax({
            type: "GET",
            url: "/subcategorias",
            dataType: "json",
            success: function (response) {
                if(response.length == 0) $('#subcat-cadastradas').html('<b class="text-danger">Sem Subcategorias Cadastradas</b>')
                else {
                    let html = '<select id="subcat-selecionada" class="form-control" name="subcat-selecionada"><option value="0">--- Selecione ---</option>'
                    response.forEach(element => {
                        html += `<option value="${element.id}">${element.nome}</option>`
                    });
                    html += '</select>'
                    $('#subcat-cadastradas').html(html)
                }
            },
            error: function (error) {
                console.log(`erro: ${error}`)
            }
        });

        $.ajax({
            type: "GET",
            url: "/marcas",
            dataType: "json",
            success: function (response) {
                if(response.length == 0) $('#marcas-cadastradas').html('<b class="text-danger">Sem Marcas Cadastradas!</b>')
                else {
                    let html = '<select id="marca-selecionada" class="form-control" name="marca-selecionada"><option value="0">--- Selecione ---</option>'
                    response.forEach(element => {
                        html += `<option value="${element.id}">${element.nome}</option>`
                    });
                    html += '</select>'
                    $('#marcas-cadastradas').html(html)
                }
            },
            error: function (error) {
                console.log(`erro: ${error}`)
            }
        });

        $('#botao-calcular-parcelas').on('click', (e) => {
            if($('#produto_valor_').val() == '' || $('#produto_nro_parcelas').val() == '') {
                alert('Digite o preço do produto, e informe a quantidade de parcelas')
                $('#valor_parcela').html('<b>R$ 0,00</b>')
            }
            else {
                if($('#produto_nro_parcelas').val() == 0) {
                    $('#valor_parcela').html('<b>R$ 0,00</b>')
                }
                else {
                    let preco = $('#produto_valor_').val()
                    preco = preco.replace('.', '').replace(',', '.')
                    this.valor = parseFloat(preco)
                    $('#produto_valor').val(this.valor)
                    let nro_parcelas = ($('#produto_nro_parcelas').val())
                    let calculo = preco / nro_parcelas
                    let valorParcela = Math.floor(calculo * 100) / 100
                    let resultadoComVirgula = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(valorParcela)
                    valorParcela = valorParcela.toString()
                    valorParcela = valorParcela.replace(',', '.')
                    let html = `<b> ${resultadoComVirgula}</b><input type="text" name="produto_valor_parcela" value="${valorParcela}" hidden="true">`
                    $('#valor_parcela').html(html)
                    $('#valor_parcela_erro').html('')
                }
            }
        })

        $('#botao-cadastrar-nova-marca').on('click', () => {
            let marca = $('#cadastrar-nova-marca')
            if(!marca.val()) {
                $('#cadastrar-nova-marca').css('border', '2px solid red')
                $('#produto_erro_marca').html('<span class="text-danger">Digite a Marca</span>')
                alert('Preencha a nova Marca!')
            }
            else {
                $.ajax({
                    type: "POST",
                    url: "/marca/cadastro",
                    data: {marca: marca.val()},
                    dataType: "json",
                    success: function (response) {
                        if(response == 1) {
                            alert('ATENÇÃO! Essa Marca já foi cadastrada!')
                        }
                        else if(response == 2) {
                            alert('Marca foi cadastrada com sucesso!!!')
                            $.ajax({
                                type: "GET",
                                url: "/marcas",
                                dataType: "json",
                                success: function (response) {
                                    let html = '<select id="marca-selecionada" class="form-control" name="marca-selecionada"><option value="0">--- Selecione ---</option>'
                                    response.forEach(element => {
                                        html += `<option value="${element.id}">${element.nome}</option>`
                                    });
                                    html += '</select>'
                                    $('#marcas-cadastradas').html(html)
                                    $('#cadastrar-nova-marca').css('border', '1px solid #D3D3D3')
                                    $('#produto_erro_marca').html('')
                                },
                                error: function (error) {
                                    console.log(`erro: ${error}`)
                                }
                            });
                        }
                    },
                    error: function (error) {
                        console.log(`erro: ${error}`)
                    }
                });
            }
        })
        
        $('#meuFormulario').on('submit', function(e) {
            e.preventDefault();
            let flagNome = true
            let flagPreco = true
            let flagValorParcela = true
            let flagNroParcelas = true
            let flagSubcategoria = true
            let flagMarca = true
            let flagImagem = true
            let flagAltura = true
            let flagLargura = true
            let flagComprimento = true
            let flagPeso = true
            if($('#produto_nome').val() === '') {
                $('#produto_nome').css('border', '2px solid #FF0000')
                $('#produto_erro_nome').html('<span class="text-danger">Preencha o Nome</span>')
                flagNome = false
            }
            else {
                $('#produto_nome').css('border', '1px solid #D3D3D3')
                $('#produto_erro_nome').html('')
                flagNome = true
            }
            if($('#produto_valor_').val() === '') {
                $('#produto_valor_').css('border', '1px solid #FF0000')
                $('#produto_erro_valor').html('<span class="text-danger">Preço ?</span>')
                flagPreco = false;
            }
            else {
                $('#produto_valor_').css('border', '1px solid #D3D3D3')
                $('#produto_erro_valor').html('')
                flagPreco = true;
            }
            if($('#produto_nro_parcelas').val() === '') {
                $('#produto_nro_parcelas').css('border', '1px solid #FF0000')
                $('#produto_erro_nro_parcelas').html('<span class="text-danger">Nº Parcelas ?</span>')
                flagNroParcelas = false
            }
            else {
                $('#produto_nro_parcelas').css('border', '1px solid #D3D3D3')
                $('#produto_erro_nro_parcelas').html('')
                flagNroParcelas = true
            }
            if($('#subcat-selecionada').val() == 0) {
                $('#subcat-selecionada').css('border', '3px solid #FF0000')
                $('#produto_erro_subcat').html('<span class="text-danger">Selecione a Subcategoria!</span>')
                flagSubcategoria = false
            }
            else {
                $('#subcat-selecionada').css('border', '1px solid #D3D3D3')
                $('#produto_erro_subcat').html('')
                flagSubcategoria = true
            }
            if($('#marca-selecionada').val() == 0) {
                $('#marca-selecionada').css('border', '3px solid #FF0000')
                $('#produto_erro_marca_selecionada').html('<span class="text-danger">Selecione a Marca!</span>')
                flagMarca = false
            }
            else {
                $('#marca-selecionada').css('border', '1px solid #D3D3D3')
                $('#produto_erro_marca_selecionada').html('')
                flagMarca = true
            }
            if($('#produto_altura').val() == '') {
                $('#produto_altura').css('border', '1px solid #FF0000')
                $('#produto_erro_altura').html('<span class="text-danger">Altura ?</span>')
                flagAltura = false
            }
            else {
                $('#produto_altura').css('border', '1px solid #D3D3D3')
                $('#produto_erro_altura').html('')
                flagAltura = true
            }
            if($('#produto_largura').val() == '') {
                $('#produto_largura').css('border', '1px solid #FF0000')
                $('#produto_erro_largura').html('<span class="text-danger">Largura ?</span>')
                flagAltura = false
            }
            else {
                $('#produto_largura').css('border', '1px solid #D3D3D3')
                $('#produto_erro_largura').html('')
                flagLargura = true
            }
            if($('#produto_comprimento').val() == '') {
                $('#produto_comprimento').css('border', '1px solid #FF0000')
                $('#produto_erro_comprimento').html('<span class="text-danger">Comprimento ?</span>')
                flagComprimento = false
            }
            else {
                $('#produto_comprimento').css('border', '1px solid #D3D3D3')
                $('#produto_erro_comprimento').html('')
                flagComprimento = true
            }
            if($('#produto_peso').val() == '') {
                $('#produto_peso').css('border', '1px solid #FF0000')
                $('#produto_erro_peso').html('<span class="text-danger">Peso ?</span>')
                flagPeso = false
            }
            else {
                $('#produto_peso').css('border', '1px solid #D3D3D3')
                $('#produto_erro_peso').html('')
                flagPeso = true
            }
            if($('#valor_parcela').is(':empty') || $('#valor_parcela').text().length == 7) {
                $('#valor_parcela_erro').html('<span class="text-danger">?</span> ')
                flagValorParcela = false
            }
            else {
                $('#valor_parcela_erro').html('')
                flagValorParcela = true
            }
            if(!flagNome || 
                !flagPreco || 
                !flagSubcategoria || 
                !flagMarca || 
                !flagAltura || 
                !flagLargura || 
                !flagComprimento ||
                !flagValorParcela ||
                !flagPeso) { alert('Atenção: Preencha os campos obrigatórios!')}
            else {
                let formData = new FormData(this);
                $.ajax({
                    url: '/prod/cad',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(resposta) {
                        if(resposta == 1) {
                            alert('SUCESSO: O Cadastro foi salvo com sucesso')
                            window.location.reload()
                        }
                        else if(resposta == 3) alert('ATENÇÃO: Envie arquivos somente do tipo imagem\nTente Novamente')
                        else if(resposta == 4) alert('ERRO: Não há arquivos selecionado\nPor favor, escolha uma imagem')
                        else alert('ERRO: Não foi possível realizar o Cadastro desse Produto\nTente Novamente')
                    },
                    error: function(error) {
                        console.log(`erro: ${error}`)
                    }
                });
            }
        });

        $('#produto_valor_').on('keyup', (e) => { 
            let valorAlterado = e.target.value
            valorAlterado = valorAlterado.replace(/\D/g, "");
            valorAlterado = valorAlterado.replace(/(\d+)(\d{2})$/, "$1,$2");
            valorAlterado = valorAlterado.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
            $('#produto_valor_').val(valorAlterado)
        });

    })

    function mascaraValorComVirgula(nome) {
        let valorAlterado = $(`#produto_${nome}`).val()
        valorAlterado = valorAlterado.replace(/\D/g, "");
        valorAlterado = valorAlterado.replace(/(\d+)(\d{2})$/, "$1,$2");
        let valorNumerico = valorAlterado
        valorNumerico = valorNumerico.replace(',', '.')
        $(`#produto_${nome}`).val(valorAlterado)
        $(`#produto_${nome}_valor`).val(valorNumerico)
    }