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
 * LocalStorage is a storage driver upload file to local file system
 */
class LocalStorage extends Component {
    
    /*
     * @var string The directory where files will be uploaded
     */
    public $upload_directory;

    /*
     * @var string The base URL of the files
     */
    public $baseUrl;

    public function init() {
        parent::init();
    }

    /*
     * Upload file to local file system
     * 
     * @param string $source The source file path
     * @param string $destination The destination path where files will be uploaded
     */
    public function upload($source, $destination) {
        $destPath = $this->upload_directory . '/' . $destination;

        // ambil folder saja
        $folder = pathinfo($destPath, PATHINFO_DIRNAME);

        // cek folder apakah ada
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        // copy file
        copy($source, $destPath);
    }

    /*
     * Get the URL of the file
     * 
     * @param string $file The file path
     * @return string The URL of the file
     */
    public function getPublicURL($file) {
        return $this->baseUrl . '/' . $file;
    }

    /*
     * Get the private URL of the file
     * 
     * @param string $file The file path
     * @return string The private URL of the file
     */
    public function getPrivateURL($file) {
        return $this->getPublicURL($file);
    }

    /*
     * Check if the file exists
     * 
     * @param string $file The file path
     * @return bool True if the file exists, false otherwise
     */
    public function isExists($file) {
        return file_exists($this->upload_directory . '/' . $file);
    }

    /*
     * Delete the file
     * 
     * @param string $file The file path
     */
    public function delete($file) {
        if (file_exists($this->upload_directory . '/' . $file)) {
            unlink($this->upload_directory . '/' . $file);
        }
    }
}