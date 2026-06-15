# LocalStorage

**Namespace:** `budimanlai\yii2pkg\storage`  
**Extends:** `yii\base\Component`  
**File:** `src/storage/LocalStorage.php`

Driver storage untuk menyimpan file ke local filesystem server. Digunakan secara internal oleh class [Storage](Storage.md) saat `driver` dikonfigurasi sebagai `'local'`.

## Konfigurasi

Tidak perlu dikonfigurasi langsung. Gunakan melalui [Storage](Storage.md):

```php
'storage' => [
    'class' => 'budimanlai\yii2pkg\storage\Storage',
    'driver' => 'local',
    'config' => [
        'upload_directory' => '/var/www/html/uploads',
        'baseUrl' => 'https://example.com/uploads',
    ],
],
```

---

## Properties

| Property | Tipe | Deskripsi |
|---|---|---|
| `$upload_directory` | `string` | Absolute path ke direktori penyimpanan file di server |
| `$baseUrl` | `string` | Base URL yang digunakan untuk menghasilkan URL publik file |

---

## Methods

### `upload()`

```php
public function upload(string $source, string $destination): void
```

Mengupload (menyalin) file ke direktori local. Jika direktori tujuan belum ada, akan dibuat secara otomatis (rekursif).

**Parameter**

| Nama | Tipe | Deskripsi |
|---|---|---|
| `$source` | `string` | Absolute path file sumber |
| `$destination` | `string` | Path relatif tujuan di dalam `$upload_directory` |

**Contoh**

```php
$storage->upload('/tmp/photo.jpg', 'avatars/user-1.jpg');
// File disimpan ke: /var/www/html/uploads/avatars/user-1.jpg
```

---

### `getPublicURL()`

```php
public function getPublicURL(string $file): string
```

Menghasilkan URL publik file dengan menggabungkan `$baseUrl` dan path file.

**Parameter**

| Nama | Tipe | Deskripsi |
|---|---|---|
| `$file` | `string` | Path relatif file di dalam storage |

**Return:** `string` — URL lengkap file.

**Contoh**

```php
$url = $storage->getPublicURL('avatars/user-1.jpg');
// → 'https://example.com/uploads/avatars/user-1.jpg'
```

---

### `getPrivateURL()`

```php
public function getPrivateURL(string $file): string
```

Mengembalikan URL file. Pada local storage, method ini identik dengan `getPublicURL()` karena tidak ada mekanisme pre-signed URL.

**Parameter**

| Nama | Tipe | Deskripsi |
|---|---|---|
| `$file` | `string` | Path relatif file di dalam storage |

**Return:** `string` — URL lengkap file.

---

### `isExists()`

```php
public function isExists(string $file): bool
```

Mengecek apakah file ada di direktori storage local menggunakan `file_exists()`.

**Parameter**

| Nama | Tipe | Deskripsi |
|---|---|---|
| `$file` | `string` | Path relatif file di dalam storage |

**Return:** `bool` — `true` jika file ada, `false` jika tidak.

---

### `delete()`

```php
public function delete(string $file): void
```

Menghapus file dari direktori storage local. Tidak melakukan apa-apa jika file tidak ditemukan.

**Parameter**

| Nama | Tipe | Deskripsi |
|---|---|---|
| `$file` | `string` | Path relatif file di dalam storage |
