<?php

namespace ChristianDarnell\TranslationManager\Commands;

use Facades\ChristianDarnell\TranslationManager\TranslationManager;
use Illuminate\Console\Command;

class ScanFiles extends Command
{
	protected $signature = 'tm:scan';

	protected $description = 'Scan files for translation strings.';

	public function handle()
	{
		TranslationManager::scan();

		$this->info('The scan has completed.');
	}
}
