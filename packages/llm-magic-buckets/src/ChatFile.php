<?php

namespace Mateffy\Magic\Buckets;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Storage;
use Livewire\Wireable;

class ChatFile implements Wireable
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $ai_summary,
        public string $mime_type,
        public int $size,
        public CarbonImmutable $created_at,
    )
    {
    }

    public static function fromCloudArtifact(CloudArtifact $cloudArtifact): static
    {
        return new static(
            id: $cloudArtifact->id,
            name: "{$cloudArtifact->name}.{$cloudArtifact->extension}",
            ai_summary: $cloudArtifact->ai_summary,
            mime_type: $cloudArtifact->mime_type,
            size: $cloudArtifact->size,
            created_at: $cloudArtifact->created_at->toImmutable(),
        );
    }

    public function artifact(): ?CloudArtifact
    {
        return CloudArtifact::find($this->id);
    }

    public function url(): ?string
    {
        $media = $this->artifact()->original();

        if (! $media) {
            return null;
        }

        return $media->getTemporaryUrl(
            expiration: now()->addMinutes(5),
        );
    }

    public function contents(): ?string
    {
        $media = $this->artifact()->original();

        if (! $media) {
            return null;
        }

        $disk = Storage::disk($media->disk);

        return $disk->get($media->getPath());
    }

    public function base64(): ?string
    {
        $contents = $this->contents();

        if (! $contents) {
            return null;
        }

        return base64_encode($contents);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'ai_summary' => $this->ai_summary,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }

    public static function from(array $data): static
    {
        return new static(
            id: $data['id'],
            name: $data['name'],
            ai_summary: $data['ai_summary'],
            mime_type: $data['mime_type'],
            size: $data['size'],
            created_at: CarbonImmutable::parse($data['created_at']),
        );
    }

    public function toLivewire()
    {
        return $this->toArray();
    }

    public static function fromLivewire($value)
    {
        return static::from($value);
    }
}
