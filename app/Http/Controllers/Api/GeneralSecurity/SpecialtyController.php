<?php

namespace App\Http\Controllers\Api\GeneralSecurity;

use App\Http\Controllers\Api\Shared\ResponseApi;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\Admission\MedicalCenterMainController;
use App\Http\Controllers\Core\GeneralSecurity\ContractDetailMainController;
use App\Http\Controllers\Core\GeneralSecurity\SpecialtyMainController;
use DB;
use Illuminate\Http\Request;
use Validator;



class SpecialtyController extends Controller
{





    public function getSpecialty(Request $request)
    {

        $responseApi = new ResponseApi();
        $contractDetailMain = new ContractDetailMainController();

        try {

            $speciality = new SpecialtyMainController();
            $patientIdentificationNumber = $request->identification_number;

            $validationParams = Validator::make(
                $request->all(),

                [
                    'identification_number' => ['required', 'string', 'max:255'],

                ],
                [
                    'identification_number.required' => 'El número de identificación es obligatorio.',
                    'identification_number.string' => 'El número de identificación debe ser una cadena de texto.',
                    'identification_number.max' => 'El número de identificación no puede tener más de 255 caracteres.',
                ]
            );

            $validationParams->setAttributeNames(['identification_number' => 'número de identificación']);

            $customErrors = $responseApi->getParametersErros($validationParams);
            if (count($customErrors) > 0) {
                return $responseApi->response("Se ha encontrado errores en los parametros", [], $customErrors, 161, "ERROR", false, 442);
            }

            $doesPatientBelongsSanitas = $contractDetailMain->doesPatientBelongSanitas($patientIdentificationNumber);

            $resp = [];
            if ($doesPatientBelongsSanitas) {

                $resp = $speciality->getSanitasSpecialties();

            } else {

                $resp = $speciality->getSpecialtiesOtherThanSanitas();
            }

            return $responseApi->response("Especialidades obtenido correctamente", $resp, [], 162, "OK", true, 200);

        } catch (\Exception $e) {
            return $responseApi->response("Error al obtener las especialidades", [], [], 160, "ERROR", false, 500);
        }

    }
}
