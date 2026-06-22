<?php
namespace budimanlai\yii2pkg\storage;

use yii\base\Component;

/**
 * Local filesystem storage driver.
 *
 * Stores files on the local disk under `$upload_directory` and exposes them
 * via `$baseUrl`. Use through {@see Storage} by setting `driver = 'local'`.
 *
 * @package budimanlai\yii2pkg\storage
 * @author  Budiman Lai <budiman.lai@gmail.com>
 */
class LocalStorage extends Component {

    /** @var string Absolute path to the root upload directory on disk */
    public string $upload_directory;

    /** @var string Base URL used to build public file URLs */
    public string $baseUrl;

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function init(): void {
        parent::init();
    }

    /**
     * Upload a file to the local filesystem.
     *
     * Automatically creates the destination directory if it does not exist.
     *
     * @param  string $source      Absolute path to the source file
     * @param  string $destination Relative destination path within `$upload_directory`
     * @return void
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
     * Return the public URL of a stored file.
     *
     * Appends the file path to `$baseUrl`.
     *
     * @param  string $file Relative file path within the storage
     * @return string Full public URL of the file
     */
    public function getPublicURL(string $file): string {
        return $this->baseUrl . '/' . $file;
    }

    /**
     * Return the private URL of a stored file.
     *
     * For local storage this is identical to {@see getPublicURL()}.
     *
     * @param  string $file Relative file path within the storage
     * @return string Full URL of the file
     */
    public function getPrivateURL(string $file): string {
        return $this->getPublicURL($file);
    }

    /**
     * Check whether a file exists in the local storage directory.
     *
     * @param  string $file Relative file path within the storage
     * @return bool   True if the file exists, false otherwise
     */
    public function isExists(string $file): bool {
        return file_exists($this->upload_directory . '/' . $file);
    }

    /**
     * Delete a file from the local storage directory.
     *
     * Does nothing if the file does not exist.
     *
     * @param  string $file Relative file path within the storage
     * @return void
     */
    public function delete(string $file): void {
        if (file_exists($this->upload_directory . '/' . $file)) {
            unlink($this->upload_directory . '/' . $file);
        }
    }
}
