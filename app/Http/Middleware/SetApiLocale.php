<?php

namespace App\Http\Middleware;

use App\Common\Helpers\TranslationHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetApiLocale
{
    /**
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        app()->setLocale(TranslationHelper::resolveLocale($request));

        return $next($request);
    }
}
