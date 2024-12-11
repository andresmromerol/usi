<?php

namespace App\Http\Controllers\Core\MedicalConsultation;

use App\Http\Controllers\Api\Shared\ResponseApi;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Log;



class MedicalTurnMainController extends Controller
{


    public function getMedicalTurnById($id)
    {
        $medicalCenterId = DB::table('CMNHORMED')->select('ADNCENATE')->where('OID', $id)->first();

        if ($medicalCenterId) {
            $medicalCenterId = $medicalCenterId->ADNCENATE;
        }

        return DB::table('ADNCENATE')->where('OID', (int) $medicalCenterId)->get();



    }


    public $medicalTurnGroupDefault = [
        'oid' => -1,
        'doctor_id' => -1,
        'start_date' => -1,
        'end_date' => -1,
        'medical_center_id' => -1,
        'medical_office_id' => -1,
        'type_activity_id' => -1,
        'status' => -1,
        'status_turn_completed' => -1,
        'specialty_id' => -1,
        'superuser_id' => -1,
        'date_created' => -1,
        'status_resource' => -1,
        'resource' => -1,
        'reserved' => -1,
        'type_turn' => -1,
    ];




    public function getMedicalTurnGroup($medicalTurnGroup)
    {
        $responseApi = new ResponseApi();

        $medicalTurnGroup = array_merge($this->medicalTurnGroupDefault, $medicalTurnGroup);

        $query = DB::table('CMNHORMED');
        foreach ($medicalTurnGroup as $key => $value) {
            if ($value !== -1) {
                $column = match ($key) {
                    'oid' => 'OID',
                    'doctor_id' => 'GENMEDICO',
                    'start_date' => 'CHMHORINI',
                    'end_date' => 'CHMHORFIN',
                    'medical_center_id' => 'ADNCENATE',
                    'medical_office_id' => 'CMNCONSUL',
                    'type_activity_id' => 'CMNTIPACT',
                    'status' => 'CHMESTADO',
                    'status_turn_completed' => 'CHMTURCOM',
                    'specialty_id' => 'GENESPECI',
                    'superuser_id' => 'GENUSUARIO1',
                    'date_created' => 'CHMFECCRE',
                    'status_resource' => 'CHMESRECFIS',
                    'resource' => 'CMNRECFIS',
                    'reserved' => 'CHMRESERVADO',
                    'type_turn' => 'CHMTIPOTURNO',
                    default => $key,
                };

                // Aplica condiciones específicas para fechas
                if ($key === 'start_date') {
                    $query->whereDate($column, '>=', $value);
                } elseif ($key === 'end_date') {
                    $query->whereDate($column, '<=', $value);
                } else {
                    $query->where($column, $value);
                }
            }
        }
        $query->orderBy('CHMHORINI', 'asc');

        return $query->get();
    }


    public function generateScheduledAppointments($rangeMedicalTurn, $intervalsBetweenMedicalTurn)
    {

        $scheduledAppointments = [];

        foreach ($rangeMedicalTurn as $turn) {
            $startDateTime = Carbon::parse($turn->CHMHORINI);
            $endDateTime = Carbon::parse($turn->CHMHORFIN);

            while ($startDateTime->lt($endDateTime)) {
                $intervalInSeconds = Carbon::parse($intervalsBetweenMedicalTurn)->hour * 3600 +
                    Carbon::parse($intervalsBetweenMedicalTurn)->minute * 60 +
                    Carbon::parse($intervalsBetweenMedicalTurn)->second;

                $appointmentEnd = $startDateTime->copy()->addSeconds($intervalInSeconds);

                if ($appointmentEnd->gt($endDateTime)) {
                    break;
                }

                $scheduledAppointments[] = [
                    'OID' => $turn->OID,
                    'GENMEDICO' => $turn->GENMEDICO,
                    'RANGEINI' => $turn->CHMHORINI,
                    'RANGEFIN' => $turn->CHMHORFIN,
                    'CCMFECCIT' => $startDateTime->format('Y-m-d H:i:s'),
                    'CCMFINCIT' => $appointmentEnd->format('Y-m-d H:i:s'),
                    'ADNCENATE' => $turn->ADNCENATE,
                    'CMNCONSUL' => $turn->CMNCONSUL,
                    'CMNTIPACT' => $turn->CMNTIPACT,
                    'CHMESTADO' => $turn->CHMESTADO,
                    'CHMTURCOM' => $turn->CHMTURCOM,
                    'GENESPECI' => $turn->GENESPECI,
                    'GENUSUARIO1' => $turn->GENUSUARIO1,
                    'CHMFECCRE' => $turn->CHMFECCRE,
                    'CHMESRECFIS' => $turn->CHMESRECFIS,
                    'CMNRECFIS' => $turn->CMNRECFIS,
                    'OptimisticLockField' => $turn->OptimisticLockField,
                    'CHMRESERVADO' => $turn->CHMRESERVADO,
                    'CHMTIPOTURNO' => $turn->CHMTIPOTURNO,
                ];

                $startDateTime->addSeconds($intervalInSeconds);
            }
        }

        return $scheduledAppointments;

    }


}