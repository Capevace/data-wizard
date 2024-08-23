<?php

namespace App\Models\Actor;

enum ActorMessageType: string
{
    case Text = 'text';
    case Json = 'json';
    case Base64Image = 'base64_image';
    case FunctionInvocation = 'function_invocation';
    case FunctionOutput = 'function_output';
}
