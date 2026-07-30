<?php

namespace App\Console\Commands;

use App\Services\TaskService;
use Illuminate\Console\Command;

class AutoDropOverdue extends Command
{
    protected $daysAfterOverdue = 3;
    protected $signature = 'tasks:auto-drop-overdue';
    protected $description = "Auto drop task yang overdue";

    public function handle(TaskService $service)
    {
        $count = $service->bulkStatusByCondition(['pending', 'progress'], now()->subDays(3), 'drop');
        $this->info("Auto drop task overdue melebihi {$this->daysAfterOverdue} hari");
        $this->info(($count > 0 ? $count : 0).' task telah di-drop.');
    }
}
