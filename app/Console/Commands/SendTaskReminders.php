<?php

namespace App\Console\Commands;

use App\Jobs\SendTaskReminderEmail;
use App\Models\Task;
use Illuminate\Console\Command;

class SendTaskReminders extends Command
{
    protected $signature = 'tasks:send-reminders';

    protected $description = 'Send reminder emails for tasks due soon';

    public function handle(): int
    {
        $tasks = Task::with('user')
            ->whereIn('status', ['pending', 'progress'])
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [
                now()->startOfDay()->toDateString(),
                now()->addDays(3)->endOfDay()->toDateString(),
            ])
            ->get();

        foreach ($tasks as $task) {
            SendTaskReminderEmail::dispatch($task);
        }

        $this->info($tasks->count().' reminder jobs dispatched.');

        return 0;
    }
}
