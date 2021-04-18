<?php

namespace Tfarias\InstaladorTfariasLte\Commands;

use Tfarias\InstaladorTfariasLte\Services\Crud\CriaForms;
use Tfarias\InstaladorTfariasLte\Services\Crud\CriaModel;
use Tfarias\InstaladorTfariasLte\Services\Crud\CriarController;
use Tfarias\InstaladorTfariasLte\Services\Crud\CriaRepository;
use Tfarias\InstaladorTfariasLte\Services\Crud\CriaRequest;
use Tfarias\InstaladorTfariasLte\Services\Crud\CriarRelatorio;
use Tfarias\InstaladorTfariasLte\Services\Crud\CriaViews;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateModel extends Command
{

    protected $signature = 'create-model';

    protected $description = 'Cria apenas o model. por Tiago F. S.';


    /**
     * @var CriaModel
     */
    private $criaModel;


    public function __construct(
        CriaModel $criaModel
    ) {
        parent::__construct();

        $this->criaModel = $criaModel;

    }

    public function handle()
    {

        while (true) {
            $tabela = $this->ask("Qual a tabela?");


            echo "\n\n";
            echo "##############################################################\n";
            echo "####################### Conferir dados #######################\n";
            echo "##############################################################\n";
            echo "\n\n";
            echo "Tabela --------------------" . $tabela . "\n";


            echo "\n\n";

            $confirma = $this->ask("Confirma os dados abaixo (y/n)?");

            if (strtolower($confirma) != "y") {
                echo "\n\n";
                echo "##############################################################\n";
                echo "################## Reiniciando processo ######################\n";
                echo "##############################################################\n";
                echo "\n\n";
            } else {
                echo "\n\n";
                echo "GERANDO INFORMACOES.....................\n";
                break;
            }
        }



        try {
             // Agora vamos criar o model
            $this->criaModel->criar($tabela);
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
