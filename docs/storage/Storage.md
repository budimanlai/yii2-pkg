# Storage

**Namespace:** `budimanlai\yii2pkg\storage`  
**Extends:** `yii\base\Component`  
**File:** `src/storage/Storage.php`

Facade utama untuk operasi file storage. Class ini menjadi satu-satunya titik akses untuk upload, download URL, dan manajemen file, terlepas dari driver yang digunakan (local atau S3). Driver yang aktif ditentukan melalui konfigurasi komponen Yii2.

## Driver yang Tersedia

| Value `driver` | Class | Keterangan |
|---|---|---|
| `'local'` | `LocalStorage` | Menyimpan file ke local filesystem |
| `'s3'` | `S3Storage` | Menyimpan file ke AWS S3 / S3-compatible storage |

## Konfigurasi

Daftarkan sebagai komponen di `config/main.php`:

```php
'components' => [
    'storage' => [
        'class' => 'budimanlai\yii2pkg\storage\Storage',
        'driver' => 'local',
        'config' => [
            'upload_directory' => '/var/www/uploads',
            'baseUrl' => 'https://example.com/uploads',
        ],
    ],
],
```

Untuk S3:

```php
'components' => [
    'storage' => [
        'class' => 'budimanlai\yii2pkg\storage\Storage',
        'driver' => 's3',
        'config' => [
            'credentials' => ['key' => 'ACCESS_KEY', 'secret' => 'SECRET_KEY'],
            'endpoint'    => 'https://s3.example.com',
            'region'      => 'ap-southeast-1',
            'bucket'      => 'my-bucket',
        ],
    ],
],
```

---

## Properties

| Property | Tipe | Deskripsi |
|---|---|---|
| `$driver` | `string` | Nama driver yang digunakan (`'local'` atau `'s3'`) |
| `$config` | `array` | Konfigurasi yang diteruskan ke driver |

---

## Methods

### `getDriverName()`

```php
public function getDriverName(): string
```

Mengembalikan nama driver storage yang sedang aktif.

**Return:** `string` — Nama driver, misalnya `'local'` atau `'s3'`.

---

### `upload()`

```php
public function upload(string $file, string $path): void
```

Mengupload file ke storage menggunakan driver yang aktif.

**Parameter**

| Nama | Tipe | Deskripsi |
|---|---|---|
| `$file` | `string` | Absolute path file sumber di local disk |
| `$path` | `string` | Path tujuan di dalam storage (contoh: `'images/photo.jpg'`) |

**Contoh**

```php
Yii::$app->storage->upload('/tmp/uploaded.jpg', 'avatars/user-1.jpg');
```

---

### `getPublicURL()`

```php
public function getPublicURL(string $file): string
```

Mendapatkan URL publik dari sebuah file yang tersimpan di storage.

**Parameter**

| Nama | Tipe | Deskripsi |
|---|---|---|
| `$file` | `string` | Path file di dalam storage |

**Return:** `string` — URL publik file.

**Contoh**

```php
$url = Yii::$app->storage->getPublicURL('avatars/user-1.jpg');
// → 'https://example.com/uploads/avatars/user-1.jpg'
```

---

### `getPrivateURL()`

```php
public function getPrivateURL(string $file): string
```

Mendapatkan URL privat dari sebuah file. Untuk local storage, hasilnya sama dengan `getPublicURL()`. Untuk S3, menghasilkan pre-signed URL yang hanya berlaku selama durasi yang dikonfigurasi.

**Parameter**

| Nama | Tipe | Deskripsi |
|---|---|---|
| `$file` | `string` | Path file di dalam storage |

**Return:** `string` — URL privat atau pre-signed URL.

---

### `isExists()`

```php
public function isExists(string $file): bool
```

Mengecek apakah sebuah file ada di storage.

**Parameter**

| Nama | Tipe | Deskripsi |
|---|---|---|
| `$file` | `string` | Path file di dalam storage |

**Return:** `bool` — `true` jika file ada, `false` jika tidak.

---

### `delete()`

```php
public function delete(string $file): void
```

Menghapus sebuah file dari storage.

**Parameter**

| Nama | Tipe | Deskripsi |
|---|---|---|
| `$file` | `string` | Path file di dalam storage |
