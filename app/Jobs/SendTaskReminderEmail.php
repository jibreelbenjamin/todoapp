<?php

namespace App\Jobs;

use App\Mail\TaskReminderMail;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTaskReminderEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public Task $task) {}

    public function handle(): void
    {
        if (! $this->task->user || ! $this->task->user->email) {
            return;
        }

        Mail::to($this->task->user->email)->send(new TaskReminderMail($this->task));
    }
}
