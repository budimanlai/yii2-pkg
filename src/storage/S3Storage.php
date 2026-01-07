<?php
/*
 * @author Budiman Lai <budiman.lai@gmail.com>
 * @created_at 2025-12-25 13:34:26
 * 
 * @package budimanlai\yii2pkg\storage
 * @version 1.0.0
 */
namespace budimanlai\yii2pkg\storage;

use yii\base\Component;

/*
 * S3Storage is a storage driver for AWS S3
 */
class S3Storage extends Component{
    public $credentials = [
        'key' => '',
        'secret' => ''
    ];
    public $endpoint;
    public $region;
    public $use_path_style_endpoint;
    public $debug = false;
    public $bucket;
    public $public_endpoint_url;
    public $public_url;
    public $private_endpoint_url;
    public $private_url;
    public $expired = "+1 minutes";

    private $s3Client;

    public function init() {
        parent::init();

        $this->s3Client = new \Aws\S3\S3Client([
            'version' => 'latest',
            'region'  => $this->region,
            'endpoint' => $this->endpoint,
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key'    => $this->credentials['key'],
                'secret' => $this->credentials['secret']
            ],
            'http' => [
                'verify' => false
            ],
            'use_aws_shared_config_files' => false,
            'debug' => $this->debug
        ]);
        
    }

    /**
     * Upload file to S3
     * 
     * @param string $source File path
     * @param string $destination S3 path
     * @param string $storageType public or private
     */
    public function upload($source, $destination, $acl = 'public-read') {
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
     * Get S3 public URL of file
     * 
     * @param string $file S3 path
     * @param string $defaultFile Default file path
     */
    public function getPublicURL($file, $defaultFile = "") {
        if (empty($file)) {
            return $defaultFile;
        } else {
            try {
                $url = $this->s3Client->getObjectUrl($this->bucket, $file);
                // Optional: Replace internal endpoint with public URL if configured
                if (isset($this->public_endpoint_url) && isset($this->public_url)) {
                    $url = str_replace($this->public_endpoint_url, $this->public_url, $url);
                }
                return $url;
            } catch (\Exception $e) {
                return $defaultFile;
            }
        }
    }

    /**
     * Get S3 private URL of file
     * 
     * @param string $file S3 path
     * @param string $defaultFile Default file path
     */
    public function getPrivateURL($file, $defaultFile = "") {
        if (empty($file)) {
            return $defaultFile;
        } else {
            try {
                $cmd = $this->s3Client->getCommand('GetObject', [
                    'Bucket' => $this->bucket,
                    'Key' => $file
                ]);
                $request = $this->s3Client->createPresignedRequest($cmd, $this->expired);
                $presignUrl = (string)$request->getUri();
                
                if (isset($this->private_endpoint_url) && isset($this->private_url)) {
                    $presignUrl = str_replace($this->private_endpoint_url, $this->private_url, $presignUrl);
                }
                
                return $presignUrl;
            } catch (\Exception $e) {
                return $defaultFile;
            }
        }
    }

    /**
     * Check if the file exists
     * 
     * @param string $file The file path
     * @return bool True if the file exists, false otherwise
     */
    public function isExists($file) {
        try {
            $this->s3Client->headObject([
                'Bucket' => $this->bucket,
                'Key'    => $file
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete the file
     * 
     * @param string $file The file path
     */
    public function delete($file) {
        try {
            $this->s3Client->deleteObject([
                'Bucket' => $this->bucket,
                'Key'    => $file
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
