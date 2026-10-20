<?php
namespace budimanlai\yii2pkg\storage;

use Aws\S3\S3Client;
use yii\base\Component;

/**
 * Amazon S3 (and S3-compatible) storage driver.
 *
 * Uploads files, generates public and pre-signed URLs, checks existence,
 * and deletes objects in an S3 bucket. Use through {@see Storage} by setting
 * `driver = 's3'`.
 *
 * @package budimanlai\yii2pkg\storage
 * @author  Budiman Lai <budiman.lai@gmail.com>
 */
class S3Storage extends Component {

    /** @var array{key: string, secret: string} AWS access credentials */
    public array $credentials = ['key' => '', 'secret' => ''];

    /** @var string S3 endpoint URL (use the AWS regional endpoint or a custom S3-compatible URL) */
    public string $endpoint;

    /** @var string AWS region (e.g. 'ap-southeast-1') */
    public string $region;

    /** @var bool Whether to use path-style endpoint instead of virtual-hosted-style */
    public bool $use_path_style_endpoint = true;

    /** @var bool Enable AWS SDK debug output */
    public bool $debug = false;

    /** @var string Target S3 bucket name */
    public string $bucket;

    /**
     * @var string Expiration duration for pre-signed URLs.
     *             Accepts any string compatible with {@link https://www.php.net/strtotime strtotime()}
     *             (e.g. `'+1 minutes'`, `'+1 hours'`).
     */
    public string $expired = '+1 minutes';

    /**
     * @var bool Always use pre-signed URLs. For S3 providers that do not allow public URLs:
     *           {@see getPublicURL()} returns a pre-signed URL and {@see upload()} uses the 'private' ACL.
     */
    public bool $always_private = false;

    /** @var S3Client Initialized AWS S3 client */
    private S3Client $s3Client;

    /**
     * Initialize and configure the S3 client.
     *
     * @return void
     */
    public function init(): void {
        parent::init();

        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region'  => $this->region,
            'endpoint' => $this->endpoint,
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key'    => $this->credentials['key'],
                'secret' => $this->credentials['secret'],
            ],
            'http' => ['verify' => false],
            'use_aws_shared_config_files' => false,
            'debug' => $this->debug,
        ]);
    }

    /**
     * Upload a file to the S3 bucket.
     *
     * @param  string $source      Absolute path to the source file on local disk
     * @param  string $destination Object key (path) in the S3 bucket
     * @param  string $acl         S3 ACL for the object (default: 'public-read'; always 'private' when `$always_private` is enabled)
     * @return void
     * @throws \Exception If the upload fails
     */
    public function upload(string $source, string $destination, string $acl = 'public-read'): void {
        if ($this->always_private) {
            $acl = 'private';
        }

        try {
            $this->s3Client->putObject([
                'Bucket' => $this->bucket,
                'Key'    => $destination,
                'Body'   => fopen($source, 'r'),
                'ACL'    => $acl,
            ]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Return the public URL of an S3 object.
     *
     * If `$always_private` is enabled, returns a pre-signed URL from {@see getPrivateURL()}.
     *
     * @param  string $file        Object key (path) in the S3 bucket
     * @param  string $defaultFile Fallback value returned when `$file` is empty or an error occurs
     * @return string Public URL, or `$defaultFile` on failure
     */
    public function getPublicURL(string $file, string $defaultFile = ''): string {
        if ($this->always_private) {
            return $this->getPrivateURL($file, $defaultFile);
        }

        if (empty($file)) {
            return $defaultFile;
        }

        try {
            return $this->s3Client->getObjectUrl($this->bucket, $file);
        } catch (\Exception) {
            return $defaultFile;
        }
    }

    /**
     * Return a pre-signed (private) URL for an S3 object.
     *
     * The URL expires after the duration defined by `$expired`.
     *
     * @param  string $file        Object key (path) in the S3 bucket
     * @param  string $defaultFile Fallback value returned when `$file` is empty or an error occurs
     * @return string Pre-signed URL, or `$defaultFile` on failure
     */
    public function getPrivateURL(string $file, string $defaultFile = ''): string {
        if (empty($file)) {
            return $defaultFile;
        }

        try {
            $cmd = $this->s3Client->getCommand('GetObject', [
                'Bucket' => $this->bucket,
                'Key' => $file,
            ]);
            $request = $this->s3Client->createPresignedRequest($cmd, $this->expired);
            return (string) $request->getUri();
        } catch (\Exception) {
            return $defaultFile;
        }
    }

    /**
     * Check whether an object exists in the S3 bucket.
     *
     * @param  string $file Object key (path) in the S3 bucket
     * @return bool   True if the object exists, false otherwise
     */
    public function isExists(string $file): bool {
        try {
            $this->s3Client->headObject([
                'Bucket' => $this->bucket,
                'Key'    => $file,
            ]);
            return true;
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * Delete an object from the S3 bucket.
     *
     * @param  string $file Object key (path) in the S3 bucket
     * @return bool   True on success, false if an error occurs
     */
    public function delete(string $file): bool {
        try {
            $this->s3Client->deleteObject([
                'Bucket' => $this->bucket,
                'Key'    => $file,
            ]);
            return true;
        } catch (\Exception) {
            return false;
        }
    }
}
