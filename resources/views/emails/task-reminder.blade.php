<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Reminder</title>
</head>
<body>
    <h1>Pengingat Tugas</h1>
    <p>Tugas <strong>{{ $task->title }}</strong> dijadwalkan pada <strong>{{ $task->due_date->format('d M Y') }}</strong>.</p>
    <p>{{ $task->description }}</p>
    <p>Silakan buka aplikasi untuk melihat detail tugas. ppppp</p>
</body>
</html>
