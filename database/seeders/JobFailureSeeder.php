<?php

namespace Database\Seeders;

use App\Jobs\FailingTestJob;
use App\Jobs\SendTaskReminderEmail;
use App\Jobs\SyncAllTasksToGoogleCalendar;
use App\Jobs\SyncTaskToGoogleCalendar;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder khusus untuk menguji job yang gagal (failed jobs).
 *
 * Seeder ini sengaja mendispatch job-job yang dijamin akan gagal saat
 * dijalankan oleh queue worker, sehingga kamu bisa memantau & menguji
 * alur failed jobs (tabel `failed_jobs`, dashboard Horizon, retry, dsb).
 *
 * Ada dua jenis skenario yang dihasilkan:
 *   1. `FailingTestJob` -> kegagalan deterministik: bisa "selalu gagal"
 *      (failed permanen) ATAU "gagal di awal lalu sukses saat di-retry"
 *      untuk menguji alur retry. Tidak bergantung koneksi eksternal.
 *   2. Job nyata (`SyncTaskToGoogleCalendar`, dll) -> kegagalan karena
 *      token Google palsu (HTTP request ke Google API biasanya gagal).
 *
 * Cara pakai (pastikan queue/Horizon worker sedang jalan):
 *   php artisan db:seed
 *   php artisan db:seed --class=JobFailureSeeder
 *
 * Lalu cek hasilnya:
 *   php artisan queue:failed
 *   # atau lihat dashboard Horizon: Failed Jobs tab
 */
class JobFailureSeeder extends Seeder
{
    public function run(): void
    {
        // ------------------------------------------------------------------
        // A. Kegagalan deterministik melalui FailingTestJob.
        //
        // Setiap job melempar exception pada N attempt pertama, lalu berhasil
        // setelah di-retry. Ada 2 skenario yang bisa diuji:
        //   1) ALWAYS FAIL : failAttempts >= tries -> job gagal permanen.
        //   2) RETRY OK    : failAttempts <  tries -> gagal di awal, lalu
        //      sukses saat di-retry (muncul sebagai "gagal" dulu di dashboard,
        //      kemudian berhasil pada attempt berikutnya).
        // ------------------------------------------------------------------

        // Skenario 1: selalu gagal (failed jobs permanen).
        FailingTestJob::dispatch('http-500', 'HTTP request ke service eksternal mengembalikan 500.', 3);
        FailingTestJob::dispatch('invalid-json', 'Tidak dapat meng-parse respon JSON dari API.', 3);
        FailingTestJob::dispatch('division-by-zero', 'Perhitungan rasio menghasilkan pembagian dengan nol.', 3);

        // Skenario 2: gagal awal, lalu berhasil setelah retry.
        //    - 'db-timeout-retry' gagal di attempt 1 & 2, sukses di attempt 3.
        //    - 'http-retry' gagal di attempt 1, sukses di attempt 2.
        //    - 'transient-retry' sama, diawali 2 gagal.
        FailingTestJob::dispatch('db-timeout-retry', 'Koneksi database timeout saat memproses baris 42.', 2);
        FailingTestJob::dispatch('http-retry', 'Koneksi ke service eksternal terputus sementara.', 1);
        FailingTestJob::dispatch('transient-retry', 'Slot worker habis, coba lagi sebentar lagi.', 2);

        // ------------------------------------------------------------------
        // B. SyncTaskToGoogleCalendar -> gagal karena token Google palsu.
        //    User diberi token tidak valid, sehingga HTTP POST ke Google
        //    Calendar API akan melempar exception (401 / network error).
        // ------------------------------------------------------------------
        // Pakai firstOrCreate agar seeder bisa dijalankan berulang kali.
        $user = User::firstOrCreate(
            ['email' => 'failed-sync@example.com'],
            [
                'name' => 'Failed-Sync-User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'google_access_token' => 'invalid-fake-access-token',
                'google_refresh_token' => null,
            ]
        );

        $tasks = Task::factory()
            ->count(5)
            ->state([
                'id_user' => $user->id,
                'due_date' => now()->addDay()->format('Y-m-d'),
            ])
            ->create();

        // Dispatch satu per satu: setiap job akan gagal karena token palsu.
        foreach ($tasks as $task) {
            SyncTaskToGoogleCalendar::dispatch($task);
        }

        // ------------------------------------------------------------------
        // C. SyncAllTasksToGoogleCalendar -> batch berisi job gagal.
        //    Karena `allowFailures()` diaktifkan di job tsb, batch tetap
        //    berlanjut, tapi job di dalamnya tetap tercatat failed.
        // ------------------------------------------------------------------
        $batchUser = User::firstOrCreate(
            ['email' => 'failed-batch@example.com'],
            [
                'name' => 'Failed-Batch-User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'google_access_token' => 'invalid-fake-batch-token',
                'google_refresh_token' => null,
            ]
        );

        Task::factory()
            ->count(5)
            ->state([
                'id_user' => $batchUser->id,
                'due_date' => now()->addDay()->format('Y-m-d'),
            ])
            ->create();

        SyncAllTasksToGoogleCalendar::dispatch($batchUser->id);

        // ------------------------------------------------------------------
        // D. SendTaskReminderEmail -> dispatch beberapa sample biar terlihat
        //    di antrean. Job ini punya guard `if (! $this->task->user) return;`
        //    sehingga ia hanya benar-benar gagal kalau `Mail::send` melempar
        //    error (mis. SMTP tidak bisa terhubung).
        // ------------------------------------------------------------------
        $sampleTask = Task::query()->first();

        if ($sampleTask) {
            SendTaskReminderEmail::dispatch($sampleTask);
        }

        $this->command?->info('JobFailureSeeder selesai. Job-job gagal telah di-dispatch ke queue.');
    }
}
