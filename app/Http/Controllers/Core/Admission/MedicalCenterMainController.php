<?php

namespace App\Http\Controllers\Core\Admission;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;

class MedicalCenterMainController extends Controller
{
    public function getMedicalCenter()
    {
        $MEDICAL_CENTER_ACTIVE = 1;

        return DB::table('ADNCENATE')
            ->select('OID', 'ACANOMBRE')
            ->where('ACAACTINAC', $MEDICAL_CENTER_ACTIVE)
            ->get();
    }
}
