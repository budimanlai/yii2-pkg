<?php

namespace budimanlai\yii2pkg\helpers;

use yii\helpers\StringHelper as YiiStringHelper;

class StringHelper extends YiiStringHelper {

    /*
     * Normalize string remove symbol and space
     * 
     * @param string $string The string to normalize
     * @return string The normalized string
     */
    public static function normalizeString($string) {
        return preg_replace('/[^A-Za-z0-9]/', '', $string);
    }

    /*
     * SEO string replace string to dash and remove symbol
     * 
     * @param string $string The string to SEO
     * @return string The SEO string
     */
    public static function seoString($string) {
        if (!empty($string)) {
            return preg_replace('/[^A-Za-z0-9]/', '', strtolower($string));
        }
        return '';
    }
}