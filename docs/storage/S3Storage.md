# S3Storage

**Namespace:** `budimanlai\yii2pkg\storage`  
**Extends:** `yii\base\Component`  
**File:** `src/storage/S3Storage.php`  
**Requires:** `aws/aws-sdk-php ^3.0`

Driver storage untuk menyimpan file ke AWS S3 atau object storage yang kompatibel dengan S3 (seperti MinIO, Cloudflare R2, DigitalOcean Spaces, dll.). Digunakan secara internal oleh class [Storage](Storage.md) saat `driver` dikonfigurasi sebagai `'s3'`.

## Konfigurasi

```php
'storage' => [
    'class' => 'budimanlai\yii2pkg\storage\Storage',
    'driver' => 's3',
    'config' => [
        'credentials' => [
            'key'    => 'YOUR_ACCESS_KEY',
            'secret' => 'YOUR_SECRET_KEY',
        ],
        'endpoint'   => 'https://s3.ap-southeast-1.amazonaws.com',
        'region'     => 'ap-southeast-1',
        'bucket'     => 'my-bucket',
        'expired'    => '+1 hours',

        // Opsional: ganti internal endpoint dengan URL publik di response
        'public_endpoint_url' => 'http://minio:9000',
        'public_url'          => 'https://cdn.example.com',

        // Opsional: ganti endpoint di pre-signed URL dengan URL yang bisa diakses client
        'private_endpoint_url' => 'http://minio:9000',
        'private_url'          => 'https://private.example.com',
    ],
],
```

---

## Properties

| Property | Tipe | Default | Deskripsi |
|---|---|---|---|
| `$credentials` | `array` | `['key' => '', 'secret' => '']` | AWS access key dan secret key |
| `$endpoint` | `string` | — | URL endpoint S3 |
| `$region` | `string` | — | Region S3 (contoh: `'ap-southeast-1'`) |
| `$use_path_style_endpoint` | `bool` | `true` | Gunakan path-style URL (wajib untuk MinIO) |
| `$debug` | `bool` | `false` | Aktifkan debug mode pada S3Client |
| `$bucket` | `string` | — | Nama bucket S3 |
| `$public_endpoint_url` | `?string` | `null` | Internal endpoint yang akan diganti di public URL |
| `$public_url` | `?string` | `null` | URL publik pengganti `$public_endpoint_url` |
| `$private_endpoint_url` | `?string` | `null` | Internal endpoint yang akan diganti di pre-signed URL |
| `$private_url` | `?string` | `null` | URL pengganti `$private_endpoint_url` pada pre-signed URL |
| `$expired` | `string` | `'+1 minutes'` | Durasi berlakunya pre-signed URL (format `strtotime`) |

---

## Methods

### `upload()`

```php
public function upload(string $source, string $destination, string $acl = 'public-read'): void
```

Mengupload file ke S3 bucket menggunakan `putObject`.

**Parameter**

| Nama | Tipe | Default | Deskripsi |
|---|---|---|---|
| `$source` | `string` | — | Absolute path file sumber di local disk |
| `$destination` | `string` | — | Object key (path) di S3 bucket |
| `$acl` | `string` | `'public-read'` | ACL S3 untuk object yang diupload |

**Throws:** `\Exception` jika upload gagal.

**Contoh**

```php
// Upload sebagai public
$storage->upload('/tmp/photo.jpg', 'avatars/user-1.jpg');

// Upload sebagai private
$storage->upload('/tmp/doc.pdf', 'documents/contract.pdf', 'private');
```

---

### `getPublicURL()`

```php
public function getPublicURL(string $file, string $defaultFile = ''): string
```

Mendapatkan URL publik dari sebuah object S3. Jika `$public_endpoint_url` dan `$public_url` dikonfigurasi, internal endpoint di URL akan diganti secara otomatis.

**Parameter**

| Nama | Tipe | Default | Deskripsi |
|---|---|---|---|
| `$file` | `string` | — | Object key (path) di S3 bucket |
| `$defaultFile` | `string` | `''` | Nilai fallback jika `$file` kosong atau terjadi error |

**Return:** `string` — URL publik object, atau `$defaultFile` jika gagal.

**Contoh**

```php
$url = $storage->getPublicURL('avatars/user-1.jpg');
// → 'https://cdn.example.com/my-bucket/avatars/user-1.jpg'

$url = $storage->getPublicURL('', '/images/default-avatar.png');
// → '/images/default-avatar.png'
```

---

### `getPrivateURL()`

```php
public function getPrivateURL(string $file, string $defaultFile = ''): string
```

Menghasilkan pre-signed URL untuk mengakses object S3 secara sementara (private). URL berlaku selama durasi yang ditentukan oleh `$expired`. Berguna untuk object yang ACL-nya `private` agar tetap bisa diakses oleh user tertentu.

**Parameter**

| Nama | Tipe | Default | Deskripsi |
|---|---|---|---|
| `$file` | `string` | — | Object key (path) di S3 bucket |
| `$defaultFile` | `string` | `''` | Nilai fallback jika `$file` kosong atau terjadi error |

**Return:** `string` — Pre-signed URL yang berlaku sementara, atau `$defaultFile` jika gagal.

**Contoh**

```php
$url = $storage->getPrivateURL('documents/contract.pdf');
// → 'https://private.example.com/my-bucket/documents/contract.pdf?X-Amz-Signature=...'
```

---

### `isExists()`

```php
public function isExists(string $file): bool
```

Mengecek apakah sebuah object ada di S3 bucket menggunakan `headObject`.

**Parameter**

| Nama | Tipe | Deskripsi |
|---|---|---|
| `$file` | `string` | Object key (path) di S3 bucket |

**Return:** `bool` — `true` jika object ada, `false` jika tidak ada atau terjadi error.

---

### `delete()`

```php
public function delete(string $file): bool
```

Menghapus sebuah object dari S3 bucket.

**Parameter**

| Nama | Tipe | Deskripsi |
|---|---|---|
| `$file` | `string` | Object key (path) di S3 bucket |

**Return:** `bool` — `true` jika berhasil, `false` jika terjadi error.
