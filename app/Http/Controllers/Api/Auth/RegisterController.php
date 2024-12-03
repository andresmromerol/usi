<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware("guest");
    }
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {

 
        $validationParams = Validator::make(
            $request->all(),

            [
                'identification_number' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'string', Rule::unique(User::class, 'email')],
                'password' => 'required|string|min:8|confirmed'
            ],
            [
                'identification_number.required' => 'El número de identificación es obligatorio.',
                'identification_number.string' => 'El número de identificación debe ser una cadena de texto.',
                'identification_number.max' => 'El número de identificación no puede tener más de 255 caracteres.',
                'email.required' => 'El correo electrónico es obligatorio.',
                'email.email' => 'El correo electrónico debe ser una dirección válida.',
                'email.string' => 'El correo electrónico debe ser una cadena de texto.',
                'email.unique' => 'El correo electrónico ya está registrado.',
                'password.required' => 'La contraseña es obligatoria.',
                'password.string' => 'La contraseña debe ser una cadena de texto.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'password.confirmed' => 'La confirmación de la contraseña no coincide.'
            ]
        );

        $validationParams->setAttributeNames(['identification_number' => 'número de identificación', 'email' => 'correo electrónico', 'password' => 'contraseña',]);

        if ($validationParams->fails()) {
            $errors = $validationParams->errors()->toArray();
            $customErrors = [];
            foreach ($errors as $field => $messages) {
                foreach ($messages as $message) {
                    $customErrors[] = ['field' => $field, 'message' => $message];
                }
            }



            return response()->json([
                "content" => [
                    "message" => "Se ha encontrado errores en los parametros",
                    "errors" => $customErrors
                ],
                "status" => [
                    "code" => 110,
                    "reason" => "ERROR",
                    "success" => false
                ]
            ], 442);






        }





        $user = User::where("identification_number", $request->identification_number)->first();


        if (!$user) {

            $user_exists = DB::table('GENPACIEN')->where('PACNUMDOC', $request->identification_number)->exists();

            if ($user_exists) {


                $user = User::create([
                    "identification_number" => $request->identification_number,
                    "email" => $request->email,
                    "password" => bcrypt($request->password)
                ]);

                $token = $user->createToken("auth_token")->plainTextToken;

                return response()->json([
                    "content" => [
                        "message" => "Usuario registrado",
                        "errors" => [],
                        "access_token" => $token,
                        "token_type" => "Bearer"
                    ],
                    "status" => [
                        "code" => 111,
                        "reason" => "CREATED",
                        "success" => true
                    ]
                ], 201);





















            } else {
                return response()->json([


                    "content" => [
                        "message" => "El número de identificación no esta relacionado a un paciente",
                        "errors" => [],
                    ],
                    "status" => [
                        "code" => 102,
                        "reason" => "UNAUTHORIZED",
                        "success" => false
                    ]

                ], 401);
            }
        }



















    }
}
