<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use WireElements\WireExtender\Http\Middlewares\IgnoreForWireExtender;

class VerifyCsrfToken extends Middleware
{
    use IgnoreForWireExtender;

    /*
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
