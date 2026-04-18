<?php

declare(strict_types=1);

namespace App\Console\Commands\Development;

use App\Console\Concerns\GuardsProductionEnvironment;
use Illuminate\Console\Command;

/**
 * @deprecated Utiliser `php artisan project:prepare` (ou `project:dev` qui l’enchaîne).
 * Conservé comme alias pour scripts / habitudes locales.
 */
class PrepareProjectCommand extends Command
{
    use GuardsProductionEnvironment;

    protected $signature = 'server:prepare';

    protected $description = '[Déprécié] Alias vers project:prepare — préférez php artisan project:prepare';

    public function handle(): int
    {
        if (! $this->guardDevelopmentOnly()) {
            return self::FAILURE;
        }

        $this->warn('server:prepare est déprécié — utilisez : php artisan project:prepare');
        $this->newLine();

        return $this->call('project:prepare');
    }
}
