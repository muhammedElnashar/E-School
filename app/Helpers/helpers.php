<?php
if (!function_exists('get_language')) {
    function get_language() {
        return [
            (object)['code' => 'en', 'name' => 'English'],
            (object)['code' => 'ar', 'name' => 'العربية'],
        ];
    }
}

if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        return config($key, $default);
    }
}
