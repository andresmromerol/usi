<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Api\Shared\ResponseApi;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function unauthenticated($request, array $guards)
    {
        $responseApi = new ResponseApi();

        // Lanza una excepción con un mensaje personalizado
        throw new HttpResponseException(response()->json([
            "content" => [
                "message" => "No estás autenticado",
                "data" => [],
                "errors" => []
            ],
            "status" => [
                "code" => 121,
                "reason" => "ERROR",
                "success" => false
            ]

        ], 401));
    }
}
