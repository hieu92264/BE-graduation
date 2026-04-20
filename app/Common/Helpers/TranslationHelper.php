<?php

namespace App\Common\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TranslationHelper
{
    /**
     * @var array<int, string>
     */
    private const SUPPORTED_LOCALES = ['vi', 'en'];

    public static function resolveLocale(Request $request): string
    {
        $requestedLocale = $request->query('lang')
            ?? $request->query('locale')
            ?? $request->header('X-Locale')
            ?? $request->header('X-Lang');

        $normalizedRequestedLocale = self::normalizeLocale($requestedLocale);
        if ($normalizedRequestedLocale !== null) {
            return $normalizedRequestedLocale;
        }

        $userLocale = self::normalizeLocale(Auth::user()?->locale);
        if ($userLocale !== null) {
            return $userLocale;
        }

        $preferredLocale = self::normalizeLocale(
            $request->getPreferredLanguage(self::SUPPORTED_LOCALES)
        );

        return $preferredLocale ?? config('app.locale', 'vi');
    }

    public static function translate(?string $message, array $replace = []): ?string
    {
        if ($message === null || $message === '') {
            return $message;
        }

        return __($message, $replace);
    }

    public static function normalizeLocale(?string $locale): ?string
    {
        if (! is_string($locale) || trim($locale) === '') {
            return null;
        }

        $normalizedLocale = strtolower(str_replace('_', '-', trim($locale)));

        foreach (self::SUPPORTED_LOCALES as $supportedLocale) {
            if ($normalizedLocale === $supportedLocale || str_starts_with($normalizedLocale, $supportedLocale . '-')) {
                return $supportedLocale;
            }
        }

        return null;
    }
}
