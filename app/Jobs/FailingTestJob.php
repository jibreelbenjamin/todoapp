<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RuntimeException;

/**
 * Job uji yang bisa dikonfigurasi untuk gagal pada N attempt awal,
 * lalu berhasil setelah di-retry.
 *
 * Dipakai oleh JobFailureSeeder untuk menguji dua skenario:
 *   - `failAttempts` >= `tries` -> selalu gagal (failed jobs permanen).
 *   - `failAttempts` <  `tries` -> gagal di awal, sukses saat di-retry
 *                                  (mis. gagal 2x lalu berhasil di attempt 3).
 *
 * Tidak bergantung pada koneksi eksternal (SMTP, Google API, dll),
 * sehingga hasilnya deterministik.
 */
class FailingTestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /**
     * @param  string  $label  Nama/label untuk membedakan tiap job.
     * @param  string  $message  Pesan exception yang dilempar saat gagal.
     * @param  int  $failAttempts  Berapa attempt pertama yang sengaja digagalkan.
     *                             Untuk selalu gagal, set >= $tries.
     */
    public function __construct(
        public string $label,
        public string $message,
        public int $failAttempts = 1,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->attempts() <= $this->failAttempts) {
            throw new RuntimeException("[{$this->label}] attempt {$this->attempts()} gagal: {$this->message}");
        }
    }
}
