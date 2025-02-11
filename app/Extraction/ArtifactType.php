<?php

namespace App\Extraction;

enum ArtifactType: string
{
    case Unknown = 'unknown';
    case Text = 'text';
    case Image = 'image';
    case Pdf = 'pdf';

    public static function fromMimetype(string $mimeType): static
    {
        $str = str($mimeType)->lower();

        return match (true) {
            $str->startsWith('text/') => self::Text,
            $str->startsWith('image/') => self::Image,
            $str->startsWith('application/pdf') => self::Pdf,
            default => self::Unknown,
        };
    }
}
