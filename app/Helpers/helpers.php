<?php
if (!function_exists('get_language')) {
    function get_language() {
        return [
            (object)['code' => 'en', 'name' => 'English'],
            (object)['code' => 'ar', 'name' => 'العربية'],
        ];
    }
}
