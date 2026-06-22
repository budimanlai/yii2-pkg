# yii2-pkg

Yii2 extension package berisi kumpulan helper, storage driver, trait, dan base class yang dapat digunakan kembali di berbagai project Yii2.

## Requirement

- PHP >= 8.0
- Yii2 ~2.0

## Instalasi

```bash
composer require budimanlai/yii2-pkg
```

## Daftar Class

### Helpers

| Class | Namespace | Deskripsi |
|---|---|---|
| [StringHelper](docs/helpers/StringHelper.md) | `budimanlai\yii2pkg\helpers` | Ekstensi dari `yii\helpers\StringHelper` dengan tambahan method normalisasi string, phone number, dan utility |
| [QueryHelper](docs/helpers/QueryHelper.md) | `budimanlai\yii2pkg\helpers` | Helper untuk operasi database raw SQL (query, insert, update, delete) |

### Traits

| Trait | Namespace | Deskripsi |
|---|---|---|
| [BaseController](docs/traits/BaseController.md) | `budimanlai\yii2pkg\traits` | Trait untuk controller yang menyediakan shortcut query database dan response helper |

### Storage

| Class | Namespace | Deskripsi |
|---|---|---|
| [Storage](docs/storage/Storage.md) | `budimanlai\yii2pkg\storage` | Facade utama untuk operasi file storage (local / S3) |
| [LocalStorage](docs/storage/LocalStorage.md) | `budimanlai\yii2pkg\storage` | Driver storage untuk local filesystem |
| [S3Storage](docs/storage/S3Storage.md) | `budimanlai\yii2pkg\storage` | Driver storage untuk AWS S3 / S3-compatible object storage |

### Library

| Class | Namespace | Deskripsi |
|---|---|---|
| [Api3rdBase](docs/library/Api3rdBase.md) | `budimanlai\yii2pkg\library` | Base class untuk integrasi dengan third-party REST API |

## Lisensi

MIT
