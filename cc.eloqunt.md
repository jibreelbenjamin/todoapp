# Laravel Eloquent — Dokumentasi Query Lengkap

Semua contoh pakai model `User` sebagai ilustrasi. Bisa diganti model apa aja.

---

## 1. Retrieving Data (Ambil Data)

| Method | Keterangan |
|---|---|
| `User::all()` | Ambil semua record |
| `User::get()` | Ambil hasil query (bisa dirangkai where dll) |
| `User::first()` | Ambil 1 record pertama |
| `User::firstOrFail()` | Ambil 1 record pertama, throw 404 kalau kosong |
| `User::find(1)` | Cari by primary key |
| `User::find([1, 2, 3])` | Cari banyak by primary key |
| `User::findOrFail(1)` | Cari by PK, throw 404 kalau ga ada |
| `User::firstOrNew(['email' => $email])` | Ambil pertama yang cocok, atau bikin instance baru (belum disave) |
| `User::firstOrCreate(['email' => $email])` | Ambil pertama yang cocok, atau bikin & save baru |
| `User::firstWhere('email', $email)` | Shortcut `where(...)->first()` |
| `User::sole()` | Ambil 1 record, throw error kalau hasil 0 atau lebih dari 1 |
| `User::exists()` | Cek apakah ada record yang cocok (return bool) |
| `User::doesntExist()` | Kebalikannya |
| `User::count()` | Hitung jumlah record |

---

## 2. Where Clauses

| Method | Keterangan |
|---|---|
| `->where('status', 'active')` | Where standar (default `=`) |
| `->where('votes', '>', 100)` | Where dengan operator |
| `->where('name', 'like', '%budi%')` | Like |
| `->orWhere('status', 'pending')` | OR where |
| `->whereNot('status', 'banned')` | NOT where |
| `->whereIn('id', [1, 2, 3])` | WHERE IN |
| `->whereNotIn('id', [1, 2, 3])` | WHERE NOT IN |
| `->whereBetween('votes', [1, 100])` | BETWEEN |
| `->whereNotBetween('votes', [1, 100])` | NOT BETWEEN |
| `->whereNull('deleted_at')` | WHERE IS NULL |
| `->whereNotNull('email_verified_at')` | WHERE IS NOT NULL |
| `->whereDate('created_at', '2024-01-01')` | Filter berdasar tanggal |
| `->whereMonth('created_at', 12)` | Filter berdasar bulan |
| `->whereDay('created_at', 25)` | Filter berdasar tanggal (hari) |
| `->whereYear('created_at', 2024)` | Filter berdasar tahun |
| `->whereTime('created_at', '14:00:00')` | Filter berdasar jam |
| `->whereColumn('updated_at', '>', 'created_at')` | Bandingin 2 kolom |
| `->whereJsonContains('options->languages', 'id')` | Filter kolom JSON |
| `->whereLike('name', '%budi%')` | Alias like (Laravel 11+) |
| `->where(fn ($q) => $q->where('a', 1)->orWhere('b', 2))` | Nested/grouped where |
| `->whereAny(['name', 'email'], 'like', '%budi%')` | Cek beberapa kolom sekaligus (OR) |
| `->whereAll(['a', 'b'], '>', 0)` | Cek beberapa kolom sekaligus (AND) |

Contoh nested where (penting biar logic AND/OR ga ketuker):
```php
User::where('status', 'active')
    ->where(function ($query) {
        $query->where('votes', '>', 100)
              ->orWhere('name', 'like', 'B%');
    })
    ->get();
```

---

## 3. Ordering, Grouping, Limit

| Method | Keterangan |
|---|---|
| `->orderBy('name', 'asc')` | Urutkan ascending |
| `->orderBy('created_at', 'desc')` | Urutkan descending |
| `->orderByDesc('created_at')` | Shortcut desc |
| `->latest()` | Urutkan `created_at` desc (bisa isi kolom lain: `latest('updated_at')`) |
| `->oldest()` | Urutkan `created_at` asc |
| `->inRandomOrder()` | Urutan random |
| `->reorder()` | Hapus semua orderBy sebelumnya |
| `->groupBy('status')` | Group by |
| `->having('total', '>', 100)` | Having (dipakai bareng groupBy) |
| `->havingBetween('total', [1, 100])` | Having between |
| `->skip(10)->take(5)` | Skip & limit (alias `offset`/`limit`) |
| `->limit(5)` | Batasi jumlah hasil |
| `->offset(10)` | Lewati sekian data |

---

## 4. Aggregates

| Method | Keterangan |
|---|---|
| `->count()` | Jumlah row |
| `->max('price')` | Nilai maksimum |
| `->min('price')` | Nilai minimum |
| `->avg('price')` | Rata-rata |
| `->sum('price')` | Total |
| `->exists()` | Ada/tidak |

---

## 5. Select & Kolom Spesifik

| Method | Keterangan |
|---|---|
| `->select('name', 'email')` | Ambil kolom tertentu aja |
| `->addSelect('votes')` | Tambah kolom ke select yang udah ada |
| `->distinct()` | Ambil data unik |
| `->pluck('name')` | Ambil 1 kolom aja jadi collection |
| `->pluck('name', 'id')` | Ambil 2 kolom jadi key-value collection |
| `->value('name')` | Ambil 1 value dari 1 kolom, row pertama |

---

## 6. Joins

| Method | Keterangan |
|---|---|
| `->join('orders', 'users.id', '=', 'orders.user_id')` | Inner join |
| `->leftJoin('orders', 'users.id', '=', 'orders.user_id')` | Left join |
| `->rightJoin(...)` | Right join |
| `->crossJoin('roles')` | Cross join |
| `->joinSub($subquery, 'sub', 'users.id', '=', 'sub.user_id')` | Join ke subquery |

---

## 7. Relationships (Eloquent)

### Tipe relasi
| Method (di dalam Model) | Keterangan |
|---|---|
| `hasOne(Related::class)` | 1 - 1 |
| `belongsTo(Related::class)` | Kebalikan hasOne / hasMany |
| `hasMany(Related::class)` | 1 - banyak |
| `belongsToMany(Related::class)` | Banyak - banyak (pivot table) |
| `hasOneThrough(...)` | 1 - 1 lewat perantara |
| `hasManyThrough(...)` | 1 - banyak lewat perantara |
| `morphOne(...)` / `morphMany(...)` | Polymorphic 1 / banyak |
| `morphToMany(...)` / `morphedByMany(...)` | Polymorphic banyak - banyak |

### Eager Loading (hindari N+1 query)
| Method | Keterangan |
|---|---|
| `->with('posts')` | Eager load relasi |
| `->with(['posts', 'comments'])` | Eager load beberapa relasi |
| `->with('posts.comments')` | Nested eager load |
| `->with(['posts' => fn ($q) => $q->where('status', 'published')])` | Eager load dengan constraint |
| `->withCount('posts')` | Hitung jumlah relasi tanpa load semua datanya |
| `->withSum('orders', 'amount')` | Sum kolom dari relasi |
| `->withMax('orders', 'amount')` | Max dari relasi |
| `->withMin('orders', 'amount')` | Min dari relasi |
| `->withAvg('orders', 'amount')` | Avg dari relasi |
| `->withExists('posts')` | Cek exist relasi tanpa load |
| `$user->load('posts')` | Lazy eager load (setelah model diambil) |
| `$user->loadMissing('posts')` | Load hanya kalau belum di-load |

### Filter berdasarkan relasi
| Method | Keterangan |
|---|---|
| `->has('posts')` | Punya minimal 1 relasi |
| `->has('posts', '>=', 3)` | Punya minimal 3 relasi |
| `->doesntHave('posts')` | Ga punya relasi sama sekali |
| `->whereHas('posts', fn ($q) => $q->where('status', 'published'))` | Filter dengan constraint di relasi |
| `->whereDoesntHave('posts', fn ($q) => ...)` | Kebalikannya |
| `->orWhereHas(...)` / `->orWhereDoesntHave(...)` | Versi OR |

---

## 8. Insert, Update, Delete

| Method | Keterangan |
|---|---|
| `User::create([...])` | Insert baru (butuh `$fillable`/`$guarded` di model) |
| `User::insert([...])` | Insert langsung (bulk, ga trigger event/timestamps otomatis) |
| `$user->save()` | Simpan perubahan/instance baru |
| `$user->update([...])` | Update instance yang udah ada |
| `User::where(...)->update([...])` | Mass update |
| `$user->delete()` | Hapus 1 record |
| `User::destroy(1)` | Hapus by ID |
| `User::destroy([1, 2, 3])` | Hapus banyak by ID |
| `User::where(...)->delete()` | Mass delete |
| `$user->fill([...])` | Isi atribut tanpa save |
| `User::updateOrCreate(['email' => $e], ['name' => $n])` | Update kalau ada, create kalau ga ada |
| `$user->increment('votes')` | Tambah nilai kolom |
| `$user->decrement('votes', 5)` | Kurangi nilai kolom |
| `$user->restore()` | Restore soft-deleted record |
| `User::withTrashed()->get()` | Include yang soft-deleted |
| `User::onlyTrashed()->get()` | Hanya yang soft-deleted |
| `$user->forceDelete()` | Hapus permanen (bypass soft delete) |

---

## 9. Chunking & Lazy (buat data gede)

| Method | Keterangan |
|---|---|
| `->chunk(200, function ($users) {...})` | Proses per 200 row, hemat memory |
| `->chunkById(200, function ($users) {...})` | Sama, tapi lebih aman kalau data ikut berubah pas loop |
| `->lazy()` | Return LazyCollection, iterate row per row |
| `->lazyById()` | Sama, based on ID (lebih aman) |
| `->cursor()` | Iterate pakai 1 query aja (irit query, tapi tetep load per row) |

---

## 10. Query Scopes

### Local Scope (di dalam Model)
```php
// Model
public function scopeActive($query)
{
    return $query->where('status', 'active');
}

public function scopeOfType($query, $type)
{
    return $query->where('type', $type);
}
```
Pemakaian:
```php
User::active()->get();
User::ofType('admin')->get();
```

### Global Scope
```php
class ActiveScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where('status', 'active');
    }
}

// Di Model, override booted()
protected static function booted(): void
{
    static::addGlobalScope(new ActiveScope);
}
```
Bypass global scope: `User::withoutGlobalScope(ActiveScope::class)->get()` atau `->withoutGlobalScopes()`.

---

## 11. Accessor, Mutator, Casting (bonus, sering kepake bareng query)

```php
// Accessor (Laravel 9+)
protected function fullName(): Attribute
{
    return Attribute::make(
        get: fn ($value, $attributes) => "{$attributes['first_name']} {$attributes['last_name']}",
    );
}

// Casting
protected $casts = [
    'options' => 'array',
    'is_admin' => 'boolean',
    'birthday' => 'date',
];
```

---

## 12. Raw Expressions (kalau butuh SQL manual)

| Method | Keterangan |
|---|---|
| `->selectRaw('count(*) as total')` | Select raw |
| `->whereRaw('votes > ?', [100])` | Where raw |
| `->orderByRaw('field(status, "active", "pending")')` | Order raw |
| `->havingRaw('total > ?', [100])` | Having raw |
| `DB::raw('...')` | Ekspresi raw generik, bisa disisipkan di method manapun |

---

## 13. Debugging Query

| Method | Keterangan |
|---|---|
| `->toSql()` | Lihat SQL string yang dihasilkan (tanpa binding) |
| `->dd()` | Dump query result & mati (die) |
| `->dump()` | Dump tapi lanjut eksekusi |
| `->ddRawSql()` | Dump SQL final dengan binding udah disubstitusi |
| `DB::enableQueryLog()` lalu `DB::getQueryLog()` | Log semua query yang jalan |

---

### Referensi
Semua di atas based on fitur Eloquent ORM & Query Builder Laravel (`Illuminate\Database\Eloquent` & `Illuminate\Database\Query`). Behavior sama across versi Laravel 10/11/12, cuma beberapa method baru (`whereLike`, `whereAny`, `whereAll`, `sole`) khusus 11+.