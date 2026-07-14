<?php

if (!function_exists('is_logged_in')) {
    function is_logged_in(): bool
    {
        return session()->has('isLoggedIn') && session()->get('isLoggedIn') === true;
    }
}
