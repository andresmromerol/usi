<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Api\Shared\ResponseApi;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\Patient\PatientMainController;
use Illuminate\Http\Request;
use Validator;

class PatientController extends Controller
{
    public function getPatient(Request $request)
    {

        $responseApi = new ResponseApi();
        $patient = new PatientMainController();
        try {

            $validationParams = Validator::make(
                $request->all(),

                [
                    'identification_number' => ['required', 'string', 'max:255'],

                ]
            );

            $validationParams->setAttributeNames(['identification_number' => 'número de identificación']);

            $customErrors = $responseApi->getParametersErros($validationParams);
            if (count($customErrors) > 0) {
                return $responseApi->response("Se ha encontrado errores en los parametros", [], $customErrors, 221, "ERROR", false, 442);
            }







            $resp = $patient->getPatient($request->identification_number);

            return $responseApi->response("Paciente obtenido correctamente", $resp, [], 122, "OK", true, 200);



        } catch (\Exception $e) {


            return $responseApi->response("Error al obtener los pacientes", [], [], 120, "ERROR", false, 500);


        }


    }
}
