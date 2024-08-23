<?php

namespace Capevace\MagicImport\Artifacts;

use App\Models\ExtractionBucket;
use App\Models\File;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * @method static mixed dispatch(ExtractionBucket $bucket, File $file)
 */
class GenerateArtifactJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected ExtractionBucket $bucket, protected File $file)
    {
    }

    /**
     * @throws ArtifactGenerationFailed
     */
    public function handle(): void
    {
        // Run python script
        $bin = __DIR__ . '/../../python/venv/bin/python';
        $script = __DIR__ . '/../../python/prepare-pdf.py';

        $file = $this->file->getPath();
        $dir = str($file)
            ->beforeLast('/')
            ->append('/artifact');

        $this->file->artifact_status = ArtifactGenerationStatus::InProgress;
        $this->file->save();

        try {
            $output = shell_exec("$bin $script $dir $file --json");
            $json = json_decode($output, true);

            if (isset($json['error'])) {
                throw new ArtifactGenerationFailed($json['error']);
            }

            $this->file->artifact_status = ArtifactGenerationStatus::Complete;
            $this->file->save();
        } catch (ArtifactGenerationFailed $e) {
            $this->file->artifact_status = ArtifactGenerationStatus::Failed;
            $this->file->save();

            throw $e;
        } catch (\Exception $e) {
            $this->file->artifact_status = ArtifactGenerationStatus::Failed;
            $this->file->save();

            throw new ArtifactGenerationFailed($e->getMessage(), previous: $e);
        }
    }
}
