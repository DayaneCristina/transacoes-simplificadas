

# 💰 Transações Simplificadas

<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>

## Sobre o Projeto

Esse projeto consiste em um sistema para simular a realização de transações entre usuários do sistema.

### Tecnologias utilizadas

Para a realização desse projeto foram utilizados:

- PHP 8.4
- Laravel 12
- Docker
- Swagger
- Postgresql
- Kafka
- Redis

### Estrutura

O projeto foi desenvolvido para ser algo simples e ser feito em tempo curto por isso optei por utilizar o MVC como base para realizar o desenvolvimento. A estrutura de pastas pode ser vista abaixo:

```
├───app
│   ├───Console
│   │   └───Commands
│   ├───Enums
│   ├───Exceptions
│   │   ├───Business
│   │   └───Infrastructure
│   ├───Http
│   │   ├───Controllers
│   │   └───Middlewares
│   ├───Models
│   ├───Providers
│   ├───Repositories
│   │   └───External
│   ├───Services
│   └───Swagger
|
```
## ⚙️ Como Rodar?

Para rodar temos que ter o Docker sendo executado na máquina local. Uma vez que o Docker esteja 100% rodando, podemos executar o seguinte comando:
```shell
make up
```

Caso não possua o `make` instalado em seu sistema, pode rodar com o comando do `docker-compose` mesmo, ficando assim:
```shell
docker-compose up -d
```

Uma vez que os containeres estejam de pé, será necessário rodar as `migrations` e os `seeders` do banco de dados. Para isso, execute:
```shell
make migrate
```
ou
```shell
docker exec -it transacoes-simplificadas.app php artisan migrate
```

Para rodar as `seeds` o comando é:
```shell
make seed
```
ou
```shell
docker exec -it transacoes-simplificadas.app php artisan db:seed
```

Feito isso, o projeto já está pronto para ser executado corretamente. Para acessar a API, o link é:

http://localhost:8080/api/documentation

### Testes

Para executar os testes o comando é:
```shell
make test
```
ou
```shell
docker-compose exec transacoes-simplificadas.app ./vendor/bin/phpunit
```

### Endpoints

A aplicação possui apenas 3 endpoints sendo eles:

[GET] /api/accounts/balance
```
curl -X 'GET' \
  'http://localhost:8080/api/accounts/balance' \
  -H 'accept: application/json' \
  -H 'user-id: 1' \
  -H 'X-CSRF-TOKEN: '
```

[POST] /api/accounts/credit
```
curl -X 'POST' \
  'http://localhost:8080/api/accounts/credit' \
  -H 'accept: */*' \
  -H 'user-id: 1' \
  -H 'Content-Type: application/json' \
  -H 'X-CSRF-TOKEN: ' \
  -d '{
  "amount": 100.5,
  "account_destination": 1,
  "transaction_code": 3
}'
```

[POST] /api/accounts/transfer
```
curl -X 'POST' \
  'http://localhost:8080/api/accounts/transfer' \
  -H 'accept: */*' \
  -H 'user-id: 1' \
  -H 'Content-Type: application/json' \
  -H 'X-CSRF-TOKEN: ' \
  -d '{
  "amount": 0.05,
  "account_origin": 1,
  "account_destination": 3
}'
```

# 🚧 Pontos de melhoria

Como mencionado anteriormente, o projeto foi feito utilizando a estrutura do MVC como base por ser um desenvolvimento mais rápido e  ideal para entregar POCs ou MVPs. Além disso utilizamos alguns conceitos de SOLID. Porém há algumas melhorias que podem ser feitas.

### Arquitetura

Para um projeto grande com regras complexas, o ideal seria utilizar uma arquitetura em camadas como o DDD pois isso proporcinaria uma segregação melhor dos domínios consequentemente facilitando a implementação das regras que um sistema de cunho financeiro tem e tornando a manutenção e evolução da aplicação mais fácil a longo prazo.

Além disso, utilizar um sistema que funcione baseado em eventos e de forma ASYNC (nos fluxos de transação) pode torna-lo mais performático.

Outro ponto interessante seria a utilização de um padrão CQRS, ou seja, separar as operações de leitura das operações de escrita.

### Banco de Dados
Embora o PostgreSql seja um excelente banco de dados relacional para um projeto que visa ser escalado, seria interessante a utilização de um banco de dados NoSQL (DybanoDB ou MongoDB) para auxiliar em fluxos de Logs ou que não sejam transacionais.

### Cache
A utilização de cache para ações comuns que não se alteram tanto (como por exemplo o  e-mail de um usuário para disparo de notificação) pode auxiliar na performance também desonerando a base de dados para consultas.
