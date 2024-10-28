<?php

namespace App\Console\Commands;

use App\Http\Controllers\ModelController;
use Illuminate\Console\Command;

class SyncModelsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:models';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync available SD models';

    /**
     * Execute the console command.
     */
    public function handle(ModelController $modelController): int
    {
        $this->info('Syncing models...');
        
        try {
            $modelController->sync();
            $this->info('Models synced successfully.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to sync models: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
