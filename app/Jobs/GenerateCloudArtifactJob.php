<?php

namespace App\Jobs;

use App\Models\ArtifactGenerationStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mateffy\Magic\Buckets\CloudArtifact;
use Mateffy\Magic\Exceptions\ArtifactGenerationFailed;

/**
 * @method static mixed dispatch(CloudArtifact $cloudArtifact)
 */
class GenerateCloudArtifactJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected CloudArtifact $cloudArtifact) {}

    /**
     * @throws ArtifactGenerationFailed
     */
    public function handle(): void
    {
        // Run python script
        $pythonDir = base_path('packages/llm-magic/python');
        $script = 'prepare-pdf.py';

        $uvPath = config('llm-magic.uv.path');

        $artifactTempDir = tempnam(sys_get_temp_dir(), 'cloud-artifact');
        $artifactTempPath = "{$artifactTempDir}/original.{$this->cloudArtifact->extension}";

        try {
            \Illuminate\Support\Facades\File::ensureDirectoryExists($artifactTempDir);
            $this->cloudArtifact->streamTo($artifactTempPath);

            $this->cloudArtifact->status = ArtifactGenerationStatus::InProgress;
            $this->cloudArtifact->save();

            // Change working directory to the artifact directory
            $currentDir = getcwd();
            chdir($pythonDir);

            $output = shell_exec("$uvPath run --isolated $script $artifactTempDir $artifactTempPath -- --json");
            $json = json_decode($output, true);

            if (isset($json['error'])) {
                throw new ArtifactGenerationFailed($json['error']);
            }

            $this->cloudArtifact->status = ArtifactGenerationStatus::Complete;
            $this->cloudArtifact->save();
        } catch (ArtifactGenerationFailed $e) {
            $this->cloudArtifact->status = ArtifactGenerationStatus::Failed;
            $this->cloudArtifact->save();

            throw $e;
        } catch (\Exception $e) {
            $this->cloudArtifact->status = ArtifactGenerationStatus::Failed;
            $this->cloudArtifact->save();

            throw new ArtifactGenerationFailed($e->getMessage(), previous: $e);
        } finally {
            chdir($currentDir);

            try {
                \Illuminate\Support\Facades\File::deleteDirectory($artifactTempDir);
            } catch (\Exception $e) {
                report($e);
            }
        }
    }
}
