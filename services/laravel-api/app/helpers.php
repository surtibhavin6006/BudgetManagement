<?php

use App\Models\User;

if (!function_exists('current_user_id')) {
    function current_user_id(): int
    {
        return (int) auth()->id();
    }
}

if (!function_exists('current_user')) {
    function current_user(): User
    {
        return auth()->user();
    }
}
