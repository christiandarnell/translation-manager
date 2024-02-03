<?php

namespace ChristianDarnell\TranslationManager\Controllers;

use ChristianDarnell\TranslationManager\TranslationManager;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class ManagerController
{
	public function index(Request $request)
	{
		$tab = $request->query('tab');

		if ($tab === 'missing') {
			return $this->missingTranslations();
		}

		return $this->allTranslations();
	}

	public function store(TranslationManager $manager)
	{
		$manager->scan();

		return response([
			'message' => 'All translations have been extracted...',
		]);
	}

	public function update(Request $request)
	{
		$translations = $request->validate([
			'translations' => 'array',
		])['translations'];

		if ($request->query('tab') === 'missing') {
			$translations = $this->allTranslations()->merge($translations);
		}
		foreach ($translations as $key => $translation) {
			if (is_null($translation)) {
				$translations[$key] = '';
			}
		}
		File::put(
			$this->langFile($request->query('lang')),
			json_encode(collect($translations)->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
		);
	}

	public function counters()
	{
		return response([
			'all' => $this->allTranslations()->count(),
			'missing' => $this->missingTranslations()->count(),
		]);
	}

	protected function allTranslations(): Collection
	{
		return collect(json_decode(File::get($this->langFile(request('lang'))), associative: true));
	}

	protected function missingTranslations(): Collection
	{
		return $this->allTranslations()->filter(fn (?string $translation) => !$translation);
	}

	protected function langFile(string $locale): string
	{
		return lang_path($locale . '.json');
	}
}
