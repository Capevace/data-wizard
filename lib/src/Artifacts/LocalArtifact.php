<?php

namespace Capevace\MagicImport\Artifacts;

use Capevace\MagicImport\Artifacts\Content\ImageContent;
use Capevace\MagicImport\Artifacts\Content\TextContent;
use Capevace\MagicImport\Config\ExtractorFileType;
use Illuminate\Support\Facades\File;
use JsonException;
use Throwable;

/**
 * Artifact directory:
 * /artifacts/<ID>
 * /artifacts/<ID>/metadata.json
 * /artifacts/<ID>/source.<EXT>
 * /artifacts/<ID>/thumbnail.jpg
 * /artifacts/<ID>/embeds (optional)
 * /artifacts/<ID>/embeds/<FILENAME>.jpg
 */
readonly class LocalArtifact implements Artifact
{
    public function __construct(
        protected ArtifactMetadata $metadata,
        protected string $path,
    ) {}

    /**
     * @throws JsonException
     */
    public static function fromPath(string $path): static
    {
        $metadata = ArtifactMetadata::fromPath("{$path}/metadata.json");

        return new static(
            metadata: $metadata,
            path: $path,
        );
    }

    public function getMetadata(): ArtifactMetadata
    {
        return $this->metadata;
    }

    public function getSourcePath(): string
    {
        return "{$this->path}/{$this->getSourceFilename()}";
    }

    public function getSourceFilename(): string
    {
        return "source.{$this->metadata->extension}";
    }

    public function getContents(): array
    {
        $json = file_get_contents("{$this->path}/contents.json");
        $data = json_decode($json, associative: true, flags: JSON_THROW_ON_ERROR);

        return collect($data)
            ->map(fn ($content) => match ($content['type']) {
                'text' => TextContent::from($content),
                'image', 'page-image' => ImageContent::from($content),
                default => null
            })
            ->filter()
            ->all();
        //
        //        return [
        //            new TextContent(text: 'Raw text / OCR text', page: 1),
        //            new ImageContent(filename: 'embeds/thumbnail.jpg', page: 1),
        //            new TextContent(text: 'Raw text / OCR text', page: 2),
        //        ];
    }

    public function getText(): ?string
    {
        try {
            return file_get_contents("{$this->path}/source.txt");
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }
    }

    public function getEmbedPath(string $filename): string
    {
        // Allows an Image artifact's contents to be expressed in the contents.json file too by using the source filename
        if ($filename === $this->getSourceFilename() && in_array($this->metadata->mimetype, ExtractorFileType::IMAGES)) {
            return $this->getSourcePath();
        }

        return "{$this->path}/{$filename}";
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
            if ($content instanceof TextContent) {
                // TODO: Actually count tokens, because 1 character !== 1 token
                // TODO: We can also split the text, if the tokens for this text are higher than the maxTokens
                $tokens += strlen($content->text);
                $totalTokens += strlen($content->text);

                $contents[] = $content;
            } elseif ($content instanceof ImageContent) {
                if ($content->width && $content->height) {
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
