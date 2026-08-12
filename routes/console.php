<?php
// php artisan queue:work
// php artisan schedule:list
use Illuminate\Support\Facades\Schedule;

Schedule::command('tasks:auto-drop-overdue')->dailyAt('00:00'); // php artisan tasks:auto-drop-overdue
Schedule::command('tasks:send-reminders')->dailyAt('00:00'); // php artisan tasks:send-reminders

// php artisan tinker --execute 'Mail::to("intern.jibreelbenjamin@gmail.com")->send(new App\Mail\TaskReminderMail(App\Models\Task::first())); echo "OK";'