<?php

namespace App;

use Illuminate\Http\Request;
use Spatie\HttpLogger\LogProfile;

class LogAllRequests implements LogProfile
{
    public function shouldLogRequest(Request $request): bool
    {
        return in_array(strtolower($request->method()), ['get', 'post', 'put', 'patch', 'delete']);
    }
}
