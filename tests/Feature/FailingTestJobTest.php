<?php

use App\Jobs\FailingTestJob;

// Stub container untuk mensimulasikan attempt ke-N oleh queue worker.
$makeAttempt = function (int $attempt) {
    $job = new FailingTestJob('retry-test', 'pesan uji', failAttempts: 2);

    // InteractsWithQueue menyediakan property `$job`; kita isi dengan stub
    // yang mengembalikan attempts() sesuai nilai yang kita mau.
    $job->job = new class($attempt)
    {
        public function __construct(public int $attempt) {}

        public function attempts(): int
        {
            return $this->attempt;
        }
    };

    return $job;
};

test('job gagal pada attempt pertama', function () use ($makeAttempt) {
    $job = $makeAttempt(1);

    $job->handle();
})->throws(RuntimeException::class, '[retry-test] attempt 1 gagal: pesan uji');

test('job gagal pada attempt kedua', function () use ($makeAttempt) {
    $job = $makeAttempt(2);

    $job->handle();
})->throws(RuntimeException::class, '[retry-test] attempt 2 gagal: pesan uji');

test('job berhasil setelah retry (attempt ketiga)', function () use ($makeAttempt) {
    $job = $makeAttempt(3);

    // Tidak boleh melempar exception => berhasil.
    $job->handle();

    expect(true)->toBeTrue();
});

test('job dengan failAttempts >= tries selalu gagal', function () {
    $job = new FailingTestJob('always-fail', 'selalu gagal', failAttempts: 3);

    foreach ([1, 2, 3] as $attempt) {
        $job->job = new class($attempt)
        {
            public function __construct(public int $attempt) {}

            public function attempts(): int
            {
                return $this->attempt;
            }
        };

        try {
            $job->handle();
        } catch (RuntimeException $e) {
            continue;
        }

        $this->fail("Job tidak gagal pada attempt {$attempt}.");
    }

    expect(true)->toBeTrue();
});
