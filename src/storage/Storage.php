<?php
namespace budimanlai\yii2pkg\storage;

use yii\base\Component;

class Storage extends Component {
    public string $driver;
    public array $config = [];

    private LocalStorage|S3Storage $driver_instance;

    private array $driver_map = [
        'local' => 'LocalStorage',
        's3' => 'S3Storage',
    ];

    public function init(): void {
        parent::init();

        $class = "budimanlai\yii2pkg\storage\\" . $this->driver_map[$this->driver];
        $this->driver_instance = new $class($this->config);
    }

    /**
     * Get the name of the active storage driver.
     *
     * @return string The driver name (e.g. 'local', 's3')
     */
    public function getDriverName(): string {
        return $this->driver;
    }

    /**
     * Upload a file to the configured storage driver.
     *
     * @param string $file   Absolute path to the source file on local disk
     * @param string $path   Destination path within the storage (e.g. 'images/photo.jpg')
     */
    public function upload(string $file, string $path): void {
        $this->driver_instance->upload($file, $path);
    }

    /**
     * Get the publicly accessible URL of a stored file.
     *
     * @param string $file The file path within the storage
     * @return string The public URL of the file
     */
    public function getPublicURL(string $file): string {
        return $this->driver_instance->getPublicURL($file);
    }

    /**
     * Get a private (pre-signed) URL of a stored file.
     * For local storage this returns the same as getPublicURL.
     * For S3, this generates a time-limited pre-signed URL.
     *
     * @param string $file The file path within the storage
     * @return string The private URL of the file
     */
    public function getPrivateURL(string $file): string {
        return $this->driver_instance->getPrivateURL($file);
    }

    /**
     * Check whether a file exists in the storage.
     *
     * @param string $file The file path within the storage
     * @return bool True if the file exists, false otherwise
     */
    public function isExists(string $file): bool {
        return $this->driver_instance->isExists($file);
    }

    /**
     * Delete a file from the storage.
     *
     * @param string $file The file path within the storage
     */
    public function delete(string $file): void {
        $this->driver_instance->delete($file);
    }
}
