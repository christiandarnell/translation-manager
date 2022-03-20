<?php

namespace ATStudio\TranslationManager;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class TranslationManager
{
    public static function scan(): void
    {
        $locales = static::availableLocales();
        $extracted = static::extractTranslations();

        foreach ($locales as $locale) {
            $contents = static::existingTranslations($locale);
            static::storeTranslations($extracted, $locale, $contents);
        }
    }

    /**
     * Get a list of available locales.
     */
    protected static function availableLocales(): array
    {
        $locales = array_keys(config('tm.locales'));
        $key = array_search(config('app.fallback_locale'), $locales);

        if ($key !== false) {
            unset($locales[$key]);
        }

        return $locales;
    }

    /**
     * Extract translation strings from scanned files.
     */
    protected static function extractTranslations(): Collection
    {
        $foldersToScan = config('tm.folders');
        $files = static::files($foldersToScan);
        $strings = collect([]);

        foreach ($files as $file) {
            preg_match_all('/(?:__|trans(?:_choice)?|@(?:lang|choice))\([\'"](.+)[\'"][),]/', $file->getContents(), $matches);

            if ($matches[1]) {
                $strings = $strings->merge($matches[1]);
            }
        }

        return $strings;
    }

    /**
     * Get a list of all files that we need to scan for translation strings.
     *
     * @return array<SplFileInfo>
     */
    protected static function files(array $foldersToScan): array
    {
        $files = [];

        foreach ($foldersToScan as $folder) {
            $files = array_merge($files, File::allFiles(base_path($folder)));
        }

        return $files;
    }

    /**
     * Retrieve existing translation strings for a specific locale.
     *
     * @return array<string, string>
     */
    protected static function existingTranslations(string $locale): array
    {
        if (File::exists(static::langPath($locale))) {
            $contents = json_decode(File::get(static::langPath($locale)), true);
        } else {
            $contents = [];
        }

        return $contents;
    }

    /**
     * Return path to the language file.
     */
    protected static function langPath(string $locale): string
    {
        return lang_path($locale.'.json');
    }

    /**
     * Update the language file with new translation strings.
     */
    protected static function storeTranslations(Collection $extracted, string $locale, array $contents): void
    {
        $strings = $extracted->mapWithKeys(function (?string $item) use ($locale, $contents) {
            return [
                $item => $contents[$item] ?? null,
            ];
        });

        File::put(static::langPath($locale), $strings->toJson(JSON_UNESCAPED_UNICODE));
    }
}
