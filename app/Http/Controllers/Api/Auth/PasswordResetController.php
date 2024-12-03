<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LinkEmailRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Mail\ResetPasswordLink;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Validator;

class PasswordResetController extends Controller
{

    public function __construct()
    {
        $this->middleware("guest");
    }

    public function sendResetLinkEmail(Request $request)
    {
        $validationParams = Validator::make(
            $request->all(),

            [
                'email' => ['required', 'email', 'string']
            ],
            [
              
                'email.required' => 'El correo electrónico es obligatorio.',
                'email.email' => 'El correo electrónico debe ser una dirección válida.',
                'email.string' => 'El correo electrónico debe ser una cadena de texto.',
                'email.unique' => 'El correo electrónico ya está registrado.'
              
            ]
        );

        $validationParams->setAttributeNames([ 'email' => 'correo electrónico']);

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
                    "code" => 130,
                    "reason" => "ERROR",
                    "success" => false
                ]
            ], 442);

        }

        $url = URL::temporarySignedRoute("password.reset", now()->addMinutes(30), ["email" => $request->email]);
        $url = str_replace(env("APP_URL"), env("APP_FRONTEND"), $url);

        Mail::to($request->email)->send(new ResetPasswordLink($url));

        return response()->json([

            "content" => [
                "message" => "Se ha enviado a su correo el enlace para la restauración de la contraseña",
                "errors" => []

            ],
            "status" => [
                "code" => 131,
                "reason" => "OK",
                "success" => true
            ]

        ], 200);
    }

    public function reset(Request $request)
    {

 
        $validationParams = Validator::make(
            $request->all(),

            [

                'password' => 'required|string|min:8|confirmed'
            ],
            [
                'password.required' => 'La contraseña es obligatoria.',
                'password.string' => 'La contraseña debe ser una cadena de texto.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'password.confirmed' => 'La confirmación de la contraseña no coincide.'
            ]
        );

        $validationParams->setAttributeNames([ 'password' => 'contraseña']);

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
                    "code" => 140,
                    "reason" => "ERROR",
                    "success" => false
                ]
            ], 442);

        }



        $user = User::where("email", $request->email)->first();

        if (!$user) {
            return response()->json([

                "content" => [
                    "message" => "El correo electronico no existe",
                    "errors" => []
                ],
                "status" => [
                    "code" => 141,
                    "reason" => "NO_CONTENT",
                    "success" => false
                ]

            ], 404);
        }

        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json([

            "content" => [
                "message" => "Contraseña restaurada",
                "errors" => []

            ],
            "status" => [
                "code" => 142,
                "reason" => "OK",
                "success" => true
            ]

        ], 200);
    }
}
