<?php

namespace ChristianDarnell\TranslationManager\Tests;

use ChristianDarnell\TranslationManager\TranslationManagerServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
	protected function getPackageProviders($app)
	{
		return [
			TranslationManagerServiceProvider::class,
		];
	}
}
