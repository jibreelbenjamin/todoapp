<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory(100)->create();

        $users = User::all();

        $totalCategories = 200;
        $totalTasks = 5000;
        $chunkSize = 100;

        $this->command->getOutput()->progressStart($totalCategories + $totalTasks);

        for ($i = 0; $i < $totalCategories; $i += $chunkSize) {
            $count = min($chunkSize, $totalCategories - $i);
            Category::factory($count)
                ->recycle($users)
                ->create();

            $this->command->getOutput()->progressAdvance($count);
        }

        // Proporsi task seperti data produksi:
        // ~40% overdue (pending/progress + due_date sudah lewat)
        // ~20% pending (masih feasible, due_date masa depan)
        // ~15% progress (masih dikerjakan, due_date masa depan)
        // ~15% done
        // ~10% dropped
        $overdueCount = (int) ($totalTasks * 0.4);
        $pendingFutureCount = (int) ($totalTasks * 0.2);
        $progressFutureCount = (int) ($totalTasks * 0.15);
        $doneCount = (int) ($totalTasks * 0.15);
        $droppedCount = $totalTasks - $overdueCount - $pendingFutureCount - $progressFutureCount - $doneCount;

        // Overdue tasks — pending/progress + due_date sudah lewat
        for ($i = 0; $i < $overdueCount; $i += $chunkSize) {
            $count = min($chunkSize, $overdueCount - $i);
            Task::factory($count)
                ->overdue()
                ->recycle($users)
                ->create();

            $this->command->getOutput()->progressAdvance($count);
        }

        // Pending tasks dengan due_date masa depan
        for ($i = 0; $i < $pendingFutureCount; $i += $chunkSize) {
            $count = min($chunkSize, $pendingFutureCount - $i);
            Task::factory($count)
                ->pending()
                ->recycle($users)
                ->create();

            $this->command->getOutput()->progressAdvance($count);
        }

        // Progress tasks dengan due_date masa depan
        for ($i = 0; $i < $progressFutureCount; $i += $chunkSize) {
            $count = min($chunkSize, $progressFutureCount - $i);
            Task::factory($count)
                ->inProgress()
                ->recycle($users)
                ->create();

            $this->command->getOutput()->progressAdvance($count);
        }

        // Done tasks
        for ($i = 0; $i < $doneCount; $i += $chunkSize) {
            $count = min($chunkSize, $doneCount - $i);
            Task::factory($count)
                ->done()
                ->recycle($users)
                ->create();

            $this->command->getOutput()->progressAdvance($count);
        }

        // Dropped tasks
        for ($i = 0; $i < $droppedCount; $i += $chunkSize) {
            $count = min($chunkSize, $droppedCount - $i);
            Task::factory($count)
                ->dropped()
                ->recycle($users)
                ->create();

            $this->command->getOutput()->progressAdvance($count);
        }

        $this->command->getOutput()->progressFinish();
    }
}
