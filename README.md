<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# About GoodShoes

## Introdução:

Senhores, esse foi um grande desafio para mim, foi o meu primeiro contato desenvolvendo com o Laravel. De acordo os conceitos do framework foram se encaixando na minha cabeça, fui entendendo o porque dizem que esse framework é mágico. Não precisei relar em uma linha de SQL para moldar o banco, tudo é feito em PHP, isso é incrível.

Antes de começar o projeto eu montei um diagrama de entidade relacionamento, acrescentei algumas entidades que achava importante para trazer as formas normais para o projeto, acredito que isso deixe o sistema mais harmonico e profissional. A imagem do diagrama está no arquivo "Diagrama_Entidade_Relacionamento.png".

Não consegui finalizar o projeto 100%, mas acredito que evolui bastante. Parti do zero, comecei pela instalação do PHP, depois instalei o Composer, criei um diretório para o projeto e dentro dele instalei o Laravel.

Consegui compreender os conceitos das migrations, models, controllers, routes e seeds dentro da ferramenta. Acredito que uma boa parte desse desafio foi realizada, sinto que consegui ir até 85% dele.

Cheguei a iniciar a parte de pedidos, a parte de listar pedidos e cadastrar um novo pedido funciona, mas não consegui finalizar o update e o delete, confesso que a ralação ManyToMany com tabela intermediária de 'item_pedido' foi um pouco difícil de criar, com mais um pouco de prazo eu creio que conseguiria concluir esse controller específico.

Estou mandando também o projeto que criei dentro do Postman para fazer os testes de backend, funcionou corretamente: registro de usuários, login, crud de vendedor, crud de cliente, crud de produto, crud de lote, create e reload de pedido.

Gostei e me dediquei bastante para trabalhar nesse desafio, escolhi a parte de backend justamente pela curva de aprendizagem do framework. Os primeiros contatos com ferramentas novas sempre são desafiadores.

Sobre o readme.md, não consegui formular da maneira mais completa, também por contra do prazo, alguns comandos de instalação ficaram faltando.

Enfim, espero que achem adequado o meu desempenho.

-------------------

## Passo a passo para instalação do GoodShoes:

1) Instalação e configuração do PHP:

a) Baixe e extraia o PHP através do link: https://windows.php.net/downloads/releases/php-8.1.0-nts-Win32-vs16-x64.zip dentro de (C:), criando uma pasta chamada "Php".

b) Acesse "Editar as variáveis de ambiente do sistema" através da barra de pesquisa do windows, clique em "Variáveis de Ambiente". Em "Variáveis do sistema" encontre a variável "Path", clique sobre ela e depois clique em "Editar". Em seguida clique em "Novo". Adicione o caminho para a pasta do PHP que deve ser "C:/Php" caso tenha seguido o passo anterior.

c) Edite o "php.ini" arquivo dentro "C:/Php" do tipo "Parâmetro de configuração", removendo o ";" das linhas: "extension=fileinfo" e "extension=pdo_sqlite".

-------------------

2) Instalação e configuração do Composer:

a) Baixe e instale o Composer através do link: https://getcomposer.org/Composer-Setup.exe

-------------------

3) Criei o projeto através do Composer, pela falta de tempo, não consegui levantar a sequência de comandos até a finalização da instalação do LARAVEL.

-------------------

4) O projeto também utiliza o Passport Package, instalei ele através do comando no CMD forçando:

a) composer require laravel/passport 

b) php artisan passport:install --force

-------------------

5) Crie as migrations com o comando abaixo no cmd:

a) php artisan migrate

-------------------

6) Ative as seeds criadas com o comando abaixo no cmd:

a) php artisan db:seed

-------------------

7) Inicie o servido com o comando abaixo no cmd:

a) php artisan serve

-------------------

8) Baixe o aplicativo Postman e importe o arquivo "Good Shoes.postman_collection":

a) após exportação, inicie os testes.
