<?php

namespace App\Http\Controllers\Core\Patient;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;

class PatientMainController extends Controller
{
    public function getPatient($identificationNumber)
    {
        return DB::table('GENPACIEN')->where('PACNUMDOC', $identificationNumber)->get();
    }
    public function getPatientById($id)
    {
        return DB::table(table: 'GENPACIEN')->where('OID', $id)->get();
    }
}
