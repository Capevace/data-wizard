<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class ForceSetupIfNeededMiddleware
{
	public function handle(Request $request, Closure $next)
	{
        if (!User::query()->exists() && !$request->is('setup') && !$request->is('livewire/*')) {
            return redirect(route('setup'));
        }

		return $next($request);
	}
}
