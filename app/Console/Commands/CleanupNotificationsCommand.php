<?php

namespace App\Console\Commands;

use App\Support\NotificationManager;
use Illuminate\Console\Command;

class CleanupNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:notifications {days=30 : Number of days to keep notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old notifications';

    /**
     * Execute the console command.
     */
    public function handle(NotificationManager $notificationManager): int
    {
        $days = $this->argument('days');
        
        $this->info("Cleaning up notifications older than {$days} days...");
        
        $count = $notificationManager->deleteOld($days);
        
        $this->info("Deleted {$count} old notifications.");
        
        return Command::SUCCESS;
    }
}
