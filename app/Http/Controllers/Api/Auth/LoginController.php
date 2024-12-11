<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Validator;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {

        $validationParams = Validator::make(
            $request->all(),
            [
                'identification_number' => 'required|string|max:255',
                'password' => 'required|string|min:8'
            ],

            [
                'identification_number.required' => 'El número de identificación es obligatorio.',
                'identification_number.string' => 'El número de identificación debe ser una cadena de texto.',
                'identification_number.max' => 'El número de identificación no puede tener más de 255 caracteres.',

                'password.required' => 'La contraseña es obligatoria.',
                'password.string' => 'La contraseña debe ser una cadena de texto.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            ]
        );

        $validationParams->setAttributeNames(['identification_number' => 'número de identificación', 'password' => 'contraseña']);


        if ($validationParams->fails()) {
            $errors = $validationParams->errors()->toArray();
            $customErrors = [];
            foreach ($errors as $field => $messages) {
                foreach ($messages as $message) {
                    $customErrors[$field][] = $message;
                }
            }
            if ($validationParams->fails()) {

                return response()->json([

                    "content" => [
                        "message" => "Se ha encontrado errores en los parametros",
                        "errors" => $customErrors,

                    ],
                    "status" => [
                        "code" => 100,
                        "reason" => "ERROR",
                        "success" => false
                    ]

                ], 442);

            }
        }



        $user = User::where("identification_number", $request->identification_number)->first();


        if (!$user) {

            $user_exists = DB::table('GENPACIEN')->where('PACNUMDOC', $request->identification_number)->exists();

            if ($user_exists) {
                return response()->json([

                    "content" => [
                        "message" => "El paciente existe pero requiere ser registrado",
                        "errors" => []

                    ],
                    "status" => [
                        "code" => 101,
                        "reason" => "OK",
                        "success" => true
                    ]

                ], 200);
            } else {
                return response()->json([


                    "content" => [
                        "message" => "El número de identificación no esta relacionado a un paciente",
                        "errors" => []
                    ],
                    "status" => [
                        "code" => 102,
                        "reason" => "UNAUTHORIZED",
                        "success" => false
                    ]

                ], 401);
            }
        }


        // validacion de parametros


        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                "content" => [
                    "message" => "Las credenciales son incorrectas",
                    "errors" => []
                ],
                "status" => [
                    "code" => 103,
                    "reason" => "UNAUTHORIZED",
                    "success" => false
                ]

            ], 401);
        }

        $token = $user->createToken("auth_token")->plainTextToken;
        return response()->json([

            "content" => [
                "message" => "Inicio de sesión correcto",
                "errors" => [],
                "access_token" => $token,
                "token_type" => "Bearer"
            ],
            "status" => [
                "code" => 104,
                "reason" => "OK",
                "success" => true
            ]

        ], 200);

    }
}