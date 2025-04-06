<?php

namespace App\Console\Commands;

use App\Jobs\GenerateArtifactJob;
use App\Jobs\GenerateDataJob;
use App\Models\ArtifactGenerationStatus;
use App\Models\ExtractionBucket;
use App\Models\ExtractionRun;
use App\Models\ExtractionRun\RunStatus;
use App\Models\File;
use App\Models\SavedExtractor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Mateffy\Magic;
use function Laravel\Prompts\spin;

/**
 * Usage like: php artisan wizard --schema=products.json exposé.pdf other-file.docx
 */
class ClearCacheCommand extends Command
{
	protected $signature = 'wizard:clear';

	protected $description = 'Clear the wizards cache';

	public function handle(): void
	{
        $this->info('Clearing cache...');

        $this->clearCache();

        $this->info('Cache cleared.');
    }

    protected function clearCache(): void
    {
        cache()->flush();
	}
}
