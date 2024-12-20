# Gabinete Digital

## Clonar o Repositório Git

Para começar, clone este repositório Git executando o seguinte comando:

```
git clone https://github.com/JairoTSantos/gabinete <pasta_do_aplicativo>
```
Coloque todos os arquivo na pasta da sua hospedagem. `meu_dominio.com.br/pasta_do_aplicativo`

Entre na pasta do aplicativo e digite `composer install`

## Configurar as Variáveis de Ambiente

Antes de executar a aplicação, é necessário configurar as variáveis de configuração. Modifique o arquivo `/src/Configs/configs.php` na raiz do projeto com as seguintes variáveis:

```
<?php
return [

    'database' => [
        'host' => 'host do banco',
        'name' => 'nome do banco',
        'user' => 'usuario do banco',
        'password' => 'senha',
    ],

    'master_user' => [
        'master_name' => 'usuario administrativo',
        'master_email' => 'admin@admin.com',
        'master_pass' => 'senha',
    ],
    
    'app' => [
        'session_time' => 24,//tempo de duração da sessão
        'base_url' =>rtrim($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/', '')
    ]
];

```
## Sincronizar as tabelas do banco
Importe o sript sql no seu banco de dados. /mysql/db.sql