<?php

namespace Mateffy\Magic\Artifacts;

enum ArtifactType: string
{
    case Text = 'text';
    case Image = 'image';
    case Pdf = 'pdf';
}
