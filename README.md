# Projeto Loja Virtual
O Projeto Loja Virtual foi desenvolvido com objetivos de aplicar os conhecimentos adquiridos durante o período da graduação do curso Tecnólogo em Análise e Desenvolvimento de Sistemas do Centro Universitário Senac.

## Tecnologias utilizadas:
- Frontend:
   - HTML
   - CSS
   - Bootstrap (framework baseado em CSS/JavaScript para criar sites e aplicações web responsivas)
   - JavaScript
   - jQuery (biblioteca JavaScript para manipulação do DOM)
   - Fontawesome (biblioteca de ícones vetoriais baseada em CSS e LESS)
   - Maskedinput.js (biblioteca JavaScript para a criação de máscaras para campos de formulário)
- Backend:
   - PHP 8.2.12
   - Banco de Dados MySQL
----------------------------------------------------------------------------
## Conhecimentos aplicados:
- Frontend
   - Ajax
   - Consumo de APIs web externas
- Backend
   - Sessões do PHP
   - Orientação a Objetos
   - Serialização de Objetos
-----------------------------------------------------------------------------
## Padrões de programação:
   - MVC
      - Backend:
         - Camada dos Controladores (Controllers)
            - Classes responsáveis por receber/retornar os dados contidos nas requisições HTTP ao frontend
         - Camada dos Serviços (Services)
            - Classes responsáveis que contém métodos de acesso ao banco de dados relacional
         - Camada dos Modelos (Models)
            - Classes responsáveis em manipular os dados das tabelas do banco de dados
      - Frontend:
         - Camada das Views
            - Partes e fragmentos das páginas HTML que compõe o visual da Loja Virtual
-------------------------------------------------------------------------------
## Informações Técnicas:
A Loja Virtual consistem em 2 partes: Home e o Painel.
- Home: O Home consiste na página inicial da loja virtual, exibindo os produtos, e também, detalhando-os
- Painel: O Painel corresponde as interações internas e recursos específicos da loja virtual, dentre elas, a possibilidade de efetuar compras de produtos pelos usuários clientes, e, 
cadastrar produtos pelo usuário administrador.
--------------------------------------------------------------------------------
## Instruções de Uso
### Instalações
- Baixe e instale o aplicativo instalador Xampp que seja desenvolvido e compatível ao seu Sist. Operacional.
https://www.apachefriends.org/pt_br/download.html

<b>NOTA:</b> Recomendável baixar e instalar o Xampp, porque ele instala o servidor web Apache, a linguagem PHP, e o banco de dados MySQL.

- Você também pode optar por trabalhar com o programa MySQL Workbench
Baixe e instale o programa MySQL Workbench que seja desenvolvido e compatível ao seu Sist. Operacional.
https://dev.mysql.com/downloads/workbench/

NOTA: Durante a instalação do programa MySQL Workbench, você precisará criar uma conta de usuário e senha. Para sua segurança, anote o nome de usuário e senha gerada por você, pois 
será necessário definir o usuário e senha do banco de dados MySQL no arquivo de configuração da Loja Virtual.

- Executar o Banco de Dados MySQL nos Serviços do Windows:
- Abra a ferramenta Serviços no Menu Iniciar do Windows, ou digite Serviços dentro do campo Pesquisar
- Na janela que irá abrir, procure pelo serviço: "MySQL80". Clique com o botão do mouse no link: "Iniciar o serviço"
- Aguarde enquanto o Windows inicia a execução do serviço de banco de dados
- - - - - - - - - - - - - - - - - - - - - - - - - 
- Executar o Banco de Dados MySQL nos Serviços do Linux:
- Abra o terminal no Linux, e digite: sudo systemctl start mysql
- - - - - - - - - - - - - - - - - - - - - - - - -
- Encerrar o Banco de Dados MySQL nos Serviços do Windows
- Abra a ferramenta Serviços no Menu Iniciar do Windows, ou digite Serviços dentro do campo Pesquisar
- Na janela que irá abrir, procure pelo serviço: "MySQL80". Clique com o botão do mouse no link: "Parar o serviço"
- Aguarde enquanto o Windows encerra a execução do serviço de banco de dados
- - - - - - - - - - - - - - - - - - - - - - - - -
- Executar o Banco de Dados MySQL nos Serviços do Linux:
- Abra o terminal no Linux, e digite: sudo systemctl stop mysql
----------------------------------------------------------------------------------
## Criar o Banco de Dados e as Tabelas que compõem o funcionamento do Sistema:
- O Projeto da Loja Virtual armazena os dados em geral através de um sistema de banco de dados MySQL relacional
- Você deve criar o banco de dados, as tabelas inserindo alguns dados iniciais
- No Projeto loja_virtual, abra o arquivo <b>docs/Banco de Dados/loja - Tabelas.txt</b>
- Dentro do arquivo <b>loja - Tabelas.txt</b> , execute as instruções de comandos para criar o banco de dados, todas as tabelas, pré-inserções de dados em algumas tabelas e referências das 
chaves primárias com as chaves estrangeiras.
-----------------------------------------------------------------------------------
## Baixar o Projeto Loja Virtual do Git Hub
- Baixar e extraír a Loja Virtual no seu computador
-----------------------------------------------------------------------------------
## Configurar informações do Banco de Dados MySQL no arquivo de configuração do Projeto Loja Virtual
- No Projeto loja_virtual, abra o arquivo "config/Connection.php"
- Dentro do arquivo "config/Connection.php" , digite definindo o nome de usuário e senha nos atributos $usuario e $senha da Classe Connection
- o nome de usuário e a senha, deverão ser informados no formato string

Exemplos de como deve ficar a configuração:<br>
nome de usuário = admin<br>senha do usuário = admin<br><br>
$usuario = 'admin';<br>
$senha = 'admin';<hr>
nome de usuário = root<br>senha do usuário = 12345<br><br>
$usuario = 'root';<br>
$senha = '12345';<br><br>

- após a digitação nos atributos $usuario e $senha, salve o arquivo Connection.php e feche-o
------------------------------------------------------------------------------------
## Iniciar o Servidor PHP
- abrir o prompt de comando (no Windows); e o terminal (no Linux)
- navegar até a pasta onde você extraiu e colocou os arquivos e pastas da loja virtual utilizando o comando "cd"
- acessar e entrar dentro da pasta "public" utilizando o comando cd
- estando dentro da pasta public da loja virtual, digitar: <b>php -S localhost:8000</b>

### Exemplo no Windows:
- assumindo que o Projeto Loja Virtual foi baixado, extraído e colocado dentro do diretório raiz do Windows (C:\loja_virtual)
- abra o prompt de comando do Windows
- digitar o comando seguido do caminho absoluto para selecionar a pasta pública pelo prompt:  <b>cd c:\loja_virtual\public</b>  sendo mostrado no próprio prompt a esquerda:  <b>C:\loja_virtual\public</b>
- estando dentro da pasta public da loja virtual, digitar: <b>php -S localhost:8000</b>

<b>NOTA:</b> certifique-se que a porta 8000 não esteja em uso por outro programa no momento. Se porventura, a porta 8000 estiver em uso, você necessitará usar outra porta
- No Windows: para ver a lista de portas em que o sist. operacional Windows não esteja utilizando, digitar dentro do prompt de comando: <b>netstat -ano</b>
No resultado do comando mencionado, será exibido uma lista de portas em que o sist. operacional Windows está usando. As portas em que estiverem sendo usadas, a 
coluna Estado estará com o valor: <b>LISTENING</b>. As portas que não estiverem sendo utilizadas pelo sist. operacional Windows, não estarão listadas no resultado. Portanto, você poderá 
utilizar a porta que não esteja aparecendo no resultado do comando netstat -ano na execução da loja virtual.

- No Linux: para ver a lista de portas em que o sist. operacional Linux não esteja utilizando, digitar dentro do terminal: <b>ss -tuln -l</b>
O resultado desse comando exibirá uma lista de portas em que o sist. operacional Linux está usando. As portas em que estiverem sendo usadas, aparecerá o estado com o valor: <b>LISTENING</b>.
As portas que não estiverem sendo utilizadas pelo sist. operacional Linux, não estarão listadas no resultado. Portanto, você poderá utilizar a porta que não esteja aparecendo no 
resultado do comando <b>ss -tuln -l</b>  na execução da loja virtual.
-----------------------------------------------------------------------------------
## Iniciar a Loja Virtual
- após iniciar o servidor PHP pelo prompt, abra o navegador de sua preferência, e digite na url:   http://localhost:8000  e aperte a tecla enter
- será apresentada a página home da loja virtual
- para encerrar a conexão do servidor PHP no prompt de comando do Projeto Loja Virtual, você deverá pressionar uma combinação de teclas no prompt: <b>Ctrl + C</b>  , e em seguida, o servidor PHP é 
encerrado e o prompt de comando é liberado.
------------------------------------------------------------------------------------
## Informações Complementares:
<b>NOTA 1:</b> A Loja Virtual aceita 2 tipos de usuários: Administrador e Cliente. De modo que, ao acessar a aplicação web no seu navegador, você deve efetuar o 1º cadastro do usuário 
Administrador. Porque o Sistema está embasado em que o Administrador deve ser o <b>1º registro</b> a ser gravado na tabela cliente do banco de dados. À partir desse 1º registro do Administrador, 
todos os próximos cadastros a serem efetuados, serão do tipo usuário cliente.

<b>NOTA 2:</b> Inicialmente, a loja virtual não exibirá nenhum produto na página home. Para o sistema buscar algum produto, o Administrador precisa se cadastrar, e após se logar na loja virtual, 
no painel do Administrador, precisará cadastrar categorias juntamente com suas subcategorias, e cadastrar produtos a essas subcategorias (já) cadastradas.

<b>NOTA 3:</b> No Projeto loja_virtual, abra o arquivo <b>docs/Casos de Uso/Casos de Uso.pdf</b>. Esse anexo PDF contém os Casos de Uso (funcionalidades do Sistema) que os Usuários Administrador e Usuário Cliente podem executar.
