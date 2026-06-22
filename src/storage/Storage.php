<?php
namespace budimanlai\yii2pkg\storage;

use yii\base\Component;

/**
 * Unified storage component that delegates file operations to a configured driver.
 *
 * Supported drivers: `'local'` ({@see LocalStorage}) and `'s3'` ({@see S3Storage}).
 * Configure the component in `components` array and set `$driver` together with
 * any driver-specific options in `$config`.
 *
 * @package budimanlai\yii2pkg\storage
 * @author  Budiman Lai <budiman.lai@gmail.com>
 */
class Storage extends Component {

    /** @var string Storage driver to use ('local' or 's3') */
    public string $driver;

    /** @var array Driver-specific configuration options */
    public array $config = [];

    /** @var LocalStorage|S3Storage Active driver instance */
    private LocalStorage|S3Storage $driver_instance;

    /** @var array<string, string> Map of driver name to class name */
    private array $driver_map = [
        'local' => 'LocalStorage',
        's3'    => 'S3Storage',
    ];

    /**
     * Instantiate and initialize the configured storage driver.
     *
     * @return void
     */
    public function init(): void {
        parent::init();

        $class = "budimanlai\yii2pkg\storage\\" . $this->driver_map[$this->driver];
        $this->driver_instance = new $class($this->config);
    }

    /**
     * Return the name of the active storage driver.
     *
     * @return string Driver name (e.g. 'local', 's3')
     */
    public function getDriverName(): string {
        return $this->driver;
    }

    /**
     * Upload a file to the configured storage driver.
     *
     * @param  string $file Absolute path to the source file on local disk
     * @param  string $path Destination path within the storage (e.g. 'images/photo.jpg')
     * @return void
     */
    public function upload(string $file, string $path): void {
        $this->driver_instance->upload($file, $path);
    }

    /**
     * Return the publicly accessible URL of a stored file.
     *
     * @param  string $file File path within the storage
     * @return string Public URL of the file
     */
    public function getPublicURL(string $file): string {
        return $this->driver_instance->getPublicURL($file);
    }

    /**
     * Return a private (pre-signed) URL of a stored file.
     *
     * For local storage this returns the same value as {@see getPublicURL()}.
     * For S3, this generates a time-limited pre-signed URL.
     *
     * @param  string $file File path within the storage
     * @return string Private URL of the file
     */
    public function getPrivateURL(string $file): string {
        return $this->driver_instance->getPrivateURL($file);
    }

    /**
     * Check whether a file exists in the storage.
     *
     * @param  string $file File path within the storage
     * @return bool   True if the file exists, false otherwise
     */
    public function isExists(string $file): bool {
        return $this->driver_instance->isExists($file);
    }

    /**
     * Delete a file from the storage.
     *
     * @param  string $file File path within the storage
     * @return void
     */
    public function delete(string $file): void {
        $this->driver_instance->delete($file);
    }
}
