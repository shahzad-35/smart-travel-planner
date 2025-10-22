<?php

namespace App\Console\Commands;

use App\Models\Trip;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateTripStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trips:update-statuses {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically update trip statuses based on dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting automatic trip status updates...');

        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        $updatedCount = 0;

        $plannedToOngoing = $this->updatePlannedToOngoing($dryRun);
        $updatedCount += $plannedToOngoing;

        $ongoingToCompleted = $this->updateOngoingToCompleted($dryRun);
        $updatedCount += $ongoingToCompleted;

        $this->info("Status updates completed. Total trips updated: {$updatedCount}");

        if ($dryRun) {
            $this->info('This was a dry run. Run without --dry-run to apply changes.');
        }

        return Command::SUCCESS;
    }

    /**
     * Update planned trips to ongoing if start date has arrived
     */
    private function updatePlannedToOngoing(bool $dryRun): int
    {
        $trips = Trip::where('status', 'planned')
            ->where('start_date', '<=', now()->toDateString())
            ->get();

        $count = 0;
        foreach ($trips as $trip) {
            if ($dryRun) {
                $this->line("Would update trip {$trip->id} ({$trip->destination}) from planned to ongoing");
            } else {
                if ($trip->updateStatus('ongoing', 'Automatic status update: start date reached')) {
                    $count++;
                    Log::info("Auto-updated trip {$trip->id} to ongoing");
                }
            }
        }

        if (!$dryRun) {
            $this->info("Updated {$count} planned trips to ongoing");
        }

        return $dryRun ? $trips->count() : $count;
    }

    /**
     * Update ongoing trips to completed if end date has passed
     */
    private function updateOngoingToCompleted(bool $dryRun): int
    {
        $trips = Trip::where('status', 'ongoing')
            ->where('end_date', '<', now()->toDateString())
            ->get();

        $count = 0;
        foreach ($trips as $trip) {
            if ($dryRun) {
                $this->line("Would update trip {$trip->id} ({$trip->destination}) from ongoing to completed");
            } else {
                if ($trip->updateStatus('completed', 'Automatic status update: end date passed')) {
                    $count++;
                    Log::info("Auto-updated trip {$trip->id} to completed");
                }
            }
        }

        if (!$dryRun) {
            $this->info("Updated {$count} ongoing trips to completed");
        }

        return $dryRun ? $trips->count() : $count;
    }
}
