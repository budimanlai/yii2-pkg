# yii2-pkg Documentation

Yii2 extension package berisi kumpulan helper, storage driver, dan base class yang dapat digunakan kembali di berbagai project Yii2.

## Daftar Class

### Helpers
| Class | Namespace | Deskripsi |
|---|---|---|
| [StringHelper](helpers/StringHelper.md) | `budimanlai\yii2pkg\helpers` | Ekstensi dari `yii\helpers\StringHelper` dengan tambahan method normalisasi string |

### Storage
| Class | Namespace | Deskripsi |
|---|---|---|
| [Storage](storage/Storage.md) | `budimanlai\yii2pkg\storage` | Facade utama untuk operasi file storage (local / S3) |
| [LocalStorage](storage/LocalStorage.md) | `budimanlai\yii2pkg\storage` | Driver storage untuk local filesystem |
| [S3Storage](storage/S3Storage.md) | `budimanlai\yii2pkg\storage` | Driver storage untuk AWS S3 / S3-compatible object storage |

### Models
| Class | Namespace | Deskripsi |
|---|---|---|
| [Api3rdBase](models/Api3rdBase.md) | `budimanlai\yii2pkg\models` | Base class untuk integrasi dengan third-party REST API |

## Instalasi

```bash
composer require budimanlai/yii2-pkg
```

## Requirement

- PHP >= 8.0
- Yii2 ~2.0
- `aws/aws-sdk-php` ^3.0 (hanya untuk S3Storage)
- `yiisoft/yii2-httpclient` ^2.0 (hanya untuk Api3rdBase)
