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

class LocalStorage extends Component {
    public string $upload_directory;
    public string $baseUrl;

    public function init(): void {
        parent::init();
    }

    /**
     * Upload a file to the local filesystem.
     * Automatically creates the destination directory if it does not exist.
     *
     * @param string $source      Absolute path to the source file
     * @param string $destination Relative destination path within `$upload_directory`
     */
    public function upload(string $source, string $destination): void {
        $destPath = $this->upload_directory . '/' . $destination;
        $folder = pathinfo($destPath, PATHINFO_DIRNAME);

        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        copy($source, $destPath);
    }

    /**
     * Get the public URL of a stored file by appending the file path to `$baseUrl`.
     *
     * @param string $file Relative file path within the storage
     * @return string The full public URL of the file
     */
    public function getPublicURL(string $file): string {
        return $this->baseUrl . '/' . $file;
    }

    /**
     * Get the private URL of a stored file.
     * For local storage, this is identical to getPublicURL.
     *
     * @param string $file Relative file path within the storage
     * @return string The full URL of the file
     */
    public function getPrivateURL(string $file): string {
        return $this->getPublicURL($file);
    }

    /**
     * Check whether a file exists in the local storage directory.
     *
     * @param string $file Relative file path within the storage
     * @return bool True if the file exists, false otherwise
     */
    public function isExists(string $file): bool {
        return file_exists($this->upload_directory . '/' . $file);
    }

    /**
     * Delete a file from the local storage directory.
     * Does nothing if the file does not exist.
     *
     * @param string $file Relative file path within the storage
     */
    public function delete(string $file): void {
        if (file_exists($this->upload_directory . '/' . $file)) {
            unlink($this->upload_directory . '/' . $file);
        }
    }
}
