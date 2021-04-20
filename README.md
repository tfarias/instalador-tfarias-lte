## Instalador de codigos laravel Baseado no template Admin-Lte

## Método de utilização

- após fazer o require

```bash
# Após isso rodar o comando
$ php artisan vendor:publish --force

ecolhe o provider:  "Tfarias\InstaladorTfariasLte\TfariasInstaladorLteServiceProvider"

#

  adicionar no config/app.php

# App\Providers\RepositoryServiceProvider::class,


```

## \*atenção

para executar os comando primeiro você deve fazer e rodar suas migrations
após isso:

$ php artisan create-lte

## Filtros

```bash
#
 Para os campos que deseja ter os filtros basta adicionar um comment na migration

exemplo

   Schema::create('tipo', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('descricao')->comment('filter');
            $table->timestamps();
      });

      na migration acima a coluna descricao vai constar nos filtros
```
