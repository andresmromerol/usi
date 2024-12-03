<?php

namespace App\Http\Controllers\Api\MedicalConsultation;

use App\Http\Controllers\Api\Shared\ResponseApi;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\MedicalConsultation\TypeActivityMainController;
use Illuminate\Http\Request;
use Validator;

class TypeActivityController extends Controller
{
    public function getTypeActivity(Request $request)
    {




        $responseApi = new ResponseApi();
        $specialityId = $request->specialty_id;
        $typeActivity = new TypeActivityMainController();


        try {



            $validationParams = Validator::make(
                $request->all(),

                [
                    'specialty_id' => ['required', 'integer', 'max:10000000'],

                ],
                [
                    'specialty_id.required' => 'La especialidad debe ser seleccionada.',
                    'specialty_id.integer' => 'La especialidad debe ser seleccionada.',
                    'specialty_id.max' => 'La especialidad debe ser seleccionada',
                ]
            );

            $validationParams->setAttributeNames(['specialty_id' => ' especialidad']);

            $customErrors = $responseApi->getParametersErros($validationParams);

            if (count($customErrors) > 0) {
                return $responseApi->response("Se ha encontrado errores en los parametros", [], $customErrors, 171, "ERROR", false, 442);
            }


            $resp = $typeActivity->getTypesActivityBySpecialty($specialityId);

            return $responseApi->response("Tipos de actividades obtenidas correctamente", $resp, [], 172, "OK", true, 200);

        } catch (\Exception $e) {
            return $responseApi->response("Error al obtener los tipos de actividades", $e->getMessage(), [], 170, "ERROR", false, 500);
        }

    }


}

