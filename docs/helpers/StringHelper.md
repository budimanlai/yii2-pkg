# StringHelper

**Namespace:** `budimanlai\yii2pkg\helpers`  
**Extends:** `yii\helpers\StringHelper`  
**File:** `src/helpers/StringHelper.php`

Ekstensi dari `yii\helpers\StringHelper` bawaan Yii2 dengan tambahan method untuk normalisasi dan konversi string. Semua method dari class parent tetap tersedia.

## Penggunaan

```php
use budimanlai\yii2pkg\helpers\StringHelper;
```

---

## Methods

### `normalizeString()`

```php
public static function normalizeString(string $string): string
```

Menghapus semua karakter selain huruf dan angka dari sebuah string.

**Parameter**

| Nama | Tipe | Deskripsi |
|---|---|---|
| `$string` | `string` | String yang akan dinormalisasi |

**Return:** `string` — String yang hanya mengandung karakter `A-Z`, `a-z`, dan `0-9`.

**Contoh**

```php
StringHelper::normalizeString('Hello, World! 123');
// → 'HelloWorld123'

StringHelper::normalizeString('user@email.com');
// → 'useremailcom'
```

---

### `seoString()`

```php
public static function seoString(string $string): string
```

Mengkonversi string menjadi format SEO-friendly dengan cara mengubah ke huruf kecil dan menghapus semua karakter non-alphanumeric (simbol, spasi, tanda hubung, dll.).

**Parameter**

| Nama | Tipe | Deskripsi |
|---|---|---|
| `$string` | `string` | String yang akan dikonversi |

**Return:** `string` — String SEO-friendly dalam huruf kecil, atau string kosong jika input kosong.

**Contoh**

```php
StringHelper::seoString('Hello World!');
// → 'helloworld'

StringHelper::seoString('Produk Terbaru 2025');
// → 'produkterbaru2025'

StringHelper::seoString('');
// → ''
```
