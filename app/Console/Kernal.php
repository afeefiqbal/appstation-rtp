<?php 
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\AdSlot;

class Kernel extends ConsoleKernel
{
    /**
     * Register Artisan commands.
     *
     * Make sure your EvaluateBids command is registered here.
     */
    protected $commands = [
        \App\Console\Commands\UpdateAdSlotStatuses::class,
        \App\Console\Commands\EvaluateBids::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
          $schedule->command('rtb:update-statuses')->everyMinute();
        // Prune old ad slots daily at 3AM (optional cleanup)
        $schedule->command('model:prune', ['--model' => 'App\\Models\\AdSlot'])
            ->dailyAt('03:00');
        // Update ad slot statuses every minute
        $schedule->call(function () {
            AdSlot::where('status', 'upcoming')
                ->where('start_time', '<=', now())
                ->update(['status' => 'open']);

            AdSlot::where('status', 'open')
                ->where('end_time', '<=', now())
                ->update(['status' => 'closed']);
        })->everyMinute()->withoutOverlapping();

        // Evaluate winners
        $schedule->command('rtb:evaluate')
            ->everyMinute()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
