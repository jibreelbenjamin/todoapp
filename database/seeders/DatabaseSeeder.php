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

        $totalCategories = 75000;
        $totalTasks = 100000;
        $chunkSize = 1000;

        $this->command->getOutput()->progressStart($totalCategories + $totalTasks);

        for ($i = 0; $i < $totalCategories; $i += $chunkSize) {
            $count = min($chunkSize, $totalCategories - $i);
            Category::factory($count)
                ->recycle($users)
                ->create();

            $this->command->getOutput()->progressAdvance($count);
        }

        for ($i = 0; $i < $totalTasks; $i += $chunkSize) {
            $count = min($chunkSize, $totalTasks - $i);
            Task::factory($count)
                ->recycle($users)
                ->create();

            $this->command->getOutput()->progressAdvance($count);
        }

        $this->command->getOutput()->progressFinish();
    }
}
