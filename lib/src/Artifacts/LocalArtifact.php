<?php

namespace Capevace\MagicImport\Artifacts;

use Capevace\MagicImport\Artifacts\Content\ImageContent;
use Capevace\MagicImport\Artifacts\Content\TextContent;
use Capevace\MagicImport\Config\ExtractorFileType;
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
    )
    {
    }

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
            ->map(fn($content) => match ($content['type']) {
                'text' => TextContent::from($content),
                'image' => ImageContent::from($content),
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

        return "{$this->path}/embeds/{$filename}";
    }

    /**
     * Splits the document. Adds data until either the character limit or embed limit is reached, then starts a new split.
     * @return array<array<TextContent|ImageContent>>
     */
    public function split(int $maxCharacters, int $maxEmbedCount): array
    {
        $splits = [];
        $contents = [];

        $characters = 0;
        $embedCount = 0;

        foreach ($this->getContents() as $content) {
            if ($content instanceof TextContent) {
                $characters += strlen($content->text);
            } elseif ($content instanceof ImageContent) {
                $embedCount++;
            }

            if ($characters > $maxCharacters || $embedCount > $maxEmbedCount) {
                $splits[] = $contents;
                $contents = [];
                $characters = 0;
                $embedCount = 0;
            }

            $contents[] = $content;
        }

        if (!empty($contents)) {
            $splits[] = $contents;
        }

        return $splits;
    }
}