<?php

namespace Capevace\MagicImport\Artifacts;

enum ArtifactType: string
{
    case Text = 'text';
    case Image = 'image';
    case Pdf = 'pdf';
}