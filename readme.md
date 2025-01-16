# Gabinete Digital

## Clonar o Repositório Git

Para começar, clone este repositório Git executando o seguinte comando:

```
git clone https://github.com/JairoJeffersont/app <pasta_do_aplicativo>
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
        'session_time' => 24,//tempo de duração da sessão...
    ]
];

```
## Sincronizar as tabelas do banco
Importe o sript sql no seu banco de dados. /mysql/db.sql

## Dependências Principais

O projeto utiliza as seguintes dependências principais:

PHP >= 7.4.0

Mysql >= 5.3

PHPMailer (v6.9.3): Biblioteca robusta para envio de e-mails via SMTP.

Requer extensões do PHP: ext-ctype, ext-filter, ext-hash, ext-mbstring (opcional para codificações multibyte), e ext-openssl (opcional para envio SMTP seguro).

Certifique-se de verificar as extensões do PHP necessárias e habilitá-las no servidor.