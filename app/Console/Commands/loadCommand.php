<?php

namespace App\Console\Commands;

use App\Console\Concerns\GuardsProductionEnvironment;
use Illuminate\Console\Command;

class loadCommand extends Command
{
    use GuardsProductionEnvironment;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:load';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lancer les servers de développement php et node';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! $this->guardDevelopmentOnly()) {
            return self::FAILURE;
        }

        $this->info('Optimisation de Laravel');
        $this->call('optimize');

        $this->info('Lancement de vivaldi');
        exec("start vivaldi 'http://localhost:8000'");

        $this->info('Lancement du serveur PHP');
        exec('composer run dev');
    }
}
