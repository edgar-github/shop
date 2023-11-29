<?php

use Illuminate\Support\Str;

if (!function_exists('isRouteActive')) {
    function isRouteActive($routeName): bool
    {
        $currentRoute = request()->route()->getName();
        return $currentRoute === $routeName || Str::endsWith($currentRoute, '.' . $routeName);
    }
}

