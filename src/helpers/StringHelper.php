<?php

namespace budimanlai\yii2pkg\helpers;

use Yii;
use yii\helpers\StringHelper as YiiStringHelper;

class StringHelper extends YiiStringHelper {

    /**
     * Standardize a phone number to the Indonesian format with prefix `62`.
     * Strips spaces, dashes, and plus signs before normalizing the prefix.
     *
     * @param string $number The raw phone number to normalize
     * @return string The normalized phone number with prefix `62`
     */
    public static function normalizePhoneNumber(string $number): string {
        $number = str_replace([' ', '-', '+'], '', $number);

        if (substr($number, 0, 5) == '62628') {
            return '62' . substr($number, 4, strlen($number));
        } else if (substr($number, 0, 2) == '62') {
            return $number;
        } else if (substr($number, 0, 1) == '0') {
            return '62' . substr($number, 1, strlen($number));
        } else {
            return $number;
        }
    }

    /**
     * Generate a unique transaction ID based on the current timestamp.
     * Format: `yymmddHHiiss` + last 4 digits of unix timestamp.
     *
     * @return string The generated transaction ID (16 characters)
     */
    public static function generateTrxID(): string {
        $t = (string) time();
        return date('ymdHis') . substr($t, -4);
    }

    /**
     * Collect and concatenate all first error messages from a Yii2 model
     * into a single comma-separated string.
     *
     * @param \yii\base\Model $model The model whose errors will be extracted
     * @return string Comma-separated error messages
     */
    public static function getErrorModel(\yii\base\Model $model): string {
        $msg = [];
        foreach ($model->getFirstErrors() as $message) {
            $msg[] = $message;
        }

        return implode(', ', $msg);
    }

    /**
     * Delete a file from the server if it exists.
     * The base upload directory is read from `Yii::$app->params['uploads']['upload_dir']`.
     * Does nothing if `$file` is empty or the file does not exist.
     *
     * @param string $file Relative file path within the upload directory
     */
    public static function deleteFile(string $file): void {
        if (!empty($file)) {
            $base_url = Yii::$app->params['uploads']['upload_dir'];
            $fullname = "{$base_url}/{$file}";

            if (file_exists($fullname)) {
                unlink($fullname);
            }
        }
    }

    /**
     * Generate a cryptographically secure random alphanumeric string.
     *
     * @param int $length The desired length of the random string (default: 10)
     * @return string The generated random string
     */
    public static function generateRandomString(int $length = 10): string {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Normalize string by removing all non-alphanumeric characters and spaces.
     *
     * @param string $string The string to normalize
     * @return string The normalized string containing only A-Z, a-z, and 0-9
     */
    public static function normalizeString(string $string): string {
        return (string) preg_replace('/[^A-Za-z0-9]/', '', $string);
    }

    /**
     * Convert a string to SEO-friendly format by lowercasing and removing
     * all non-alphanumeric characters (symbols, spaces, dashes, etc.).
     *
     * @param string $string The string to convert
     * @return string The SEO-friendly string, or empty string if input is empty
     */
    public static function seoString(string $string): string {
        if (!empty($string)) {
            return (string) preg_replace('/[^A-Za-z0-9]/', '', strtolower($string));
        }
        return '';
    }
}
