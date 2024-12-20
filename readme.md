# Gabinete Digital

## Clonar o Repositório Git

Para começar, clone este repositório Git executando o seguinte comando:

```
git clone https://github.com/JairoTSantos/gabinete <pasta_do_aplicativo>
```
Coloque todos os arquivo na pasta da sua hospedagem. `meu_dominio.com.br/pasta_do_aplicativo`

Entre na pasta do aplicativo e digite `composer install`

## Configurar as Variáveis de Ambiente

Antes de executar a aplicação, é necessário configurar as variáveis de configuração. Modifique o arquivo `.env` na raiz do projeto com as seguintes variáveis:

```
DB_HOST=localhost
DB_NAME=gabinete_digital
DB_USER=root
DB_PASS=root

SESSION_TIME=24

MASTER_USER=Administrador
MASTER_EMAIL=admin@admin.com
MASTER_PASS=senha

```
## Sincronizar as tabelas do banco
Importe o sript sql no seu banco de dados. /mysql/db.sql