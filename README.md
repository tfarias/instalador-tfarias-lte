## Instalador de codigos laravel Baseado no template Metronic

Ele funciona baseado no modelo de permissões
caso queira aplicar basta ter mi middware de permissoes que utiliza table de rota

## Método de utilização

- após fazer o require

  adicionar no config/app.php

Tfarias\InstaladorTfarias\TfariasInstaladorServiceProvider::class,

## \*atenção

para executar os comando primeiro você deve fazer e rodar suas migrations
após isso:

$ php artisan create-crud

e também deve ter essas chaves nos determinados arquivos

```bash
# routes/web.php
deve adicionar essa chave onde deseja que as rotas sejam criadas
# //[rota]

# resouces/views/partials/menu.blade.php
  deve adicionar essa chave onde deseja que os menus sejam criados
# {{--menu--}}

# app/Providers/RepositoryServiceProvider.php

deve aicionar essa chave abaido dos uses
#  //[uses]

e esta dentro da função register

# //[repository]

```
