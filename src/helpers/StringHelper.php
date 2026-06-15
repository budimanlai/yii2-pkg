<?php

namespace budimanlai\yii2pkg\helpers;

use yii\helpers\StringHelper as YiiStringHelper;

class StringHelper extends YiiStringHelper {

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
