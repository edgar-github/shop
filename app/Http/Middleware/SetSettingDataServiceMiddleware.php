<?php

namespace App\Http\Middleware;

use App\Models\Settings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SetSettingDataServiceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Cache::has('settings')) {
            try {
                $allSettings = Settings::get();
                $sessionData = [];
                foreach ($allSettings as $setting) {
                    $sessionData[$setting->slug] = $setting->value;
                }

                Cache::put('settings', $sessionData);
            }
            catch (\Exception $e) {
                 echo $e->getMessage();
            }
        }

        return $next($request);
    }
}
