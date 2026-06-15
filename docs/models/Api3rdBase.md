# Api3rdBase

**Namespace:** `budimanlai\yii2pkg\models`  
**File:** `src/models/Api3rdBase.php`  
**Requires:** `yiisoft/yii2-httpclient ^2.0`

Base class untuk integrasi dengan third-party REST API. Class ini menyediakan method HTTP standar (GET, POST, PATCH, DELETE) sekaligus mencatat setiap request dan response ke tabel database `api_3rd_log` untuk keperluan audit dan debugging.

Gunakan dengan cara meng-extend class ini untuk setiap integrasi API:

```php
use budimanlai\yii2pkg\models\Api3rdBase;

class PaymentApi extends Api3rdBase {
    public string $baseUrl = 'https://api.payment-gateway.com';
    public string $category = 'payment';

    public function getHeaders(): array {
        return array_merge(parent::getHeaders(), [
            'Authorization' => 'Bearer ' . $this->api_key,
        ]);
    }

    public function charge(int $userId, int $amount): mixed {
        $this->user_id = $userId;
        return $this->post('/charge', ['amount' => $amount]);
    }
}
```

## Database Migration

Class ini memerlukan tabel `api_3rd_log` di database:

```sql
CREATE TABLE `api_3rd_log` (
    `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `category`         VARCHAR(100) NOT NULL,
    `method`           VARCHAR(10)  NOT NULL,
    `url`              TEXT         NOT NULL,
    `reff_id`          VARCHAR(100) DEFAULT NULL,
    `headers`          TEXT         DEFAULT NULL,
    `request_log`      TEXT         DEFAULT NULL,
    `response_log`     TEXT         DEFAULT NULL,
    `latency`          FLOAT        DEFAULT NULL,
    `created_datetime` DATETIME     NOT NULL,
    PRIMARY KEY (`id`)
);
```

---

## Properties

| Property | Tipe | Default | Deskripsi |
|---|---|---|---|
| `$baseUrl` | `string` | — | Base URL API (contoh: `'https://api.example.com'`) |
| `$api_key` | `string` | — | API key untuk autentikasi |
| `$category` | `string` | — | Nama kategori yang dicatat di log (contoh: `'payment'`, `'shipping'`) |
| `$path` | `string` | `'/'` | Path prefix yang ditambahkan sebelum setiap endpoint |
| `$user_id` | `int\|string` | — | ID user/entitas yang diasosiasikan dengan request (dicatat di log) |

---

## Methods

### `getHeaders()`

```php
public function getHeaders(): array
```

Mengembalikan default HTTP headers yang dikirim di setiap request. Override method ini di subclass untuk menambahkan header kustom seperti `Authorization`.

**Return:** `array` — Associative array `header => value`. Default: `['content-type' => 'application/json']`.

**Contoh override**

```php
public function getHeaders(): array {
    return array_merge(parent::getHeaders(), [
        'Authorization' => 'Bearer ' . $this->api_key,
        'X-App-Id'      => 'my-app',
    ]);
}
```

---

### `get()`

```php
public function get(string $endpoint, array $params = []): mixed
```

Mengirim GET request ke API. Query parameters di-encode dan ditambahkan ke URL secara otomatis.

**Parameter**

| Nama | Tipe | Default | Deskripsi |
|---|---|---|---|
| `$endpoint` | `string` | — | Path endpoint (contoh: `'/users'`, `'/orders/123'`) |
| `$params` | `array` | `[]` | Query parameters sebagai associative array |

**Return:** `mixed` — Data response yang sudah di-decode.  
**Throws:** `\Exception` jika request gagal atau API mengembalikan status non-2xx.

**Contoh**

```php
$result = $api->get('/orders', ['status' => 'pending', 'limit' => 10]);
// Request: GET https://api.example.com/orders?status=pending&limit=10
```

---

### `post()`

```php
public function post(string $endpoint, array $params): mixed
```

Mengirim POST request dengan body JSON ke API.

**Parameter**

| Nama | Tipe | Deskripsi |
|---|---|---|
| `$endpoint` | `string` | Path endpoint |
| `$params` | `array` | Request body sebagai associative array (dikirim sebagai JSON) |

**Return:** `mixed` — Data response yang sudah di-decode.  
**Throws:** `\Exception` jika request gagal atau API mengembalikan status non-2xx.

**Contoh**

```php
$result = $api->post('/orders', [
    'product_id' => 5,
    'quantity'   => 2,
]);
```

---

### `postForm()`

```php
public function postForm(string $endpoint, array $params): mixed
```

Mengirim POST request dengan body form-encoded (multipart/form-data). Gunakan method ini saat perlu mengirim file upload atau form fields.

**Parameter**

| Nama | Tipe | Deskripsi |
|---|---|---|
| `$endpoint` | `string` | Path endpoint |
| `$params` | `array` | Request body sebagai associative array (dikirim sebagai form data) |

**Return:** `mixed` — Data response yang sudah di-decode.  
**Throws:** `\Exception` jika request gagal atau API mengembalikan status non-2xx.

---

### `patch()`

```php
public function patch(string $endpoint, array $params = []): mixed
```

Mengirim PATCH request dengan body JSON ke API. Biasanya digunakan untuk update sebagian data (partial update).

**Parameter**

| Nama | Tipe | Default | Deskripsi |
|---|---|---|---|
| `$endpoint` | `string` | — | Path endpoint |
| `$params` | `array` | `[]` | Request body sebagai associative array |

**Return:** `mixed` — Data response yang sudah di-decode.  
**Throws:** `\Exception` jika request gagal atau API mengembalikan status non-2xx.

**Contoh**

```php
$result = $api->patch('/orders/123', ['status' => 'shipped']);
```

---

### `delete()`

```php
public function delete(string $endpoint, array $params = []): mixed
```

Mengirim DELETE request ke API.

**Parameter**

| Nama | Tipe | Default | Deskripsi |
|---|---|---|---|
| `$endpoint` | `string` | — | Path endpoint |
| `$params` | `array` | `[]` | Request body sebagai associative array |

**Return:** `mixed` — Data response yang sudah di-decode.  
**Throws:** `\Exception` jika request gagal atau API mengembalikan status non-2xx.

---

### `parseError()`

```php
public function parseError(array $data): string
```

Mengekstrak pesan error yang dapat dibaca manusia dari body response API. Mencari field `message` di root level terlebih dahulu, lalu `meta.message`.

**Parameter**

| Nama | Tipe | Deskripsi |
|---|---|---|
| `$data` | `array` | Response body yang sudah di-decode |

**Return:** `string` — Pesan error.

---

### `getHeaderReq()`

```php
public function getHeaderReq(): array
```

Mengembalikan headers yang dikirim pada request terakhir. Berguna untuk debugging.

**Return:** `array` — Headers request terakhir.

---

### `getRequest()`

```php
public function getRequest(): array
```

Mengembalikan params/body yang dikirim pada request terakhir.

**Return:** `array` — Body atau query params request terakhir.

---

### `getResponse()`

```php
public function getResponse(): mixed
```

Mengembalikan data response dari request terakhir.

**Return:** `mixed` — Data response terakhir yang sudah di-decode.

---

## Logging Methods

Method berikut digunakan secara internal untuk mencatat aktivitas ke tabel `api_3rd_log`. Dapat di-override di subclass jika perlu logika pencatatan yang berbeda.

### `addLog()`

```php
public function addLog(int|string $reff_id, string $method): int|string
```

Menyisipkan baris log baru ke `api_3rd_log` sebelum request dikirim.

**Parameter**

| Nama | Tipe | Deskripsi |
|---|---|---|
| `$reff_id` | `int\|string` | ID entitas yang diasosiasikan dengan request |
| `$method` | `string` | Nama HTTP method dalam huruf kecil (`'get'`, `'post'`, dll.) |

**Return:** `int|string` — ID baris log yang baru dibuat.

---

### `addResponse()`

```php
public function addResponse(int|string $log_id, mixed $response, ?float $latency = null): void
```

Mengupdate baris log dengan data response setelah request berhasil.

**Parameter**

| Nama | Tipe | Deskripsi |
|---|---|---|
| `$log_id` | `int\|string` | ID baris log dari `addLog()` |
| `$response` | `mixed` | Data response yang sudah di-decode |
| `$latency` | `?float` | Durasi request dalam detik |

---

### `addException()`

```php
public function addException(int|string $log_id, string $message, ?float $latency = null): void
```

Mengupdate baris log dengan pesan error ketika request gagal.

**Parameter**

| Nama | Tipe | Deskripsi |
|---|---|---|
| `$log_id` | `int\|string` | ID baris log dari `addLog()` |
| `$message` | `string` | Pesan exception |
| `$latency` | `?float` | Durasi request dalam detik |
