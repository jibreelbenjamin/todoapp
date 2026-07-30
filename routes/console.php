<?php

use Illuminate\Support\Facades\Schedule;

// use App\Console\Commands\AutoDropOverdue;

// Schedule::command(AutoDropOverdue::class)->dailyAt('00:00');
Schedule::command('tasks:auto-drop-overdue')->dailyAt('00:00');
