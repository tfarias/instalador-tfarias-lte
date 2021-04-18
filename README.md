## Instalador de codigos laravel Baseado no template Admin-Lte

## Método de utilização

- após fazer o require

  adicionar no config/app.php

Tfarias\InstaladorTfariasLte\TfariasInstaladorLteServiceProvider::class,

```bash
# Após isso rodar o comando
$ php artisan vendor:publish
e escolher o repositorio

# app/Providers/RepositoryServiceProvider.php
caso ja tenha esse arquivo, deve-se ser adicionado essas chaves

#   //[uses]
abaixo dos uses

dentro da funcao register
#   //[repository]

ficando semelhante a isso

namespace [{namespace}]\Providers;

 //[uses]

class RepositoryServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    public function register()
    {
        //[repository]
    }
}

  caso não tenha esse arquivo ele será criado após rodar a primeira vez o

  $ php artisan create-lte

  porém você deverá adiciona-lo no config/app.php

  adicionar no config/app.php

# app/Providers/RepositoryServiceProvider.php,

```

## \*atenção

para executar os comando primeiro você deve fazer e rodar suas migrations
após isso:

$ php artisan create-lte
