<?php

namespace App\Http\Controllers\Api\Admission;

use App\Http\Controllers\Api\Shared\ResponseApi;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\Admission\MedicalCenterMainController;
use DB;
use Illuminate\Http\Request;

class MedicalCenterController extends Controller
{

    public function getMedicalCenter()
    {
        $medicalCenter = new MedicalCenterMainController();
        $responseApi = new ResponseApi();

        try {
            $getMedicalCenter = $medicalCenter->getMedicalCenter();
            return $responseApi->response("Centro de atención obtenido correctamente", $getMedicalCenter, [], 151, "OK", true, 200);
        } catch (\Exception $e) {
            return $responseApi->response("Error al obtener centro de atención", [], [], 150, "ERROR", false, 500);
        }
    }
}
