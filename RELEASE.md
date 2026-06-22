# Release Notes

## v1.1.2 — 2026-06-23

### Added
- `Api3rdBase` — tambah method `put()` untuk HTTP PUT request (full resource replacement)

### Changed
- Semua file PHP diupdate komentar mengikuti standar PHPDoc:
  - Class docblock dengan `@package` dan `@author` di semua class/trait
  - Property docblock dengan `@var type description` di semua property
  - Method docblock lengkap dengan `@param`, `@return`, `@throws`, dan `{@see}` cross-reference
  - Inline single-line docblock diexpand ke format multi-line yang proper
  - Header comment `/* */` lama di `QueryHelper`, `LocalStorage`, `S3Storage` dikonversi ke class docblock `/** */`

---

## v1.1.1 — 2026-06-23

### Changed
- Namespace `budimanlai\yii2pkg\models` diubah menjadi `budimanlai\yii2pkg\library` (**breaking change**)
  - Folder `src/models/` diubah menjadi `src/library/`
  - Folder `docs/models/` diubah menjadi `docs/library/`
  - Update semua referensi namespace di class `Api3rdBase`

> **Migration:** Ganti semua `use budimanlai\yii2pkg\models\Api3rdBase;` menjadi `use budimanlai\yii2pkg\library\Api3rdBase;`

## v1.1.0 — 2026-06-15

### Added
- `QueryHelper` — helper untuk operasi database raw SQL
  - `queryOne`, `queryAll`, `queryScalar`, `queryExecute`
  - `queryInsert`, `queryUpdate`, `queryBatchInsert`
  - `queryForUpdate` — select dengan row locking (`FOR UPDATE`)
  - `isExists` — cek keberadaan record
  - `getErrorModel` — ekstrak pesan error dari Yii2 model
- `BaseController` *(trait)* — shortcut query database dan response helper untuk controller
  - Wrapper method untuk semua operasi `QueryHelper`
  - `asError` — throw `BadRequestHttpException` dengan pesan kustom
  - `asErrorModel` — throw `BadRequestHttpException` dari error validasi model
  - `requiredField` — validasi keberadaan field wajib di request body
- `StringHelper` — tambahan method baru:
  - `normalizePhoneNumber` — normalisasi nomor telepon ke format `62xxx`
  - `generateTrxID` — generate ID transaksi unik berbasis timestamp
  - `getErrorModel` — ekstrak pesan error dari Yii2 model
  - `deleteFile` — hapus file dari server berdasarkan path relatif
  - `generateRandomString` — generate string acak alphanumeric

## v1.0.0 — 2025-12-25

Initial release.

### Added
- `StringHelper` — helper normalisasi string (`normalizeString`, `seoString`)
- `Storage` — facade storage dengan dukungan multi-driver (local / S3)
- `LocalStorage` — driver penyimpanan file ke local filesystem
- `S3Storage` — driver penyimpanan file ke AWS S3 / S3-compatible storage
  - Support pre-signed URL dengan durasi yang dapat dikonfigurasi via property `$expired`
  - Support penggantian internal endpoint dengan public/private URL
- `Api3rdBase` — base class untuk integrasi third-party REST API (GET, POST, PATCH, DELETE, form upload)
  - Auto-logging request dan response ke tabel `api_3rd_log`
