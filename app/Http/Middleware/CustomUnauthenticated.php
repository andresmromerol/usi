<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Api\Shared\ResponseApi;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class CustomUnauthenticated extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    protected function unauthenticated($request, array $guards)
    {
        $responseApi = new ResponseApi();

        return $responseApi->response("No estas autenticado", [], [], 401, "ERROR", false, 121);
    }
}
