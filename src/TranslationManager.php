<?php

namespace ChristianDarnell\TranslationManager;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class TranslationManager
{
	public function scan(): void
	{
		return;
		$locales = $this->availableLocales();
		$extracted = $this->extractTranslations();

		foreach ($locales as $locale) {
			$contents = $this->existingTranslations($locale);
			$this->storeTranslations($extracted, $locale, $contents);
		}
	}

	public function parse(string $text): array
	{
		$pattern = '/' .
			'(?:__|Lang::(?:get|choice)|trans(?:_choice)?|@(?:lang|choice))\(' .
			'([\'"])(.+?)\1' .
			'/';
		preg_match_all($pattern, $text, $matches);

		if (!$matches[2]) return [];

		$matches[2] = preg_replace('/\\\(["\'])/', '$1', $matches[2]);

		return $matches[2];
	}

	/**
	 * Get a list of available locales.
	 */
	protected function availableLocales(): array
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
	protected function extractTranslations(): Collection
	{
		$foldersToScan = config('tm.folders');
		$files = $this->files($foldersToScan);
		$strings = collect([]);

		foreach ($files as $file) {
			$strings = $strings->merge($this->parse($file->getContents()));
		}

		return $strings;
	}

	/**
	 * Get a list of all files that we need to scan for translation strings.
	 *
	 * @return array<SplFileInfo>
	 */
	protected function files(array $foldersToScan): array
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
	protected function existingTranslations(string $locale): array
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
	protected function langPath(string $locale): string
	{
		return lang_path($locale . '.json');
	}

	/**
	 * Update the language file with new translation strings.
	 */
	protected function storeTranslations(Collection $extracted, string $locale, array $contents): void
	{
		$strings = $extracted->mapWithKeys(function (?string $item) use ($locale, $contents) {
			return [
				$item => $contents[$item] ?? '',
			];
		});

		File::put(static::langPath($locale), $strings->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	}
}
