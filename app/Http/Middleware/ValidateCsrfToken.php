<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;

class ValidateCsrfToken extends Middleware
{
    /**
     * Prefer the X-XSRF-TOKEN header (mirrors the XSRF cookie, refreshed on
     * every response and shared across all tabs) over the page-rendered
     * `_token`, which goes stale in any tab or history entry rendered before
     * a logout regenerated the session token.
     */
    protected function getTokenFromRequest($request)
    {
        if ($header = $request->header('X-XSRF-TOKEN')) {
            try {
                return CookieValuePrefix::remove(
                    $this->encrypter->decrypt($header, static::serialized())
                );
            } catch (DecryptException) {
                // fall through to the default token sources
            }
        }

        return parent::getTokenFromRequest($request);
    }

    /**
     * Log details whenever a 419 still occurs, so mismatches are diagnosable
     * from storage/logs/laravel.log (grep for CSRF-419).
     */
    public function handle($request, Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (TokenMismatchException $e) {
            Log::warning('CSRF-419', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'session_id' => substr($request->session()->getId(), 0, 8),
                'session_token' => substr((string) $request->session()->token(), 0, 8),
                'input_token' => substr((string) $request->input('_token'), 0, 8),
                'header_token' => substr((string) $request->header('X-CSRF-TOKEN'), 0, 8),
                'has_xsrf_header' => $request->hasHeader('X-XSRF-TOKEN'),
                'has_session_cookie' => $request->cookies->has(config('session.cookie')),
                'referer' => $request->header('referer'),
                'host' => $request->getHttpHost(),
            ]);

            throw $e;
        }
    }
}
