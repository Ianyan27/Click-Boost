<?php

namespace App\Console\Commands;

use App\Services\ClickUpService;
use Illuminate\Console\Command;

class SyncClickUpUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clickup:sync-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync workspace members to ClickUp user folder';

    /**
     * Execute the console command.
     */
    public function handle(ClickUpService $clickup)
    {
        $this->info('Starting ClickUp user sync..');

        try {
            $clickup->syncWorkspaceMembersToUserFolder();
            $this->info('User Sync completed successfully!');
            return Command::SUCCESS;
        } catch (\Exception $e){
            $this->error('User sync failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
