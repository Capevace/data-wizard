<?php

namespace App\Extraction;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use JsonException;
use Mateffy\Magic\Exceptions\ArtifactGenerationFailed;
use App\Extraction\Artifacts\Slices\EmbedSlice;
use App\Extraction\Artifacts\Slices\ImageSlice;
use App\Extraction\Artifacts\Slices\RawTextSlice;
use App\Extraction\Artifacts\Slices\Slice;
use App\Extraction\Artifacts\Slices\TextualSlice;
use App\Extraction\PdfParser;
use Mateffy\Magic\LLM\Message\MultimodalMessage\Base64Image;


/**
 * Artifact directory:
 * /artifacts/<ID>
 * /artifacts/<ID>/metadata.json
 * /artifacts/<ID>/source.<EXT>
 * /artifacts/<ID>/thumbnail.jpg
 * /artifacts/<ID>/embeds (optional)
 * /artifacts/<ID>/embeds/<FILENAME>.jpg
 */
class DiskArtifact implements Artifact
{
    protected ?ArtifactMetadata $metadata = null;
    protected ?array $contents = null;

    public readonly bool $cache;
    public readonly string $artifactDir;
    public readonly ?string $artifactDirDisk;

    public static function from(string $path, ?string $disk = null): static
    {
        $artifactDisk = config('llm-magic.artifacts.disk');
        $prefix = config('llm-magic.artifacts.prefix');
        $base = config('llm-magic.artifacts.base', storage_path('app/magic-artifacts'));

        $artifactDir = $artifactDisk
            ? $prefix
            : $base;

        return new static(
            path: $path,
            disk: $disk,
            artifactDir: $artifactDir,
            artifactDirDisk: $artifactDisk,
        );
    }

    protected function __construct(
        public readonly string $path,
        /**
         * Absolute path to the directory where the artifact will be stored.
         * If a artifactDirDisk is provided,
         */
        public readonly ?string $disk = null,
        ?string $artifactDir = null,
        ?string $artifactDirDisk = null,
        ?string $artifactDirName = null,
    )
    {
        $artifactDirName ??= "magic-artifact-" . crc32("{$this->disk}:{$this->path}");

        $makeArtifactDir = fn (string $dir) => collect([$dir, $artifactDirName])
            ->filter(fn ($part) => filled($part))
            ->implode("/");

        if ($artifactDir !== null) {
            $this->cache = true;
            $this->artifactDirDisk = $artifactDirDisk;
            $this->artifactDir = $makeArtifactDir($artifactDir);
        } else {
            $this->cache = false;
            $this->artifactDirDisk = null;
            $this->artifactDir = $makeArtifactDir(sys_get_temp_dir());
        }
    }

    public function getSourcePath(): string
    {
        return $this->path;
    }

    protected function getRawContents(): mixed
    {
        return $this->disk
            ? Storage::disk($this->disk)->get($this->path)
            : File::get($this->path);
    }

    /**
     * @throws JsonException
     * @throws ArtifactGenerationFailed
     */
    public function getContents(): array
    {
        return $this->contents ?? $this->contents = match ($this->getMetadata()->type) {
            ArtifactType::Text => [new RawTextSlice($this->getRawContents())],
            ArtifactType::Image => [new ImageSlice(
                path: $this->getMetadata()->path,
                mimetype: $this->getMetadata()->mimetype,
                absolutePath: true,
            )],
            ArtifactType::Pdf => $this->getPdfContents(),
            default => [],
        };
    }

    /**
     * @throws JsonException
     * @throws ArtifactGenerationFailed
     */
    public function refreshContents(): array
    {
        $this->contents = null;

        return $this->getContents();
    }

    /**
     * @throws ArtifactGenerationFailed
     * @throws JsonException
     */
    protected function getPdfContents(): array
    {
        $disk = $this->artifactDirDisk
            ? Storage::disk($this->artifactDirDisk)
            : null;

        $outputDir = null;

        if ($this->cache) {
            if ($disk?->exists($this->artifactDir)) {
                $outputDir = $this->artifactDir;
            } elseif (File::exists($this->artifactDir)) {
                $outputDir = $this->artifactDir;
            }
        }

        // No cached path found, generate
        if ($outputDir === null) {
            $parser = new PdfParser(path: $this->path, disk: $this->disk);
            $outputDir = $parser->parse();

            if ($this->cache) {
                if ($this->artifactDirDisk) {
                    $outputDir = $parser->moveToStorage(disk: $this->artifactDirDisk, path: $this->artifactDir);
                } else {
                    $outputDir = $parser->moveToPath(path: $this->artifactDir);
                }
            }
        }

        if ($this->cache && $disk) {
            $contents = $disk->get("{$outputDir}/contents.json");
            $data = json_decode($contents, true, flags: JSON_THROW_ON_ERROR);
        } else {
            $data = File::json("{$outputDir}/contents.json", flags: JSON_THROW_ON_ERROR);
        }

        return collect($data)
            ->map(fn (array $content) => match ($content['type']) {
                'text' => RawTextSlice::from($content),
//                'image', 'page-image',
                'page-image-marked' => ImageSlice::from($content),
                default => null
            })
            ->filter()
            ->sortBy(fn (Slice $content) => $content->getPage() ?? 0)
            ->values()
            ->all();
    }

    /**
     * @throws JsonException
     */
    public function getText(?int $maxPages = null): ?string
    {
        return collect($this->getContents())
            ->filter(fn (Slice $content) => $content instanceof TextualSlice)
            ->groupBy(fn (TextualSlice $content) => $content->getPage() ?? 0)
            ->sortBy(fn (Collection $contents, $page) => $page)
            ->values()
            ->take($maxPages)
            ->flatMap(fn (Collection $contents) => collect($contents)
                ->map(fn (TextualSlice $content) => "<page num=\"{$content->page}\">\n{$content->text}\n</page>")
            )
            ->join("\n");
    }

    public function getMetadata(): ArtifactMetadata
    {
        return $this->metadata
            ?: $this->metadata = ArtifactMetadata::fromFile(path: $this->path, disk: $this->disk);
    }

    public function makeBase64Image(EmbedSlice $content): Base64Image
    {
        $path = $content->isAbsolutePath()
            ? $content->getPath()
            : "{$this->artifactDir}/{$content->getPath()}";

        return $this->disk
            ? Base64Image::fromDisk($this->artifactDirDisk, $path)
            : Base64Image::fromPath($path);
    }

    public function getBase64Images(?int $maxPages = null): Collection
    {
        return collect($this->getContents())
            ->filter(fn (Slice $content) => $content instanceof EmbedSlice)
            ->groupBy(fn (EmbedSlice $content) => $content->getPage() ?? 0)
            ->sortBy(fn (Collection $contents, $page) => $page)
            ->take($maxPages)
            ->flatMap(fn (Collection $contents) => collect($contents)
                ->map(fn (EmbedSlice $image) => $this->makeBase64Image($image))
            );
    }

    public function getEmbedContents(EmbedSlice $content): mixed
    {
        $path = $content->isAbsolutePath()
            ? $content->getPath()
            : "{$this->artifactDir}/{$content->getPath()}";

        return $this->disk
            ? Storage::disk($this->artifactDirDisk)->get($path)
            : File::get($path);
    }

    /**
     * Splits the document. Adds data until either the character limit or embed limit is reached, then starts a new split.
     *
     * @return {0: array<array<Artifact>>, 1: int}
     */
    public function split(int $maxTokens): array
    {
        $artifacts = [];
        $contents = [];

        $tokens = 0;
        $totalTokens = 0;

        foreach ($this->getContents() as $content) {
            if ($content instanceof TextualSlice) {
                $textLength = strlen($content->text());

                // TODO: Actually count tokens, because 1 character !== 1 token
                // TODO: We can also split the text, if the tokens for this text are higher than the maxTokens
                $tokens += $textLength;
                $totalTokens += $textLength;

                $contents[] = $content;
            } elseif ($content instanceof EmbedSlice) {
                if ($content instanceof ImageSlice && $content->width && $content->height) {
                    // Based on Anthropic's model: tokens = (width px * height px)/750
                    // This will not be accurate for other LLMs but is good enough for now
                    $tokens += ($content->width * $content->height) / 750;
                    $totalTokens += ($content->width * $content->height) / 750;
                    // 3:4	951x1268 px
                } else {
                    // Based on Anthropic's model: we use the average for a 1000x1000 image
                    $tokens += 1334;
                    $totalTokens += 1334;
                }

                $contents[] = $content;
            }

            if ($tokens > $maxTokens) {
                $artifacts[] = new SplitArtifact(original: $this, contents: $contents, tokens: $tokens);
                $contents = [];

                $tokens = 0;
            }
        }

        if (! empty($contents)) {
            $artifacts[] = new SplitArtifact(original: $this, contents: $contents, tokens: $tokens);
        }

        return [$artifacts, $totalTokens];
    }
}
