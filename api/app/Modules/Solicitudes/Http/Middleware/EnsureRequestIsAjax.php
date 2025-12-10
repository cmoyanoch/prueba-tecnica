<?php

declare(strict_types=1);

namespace App\Modules\Solicitudes\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRequestIsAjax
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            abort(403, 'Only AJAX requests are allowed');
        }

        return $next($request);
    }
}
