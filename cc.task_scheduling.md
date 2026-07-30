# Laravel Task Scheduling — Dokumentasi Lengkap

Lokasi definisi: `routes/console.php` (Laravel 11+) atau `app/Console/Kernel.php` method `schedule()` (Laravel 10 ke bawah).

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('app:generate-report')->dailyAt('06:00');
```

---

## 1. Frequency Methods (kapan jalannya)

| Method | Keterangan |
|---|---|
| `->cron('* * * * *')` | Custom cron expression |
| `->everySecond()` | Tiap detik |
| `->everyTwoSeconds()` | Tiap 2 detik |
| `->everyFiveSeconds()` | Tiap 5 detik |
| `->everyTenSeconds()` | Tiap 10 detik |
| `->everyFifteenSeconds()` | Tiap 15 detik |
| `->everyTwentySeconds()` | Tiap 20 detik |
| `->everyThirtySeconds()` | Tiap 30 detik |
| `->everyMinute()` | Tiap menit |
| `->everyTwoMinutes()` | Tiap 2 menit |
| `->everyThreeMinutes()` | Tiap 3 menit |
| `->everyFourMinutes()` | Tiap 4 menit |
| `->everyFiveMinutes()` | Tiap 5 menit |
| `->everyTenMinutes()` | Tiap 10 menit |
| `->everyFifteenMinutes()` | Tiap 15 menit |
| `->everyThirtyMinutes()` | Tiap 30 menit |
| `->hourly()` | Tiap jam, di menit ke-0 |
| `->hourlyAt(17)` | Tiap jam, di menit ke-17 |
| `->everyOddHour($minutes = 0)` | Tiap jam ganjil (1, 3, 5, ...) |
| `->everyTwoHours($minutes = 0)` | Tiap 2 jam |
| `->everyThreeHours($minutes = 0)` | Tiap 3 jam |
| `->everyFourHours($minutes = 0)` | Tiap 4 jam |
| `->everySixHours($minutes = 0)` | Tiap 6 jam |
| `->daily()` | Tiap hari jam 00:00 |
| `->dailyAt('13:00')` | Tiap hari di jam tertentu |
| `->twiceDaily(1, 13)` | 2x sehari, jam 01:00 & 13:00, menit ke-0 |
| `->twiceDailyAt(1, 13, 15)` | Sama, tapi menitnya custom (menit ke-15) |
| `->weekly()` | Tiap minggu, hari Minggu jam 00:00 |
| `->weeklyOn(1, '8:00')` | Tiap minggu, hari tertentu (1 = Senin) jam tertentu |
| `->monthly()` | Tiap bulan, tanggal 1 jam 00:00 |
| `->monthlyOn(4, '15:00')` | Tiap bulan, tanggal tertentu jam tertentu |
| `->twiceMonthly(1, 16, '13:00')` | 2x sebulan, tanggal 1 & 16 jam 13:00 |
| `->lastDayOfMonth('15:00')` | Hari terakhir tiap bulan, jam tertentu |
| `->quarterly()` | Tiap kuartal, tanggal 1 jam 00:00 |
| `->quarterlyOn(4, '14:00')` | Tiap kuartal, tanggal & jam tertentu |
| `->yearly()` | Tiap tahun, 1 Januari jam 00:00 |
| `->yearlyOn(6, 1, '17:00')` | Tiap tahun, bulan (6) & tanggal (1) & jam tertentu |
| `->timezone('Asia/Jakarta')` | Set timezone untuk jadwal ini |

> Catatan: `->hourlyAt()`, `->dailyAt()`, dll bisa terima array menit/jam, contoh `->hourlyAt([17, 33])`.

---

## 2. Constraint Methods (hari apa boleh jalan)

| Method | Keterangan |
|---|---|
| `->weekdays()` | Hanya Senin–Jumat |
| `->weekends()` | Hanya Sabtu–Minggu |
| `->sundays()` | Hanya Minggu |
| `->mondays()` | Hanya Senin |
| `->tuesdays()` | Hanya Selasa |
| `->wednesdays()` | Hanya Rabu |
| `->thursdays()` | Hanya Kamis |
| `->fridays()` | Hanya Jumat |
| `->saturdays()` | Hanya Sabtu |
| `->days([0, 3])` | Hari custom (0 = Minggu, 3 = Rabu) |
| `->between('7:00', '22:00')` | Hanya dijalankan di rentang jam ini |
| `->unlessBetween('23:00', '4:00')` | Kebalikannya, skip di rentang jam ini |
| `->when(fn () => true)` | Jalan hanya kalau closure return true |
| `->unless(fn () => false)` | Jalan hanya kalau closure return false |
| `->environments(['production'])` | Hanya jalan di environment tertentu |

Contoh gabungan:
```php
Schedule::command('emails:send')
    ->weekdays()
    ->hourly()
    ->between('8:00', '17:00')
    ->timezone('Asia/Jakarta');
```

---

## 3. Mencegah Task Overlap / Tumpang Tindih

| Method | Keterangan |
|---|---|
| `->withoutOverlapping()` | Skip kalau task sebelumnya masih jalan |
| `->withoutOverlapping(10)` | Sama, tapi lock expire dalam 10 menit (default 24 jam) |
| `->onOneServer()` | Kalau app jalan di banyak server, cuma 1 server yang eksekusi |

---

## 4. Menjalankan Task di Background

```php
Schedule::command('app:heavy-task')->daily()->runInBackground();
```
Berguna kalau ada beberapa task dijadwalkan di waktu yang sama dan task lain nunggu.

---

## 5. Maintenance Mode

Default: scheduled task **tidak** jalan kalau app lagi `php artisan down`.

```php
Schedule::command('app:important-task')
    ->daily()
    ->evenInMaintenanceMode();
```

---

## 6. Output Task

| Method | Keterangan |
|---|---|
| `->sendOutputTo($filePath)` | Simpan output ke file (overwrite) |
| `->appendOutputTo($filePath)` | Simpan output ke file (append) |
| `->emailOutputTo($address)` | Kirim output via email (butuh `sendOutputTo`/`appendOutputTo` dulu) |
| `->emailOutputOnFailure($address)` | Email hanya kalau task gagal (exit code ≠ 0) |

---

## 7. Hooks — Before & After Task

| Method | Keterangan |
|---|---|
| `->before(function () {})` | Jalan sebelum task dieksekusi |
| `->after(function () {})` | Jalan setelah task selesai |
| `->onSuccess(function () {})` | Jalan kalau task sukses |
| `->onFailure(function () {})` | Jalan kalau task gagal |

Contoh:
```php
Schedule::command('emails:send')
    ->daily()
    ->onSuccess(fn () => Log::info('Email terkirim'))
    ->onFailure(fn () => Log::error('Email gagal terkirim'));
```

---

## 8. Ping URL (Webhook saat before/after)

| Method | Keterangan |
|---|---|
| `->pingBefore($url)` | Ping URL sebelum task jalan |
| `->pingOnSuccess($url)` | Ping URL kalau task sukses |
| `->pingOnFailure($url)` | Ping URL kalau task gagal |
| `->pingBeforeIf($condition, $url)` | Ping kondisional sebelum |
| `->pingOnSuccessIf($condition, $url)` | Ping kondisional kalau sukses |
| `->pingOnFailureIf($condition, $url)` | Ping kondisional kalau gagal |

Butuh package `guzzlehttp/guzzle` terinstall.

---

## 9. Menjadwalkan Selain Artisan Command

Selain `Schedule::command()`, bisa juga:

```php
// Closure
Schedule::call(function () {
    DB::table('recent_users')->delete();
})->daily();

// Job (bisa queueable)
Schedule::job(new DeleteRecentUsers)->daily();
Schedule::job(new DeleteRecentUsers, 'reports')->daily(); // custom queue

// Shell command
Schedule::exec('node /home/forge/script.js')->daily();
```

---

## 10. Cek Jadwal & Testing

```bash
# Lihat semua scheduled task beserta jadwal & waktu jalan berikutnya
php artisan schedule:list

# Jalanin scheduler manual sekali (biasanya di-cron tiap menit)
php artisan schedule:run

# Test satu task tertentu langsung tanpa nunggu jadwal
php artisan schedule:test
```

Cron server (wajib, cuma 1 baris ini):
```
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 11. Named Schedules (Laravel 11+, opsional)

Kalau mau grouping jadwal (misal beda konfigurasi per grup):

```php
Schedule::command('emails:send')
    ->daily()
    ->name('send-emails-schedule'); // wajib diisi kalau pakai closure/job supaya unique
```

---

### Referensi
Semua di atas based on fitur Task Scheduling Laravel (`Illuminate\Console\Scheduling`). Kalau ada versi Laravel tertentu yang lo pakai (10 vs 11+), beda dikit di lokasi filenya doang — behaviornya sama.