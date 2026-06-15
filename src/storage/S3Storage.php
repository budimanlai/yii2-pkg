<?php
/*
 * @author Budiman Lai <budiman.lai@gmail.com>
 * @created_at 2025-12-25 13:34:26
 *
 * @package budimanlai\yii2pkg\storage
 * @version 1.0.0
 */
namespace budimanlai\yii2pkg\storage;

use Aws\S3\S3Client;
use yii\base\Component;

class S3Storage extends Component {
    public array $credentials = ['key' => '', 'secret' => ''];
    public string $endpoint;
    public string $region;
    public bool $use_path_style_endpoint = true;
    public bool $debug = false;
    public string $bucket;
    public ?string $public_endpoint_url = null;
    public ?string $public_url = null;
    public ?string $private_endpoint_url = null;
    public ?string $private_url = null;

    /** Expiration for pre-signed URLs. Accepts strtotime-compatible strings (e.g. '+1 minutes', '+1 hours'). */
    public string $expired = '+1 minutes';

    private S3Client $s3Client;

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
     * Upload a file to S3.
     *
     * @param string $source      Absolute path to the source file on local disk
     * @param string $destination Object key (path) in the S3 bucket
     * @param string $acl         S3 ACL for the object (default: 'public-read')
     * @throws \Exception If the upload fails
     */
    public function upload(string $source, string $destination, string $acl = 'public-read'): void {
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
     * Get the public URL of an S3 object.
     * If `$public_endpoint_url` and `$public_url` are both configured,
     * the internal endpoint in the URL will be replaced with the public URL.
     *
     * @param string $file        Object key (path) in the S3 bucket
     * @param string $defaultFile Fallback value returned when `$file` is empty or an error occurs
     * @return string The public URL, or `$defaultFile` on failure
     */
    public function getPublicURL(string $file, string $defaultFile = ''): string {
        if (empty($file)) {
            return $defaultFile;
        }

        try {
            $url = $this->s3Client->getObjectUrl($this->bucket, $file);
            if ($this->public_endpoint_url !== null && $this->public_url !== null) {
                $url = str_replace($this->public_endpoint_url, $this->public_url, $url);
            }
            return $url;
        } catch (\Exception) {
            return $defaultFile;
        }
    }

    /**
     * Get a pre-signed (private) URL for an S3 object.
     * The URL expires after the duration set in `$expired`.
     * If `$private_endpoint_url` and `$private_url` are configured,
     * the internal endpoint in the URL will be replaced with the private URL.
     *
     * @param string $file        Object key (path) in the S3 bucket
     * @param string $defaultFile Fallback value returned when `$file` is empty or an error occurs
     * @return string The pre-signed URL, or `$defaultFile` on failure
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
            $presignUrl = (string) $request->getUri();

            if ($this->private_endpoint_url !== null && $this->private_url !== null) {
                $presignUrl = str_replace($this->private_endpoint_url, $this->private_url, $presignUrl);
            }

            return $presignUrl;
        } catch (\Exception) {
            return $defaultFile;
        }
    }

    /**
     * Check whether an object exists in the S3 bucket.
     *
     * @param string $file Object key (path) in the S3 bucket
     * @return bool True if the object exists, false otherwise
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
     * @param string $file Object key (path) in the S3 bucket
     * @return bool True on success, false if an error occurs
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
