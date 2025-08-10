<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key');
        $configApiKey = config('services.api.key');

    // Debugging: Log the received and expected API keys
        \Log::info('Received API Key: ' . $apiKey);
        \Log::info('Config API Key: ' . $configApiKey);

        if (!$apiKey || $apiKey !== $configApiKey) {
            \Log::error('Invalid API key');
            return response()->view('welcome', [], 404);
        }

        return $next($request);
    }
}
