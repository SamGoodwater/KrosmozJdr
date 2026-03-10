<?php

namespace App\Console;

use App\Jobs\SendNotificationDigestsJob;
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
        $schedule->command('privacy:process-deletion-requests')->dailyAt('02:00');

        // Digests de notifications (quotidien, hebdo, mensuel)
        $schedule->job(new SendNotificationDigestsJob('daily'))->dailyAt('00:05');
        $schedule->job(new SendNotificationDigestsJob('weekly'))->weeklyOn(1, '00:10');
        $schedule->job(new SendNotificationDigestsJob('monthly'))->monthlyOn(1, '00:15');

        // Mise à jour des entités avec auto_update=true (project:update)
        // Activable via env: PROJECT_UPDATE_AUTO_ENABLED=true
        // Fréquence via PROJECT_UPDATE_CRON (format cron, défaut: 1er du mois à 1h)
        if ((bool) env('PROJECT_UPDATE_AUTO_ENABLED', false)) {
            $cron = env('PROJECT_UPDATE_CRON', '0 1 1 * *');
            $schedule->command('project:update')->cron($cron);
        }

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
