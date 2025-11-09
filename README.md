# ⚡ Laravel ObjectId

Elegant, fast **ObjectId** generator for Laravel models —
with automatic ID assignment, migration macro, and helper function.

[![Tests](https://github.com/wooserv/laravel-objectid/actions/workflows/tests.yml/badge.svg)](https://github.com/wooserv/laravel-objectid/actions/workflows/tests.yml)
[![Packagist](https://img.shields.io/packagist/v/wooserv/laravel-objectid.svg)](https://packagist.org/packages/wooserv/laravel-objectid)
[![License](https://img.shields.io/github/license/wooserv/laravel-objectid.svg)](https://github.com/wooserv/laravel-objectid/blob/main/LICENSE)

> Laravel ObjectId brings the power and efficiency of MongoDB-style ObjectIds to your Eloquent models — with no database dependency.
>
> It’s a **drop-in, ultra-fast unique ID system** that fits seamlessly into Laravel’s model lifecycle.
>
> With this package, you can:
>
> * Automatically assign 24-character hex ObjectIds to your models.
> * Use `$table->objectId()` directly in your migrations.
> * Generate IDs anywhere using the global `objectid()` helper.
> * Enjoy compact, sortable, timestamp-encoded identifiers — **3× faster than UUIDs**.
>
> Built for performance, readability, and developer happiness.
>
> ---
>
> **Perfect for:**
>
> * Large-scale Laravel apps
> * Multi-database systems
> * UUID/ULID replacements
> * Caching and indexing optimization

---

## Installation

```bash
composer require wooserv/laravel-objectid
```

This package is auto-discovered by Laravel.
No manual provider registration needed.

---

## Usage

### 1. Model

```php
use WooServ\LaravelObjectId\Concerns\HasObjectIds;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasObjectIds;
}
```

Now every new record gets a unique ObjectId automatically:

```php
$post = Post::create(['name' => 'Hello World']);
echo $post->id; // e.g. 6730b6a0d8a28f890b7c9f40
```

---

### 2. Migration Macro

The service provider automatically adds a new macro to the schema builder:

```php
Schema::create('posts', function (Blueprint $table) {
    $table->objectId(); // Creates string(24) primary key
    $table->string('name');
    $table->timestamps();
});
```

Optionally:

```php
$table->objectId('uuid', false); // custom column, not primary
```

---

### 3. Helper Function

```php
$id = objectid(); // returns 24-char hex ObjectId string
```

---

## Why Laravel ObjectId?

| Feature            | ObjectId         | UUID              | ULID        |
| ------------------ | ---------------- | ----------------- | ----------- |
| Sortable           | ✅ Yes            | ❌ No              | ✅ Yes       |
| Length             | 24 chars         | 36 chars          | 26 chars    |
| Contains Timestamp | ✅ Yes            | ❌ No              | ✅ Yes       |
| Index Friendly     | ✅ Yes            | ⚠️ Larger Indexes | ✅ Yes       |
| Collision Chance   | 🔒 Extremely Low | 🔒 Very Low       | 🔒 Very Low |

---

## Testing

```bash
composer test
```

Runs a full PHPUnit suite using an in-memory SQLite database.

---

## ⚡️ Benchmark Results

All benchmarks were executed on PHP 8.4 using in-memory SQLite
and 10000 iterations per test on a local machine.

### ObjectId Generation Speed

```
Laravel ObjectId Benchmark (10000 iterations)
----------------------------------------------------------
ObjectId             : 0.412 µs per ID
objectid() helper    : 0.417 µs per ID
UUID                 : 1.283 µs per ID
ULID                 : 1.147 µs per ID
----------------------------------------------------------
Fastest: ObjectId
```

**Result:** `ObjectId` is roughly **3× faster** than UUID and **~2.7× faster** than ULID.

---

### Database Insert Performance

```
Database Insert Benchmark (1000 inserts)
----------------------------------------------------------
ObjectId   : 14.78 ms total (0.015 ms/insert)
UUID       : 15.48 ms total (0.015 ms/insert)
ULID       : 15.17 ms total (0.015 ms/insert)
----------------------------------------------------------
```

**Result:** Real-world insert performance is effectively identical across ID types,
but **ObjectId maintains slightly lower overhead** during generation and indexing.

---

### Summary

| Metric             | ObjectId       | UUID         | ULID        |
| ------------------ | -------------- | ------------ | ----------- |
| Generation Speed   | 🥇 **Fastest** | ⚪ Slow       | ⚪ Medium    |
| Insert Speed       | ⚡ Very Fast    | ⚡ Very Fast  | ⚡ Very Fast |
| Length             | 24 chars       | 36 chars     | 26 chars    |
| Sortable           | ✅ Yes          | ❌ No         | ✅ Yes       |
| DB Index Size      | 🔹 Small       | 🔸 Large     | 🔹 Small    |
| Human Readable     | ⚪ Hex          | ⚪ Hyphenated | ⚪ Base32    |
| Timestamp Embedded | ✅ Yes          | ❌ No         | ✅ Yes       |

---

**Conclusion:**
`Laravel ObjectId` provides *faster generation*, *compact indexes*,
and *timestamp-friendly IDs* — ideal for large-scale Laravel applications.

---

## License

MIT © [WooServ](https://www.wooserv.com/)