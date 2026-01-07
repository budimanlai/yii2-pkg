<?php
namespace budimanlai\yii2pkg\storage;

use yii\base\Component;

class Storage extends Component {
    // $driver is the driver to be used for storage
    public $driver;

    // $config is the configuration for the driver
    public $config;

    // $driver_instance is the instance of the driver
    private $driver_instance;

    // $driver_map is the map of driver to class
    private $driver_map = [
        'local' => 'LocalStorage',
        's3' => 'S3Storage',
    ];

    public function init() {
        parent::init();

        $class = "budimanlai\yii2pkg\storage\\" . $this->driver_map[$this->driver];
        $this->driver_instance = new $class($this->config);
    }

    public function getDriverName() {
        return $this->driver;
    }

    /**
     * Upload file to storage
     * 
     * @param string $file The file to be uploaded
     * @param string $path The path where the file will be uploaded
     */
    public function upload($file, $path) {
        $this->driver_instance->upload($file, $path);
    }

    /*
     * Get the public URL of the file
     * 
     * @param string $file The file path
     * @return string The public URL of the file
     */
    public function getPublicURL($file) {
        return $this->driver_instance->getPublicURL($file);
    }

    /*
     * Get the private URL of the file
     * 
     * @param string $file The file path
     * @return string The private URL of the file
     */
    public function getPrivateURL($file) {
        return $this->driver_instance->getPrivateURL($file);
    }

    /*
     * Check if the file exists
     * 
     * @param string $file The file path
     * @return bool True if the file exists, false otherwise
     */ 
    public function isExists($file) {
        return $this->driver_instance->isExists($file);
    }

    /*
     * Delete the file
     * 
     * @param string $file The file path
     */
    public function delete($file) {
        return $this->driver_instance->delete($file);
    }
}