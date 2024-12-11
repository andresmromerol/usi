<?php

namespace App\Http\Controllers\Core\MedicalConsultation;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class TypeActivityMainController extends Controller
{
    public function getTypesActivityBySpecialty($specialityIdentificationNumber)
    {

        $relation = DB::table('CMNESPACT')->select('Actividades')
            ->where('Especialidades', $specialityIdentificationNumber)
            ->pluck('Actividades');

        return DB::table('CMNTIPACT')->select('*')
            ->whereIn('OID', $relation)
            ->where('CMAUSOWEB', 1)
            ->get();


    }

    public function getTypesActivityById($id)
    {
        return DB::table('CMNTIPACT')
            ->where('OID', $id)
            ->get();
    }

    public function getDurationFromTypesActivity($id)
    {
        $data = $this->getTypesActivityById($id);
        $dateTimeArray = $data->pluck('CMADURACT');
        $dateTime = $dateTimeArray[0];
        return Carbon::parse($dateTime)->format('H:i:s');

    }
}
