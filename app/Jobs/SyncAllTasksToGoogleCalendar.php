<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;

class SyncAllTasksToGoogleCalendar implements ShouldQueue
{
    use Batchable, Queueable;

    public $tries = 3;

    public $timeout = 600;

    public function __construct(public int $userId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        $jobs = collect();

        Task::where('id_user', $this->userId)
            ->orderBy('id')
            ->chunkById(100, function ($tasks) use (&$jobs) {
                foreach ($tasks as $task) {
                    $jobs->push(new SyncTaskToGoogleCalendar($task));
                }
            });

        Bus::batch($jobs)
            ->allowFailures()
            ->name("sync-all-user-{$this->userId}")
            ->dispatch();
    }
}
