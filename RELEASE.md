# Release Notes

## [Unreleased]

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
