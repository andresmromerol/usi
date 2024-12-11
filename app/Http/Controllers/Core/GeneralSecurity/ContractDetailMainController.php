<?php

namespace App\Http\Controllers\Core\GeneralSecurity;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;

class ContractDetailMainController extends Controller
{
    public function getIdRelatedSanitas(){
        return DB::table('GENDETCON')
                ->where('GDENOMBRE', 'LIKE', '%SANITAS%')
                ->pluck('OID');
    }
    public function doesPatientBelongSanitas($patientIdentificationNumber ){
        return DB::table('GENPACIEN')
                ->where('PACNUMDOC', $patientIdentificationNumber)
                ->whereIn('GENDETCON', $this->getIdRelatedSanitas())->exists();
    }

}
