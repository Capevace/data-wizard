<?php

namespace Mateffy\Magic\Providers\ApiKey;

enum ApiKeyTokenType: string
{
    case Token = 'token';
    case Organization = 'organization';
}
