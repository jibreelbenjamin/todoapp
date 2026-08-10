<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class SyncTaskToGoogleCalendar implements ShouldQueue
{
    use Batchable, Queueable;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(public Task $task)
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

        $user = $this->task->user;

        if (! $user || ! $user->google_access_token || ! $this->task->due_date) {
            return;
        }

        Http::withToken($user->google_access_token)
            ->post('https://www.googleapis.com/calendar/v3/calendars/primary/events', [
                'summary' => $this->task->title,
                'start' => ['date' => $this->task->due_date->format('Y-m-d')],
                'end' => ['date' => $this->task->due_date->format('Y-m-d')],
            ]);
    }
}
