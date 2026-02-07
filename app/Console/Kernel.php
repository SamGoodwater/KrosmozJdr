<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

//  generate:IconsGenerator
// generate:Test

class Kernel extends ConsoleKernel
{
    protected $middleware = [
        \App\Http\Middleware\HandleInertiaRequests::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('media:clean-thumbnails')->daily();

        // Sync automatique du catalogue de ressources depuis DofusDB
        // Activable via env: SCRAPPING_RESOURCES_AUTO_SYNC=true (par défaut: false)
        if ((bool) env('SCRAPPING_RESOURCES_AUTO_SYNC', false)) {
            $at = env('SCRAPPING_RESOURCES_AUTO_SYNC_AT', '03:00');
            $limit = (int) env('SCRAPPING_RESOURCES_AUTO_SYNC_LIMIT', 100);
            $schedule
                ->command("scrapping --entity=resource --resource-types=allowed --limit={$limit} --max-pages=0 --max-items=20000")
                ->dailyAt($at);
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
