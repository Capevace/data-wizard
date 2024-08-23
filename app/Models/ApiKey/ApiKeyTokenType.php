<?php

namespace App\Models\ApiKey;

enum ApiKeyTokenType: string
{
    case Token = 'token';
    case Organization = 'organization';
}
