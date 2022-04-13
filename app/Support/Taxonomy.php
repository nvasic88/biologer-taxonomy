<?php

namespace App\Support;

class Taxonomy
{
    public static function checkKey($key)
    {
        if ($key == '') {
            return '';
        }

        $keys = [
            'rs' => config('biologer.taxonomy_key_rs'),
            'hr' => config('biologer.taxonomy_key_hr'),
            'ba' => config('biologer.taxonomy_key_ba'),
            'test' => config('biologer.taxonomy_key_test'),
        ];

        if (in_array($key, $keys)) {
            return array_search($key, $keys);
        }

        return '';
    }
}
