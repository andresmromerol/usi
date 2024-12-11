<?php

namespace App\Http\Controllers\Core\MedicalConsultation;

use App\Http\Controllers\Api\Shared\ResponseApi;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class MedicalConsultationMainController extends Controller
{

    function getConsultationsForMonth($startOfMonth, $endOfMonth, $patientId)
    {

        $CANCELLED = [1, 3, 5];
        $startOfMonth = Carbon::parse($startOfMonth)->format('Y-m-d H:i:s');
        $endOfMonth = Carbon::parse($endOfMonth)->format('Y-m-d H:i:s');

        return DB::table('CMNCITMED')->when($startOfMonth, function ($query, $startOfMonth) {
            return $query->whereDate('CCMFECASI', '>=', $startOfMonth);
        })->when($endOfMonth, function ($query, $endOfMonth) {
            return $query->whereDate('CCMFECCIT', '<=', $endOfMonth);
        })->when($patientId, function ($query, $patientId) {
            return $query->where('GENPACIEN', $patientId);
        })->whereNotIn('CCMESTADO', $CANCELLED)->get();

    }


    function getConsultationsForMonthWithStatus($startOfMonth, $endOfMonth, $patientId, $status)
    {

        $ASSIGNED = 0;
        $startOfMonth = Carbon::parse($startOfMonth)->format('Y-m-d H:i:s');
        $endOfMonth = Carbon::parse($endOfMonth)->format('Y-m-d H:i:s');
        return DB::table('CMNCITMED')->when($startOfMonth, function ($query, $startOfMonth) {
            return $query->whereDate('CCMFECASI', '>=', $startOfMonth);
        })->when($endOfMonth, function ($query, $endOfMonth) {
            return $query->whereDate('CCMFECCIT', '<=', $endOfMonth);
        })->when($patientId, function ($query, $patientId) {
            return $query->where('GENPACIEN', $patientId);
        })->where('CCMESTADO', '=', $status)->get();
    }

    function getAllConsultationsForMonthWithStatus($patientId, $status)
    {

        return DB::table('CMNCITMED')->when($patientId, function ($query, $patientId) {
            return $query->where('GENPACIEN', $patientId);
        })->where('CCMESTADO', '=', $status)->get();

    }



    function cancelConsultation($oid, $feccan, $caucan)
    {


        $CCMESTADO = 1;
        $GENUSUARIO2 = 1;
        $CCMFECCAN = $feccan;
        $CMNCAUCAN = $caucan;
        // Ejecutar la actualización 
        $updated = DB::table('CMNCITMED')
            ->where('OID', $oid)
            ->update(['CCMESTADO' => $CCMESTADO, 'GENUSUARIO2' => $GENUSUARIO2, 'CCMFECCUM' => $CCMFECCAN]);
        return $updated > 0;



    }


    function checkTimeDifference($oid, $feccan, $limitHour)
    {

        $responseApi = new ResponseApi();

        $record = DB::table('CMNCITMED')->select('CCMFECCIT')->where('OID', $oid)->first();

        if ($record) {
            $startDate = Carbon::parse($record->CCMFECCIT);
            $endDate = Carbon::parse($feccan);

            if ($endDate->greaterThan($startDate)) {
                return true;
            }

            $differenceInHours = $endDate->diffInHours($startDate);
            $res = $differenceInHours < $limitHour;
            return $res;
        } else {
            return false;
        }
    }



    public function getBySpecialityAndStatusAndPatient($genSpeci, $status, $patientId)
    {
        return DB::table('CMNCITMED')->where('GENESPECI', $genSpeci)->where('CCMESTADO', $status)->where('GENPACIEN', $patientId)->get();
    }

    public function filterScheduledAppointments($scheduledAppointments)
    {
        try {

            foreach ($scheduledAppointments as $key => $appointment) {
                $existingAppointment = DB::table('CMNCITMED')
                    ->where('CMNHORMED', $appointment['OID'])
                    ->whereRaw("CONVERT(VARCHAR, CCMFECCIT, 120) = ?", [$appointment['CCMFECCIT']])
                    ->whereRaw("CONVERT(VARCHAR, CCMFINCIT, 120) = ?", [$appointment['CCMFINCIT']])
                    ->where('CMNTIPACT', $appointment['CMNTIPACT'])
                    ->where('GENESPECI', $appointment['GENESPECI'])
                    ->where('CCMESTADO', '!=', 0)
                    ->first();

                if ($existingAppointment) {
                    unset($scheduledAppointments[$key]);
                }
            }

            return $scheduledAppointments;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }




    public function filterScheduledAppointmentsWithExlude($scheduledAppointments)
    {
        try {
            $removedAppointments = [];

            foreach ($scheduledAppointments as $key => $appointment) {
                $existingAppointment = DB::table('CMNCITMED')
                    ->where('CMNHORMED', $appointment['OID'])
                    ->whereRaw("CONVERT(VARCHAR, CCMFECCIT, 120) = ?", [$appointment['CCMFECCIT']])
                    ->whereRaw("CONVERT(VARCHAR, CCMFINCIT, 120) = ?", [$appointment['CCMFINCIT']])
                    ->where('CMNTIPACT', $appointment['CMNTIPACT'])
                    ->where('GENESPECI', $appointment['GENESPECI'])
                    ->where('CCMESTADO', '!=', 0)
                    ->first();

                if ($existingAppointment) {
                    $removedAppointments[] = $appointment;
                    unset($scheduledAppointments[$key]);
                }
            }

            return [
                'available' => array_values($scheduledAppointments),
                'removed' => $removedAppointments
            ];
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function create($data)
    {

        $nullableDates = ['CCMFECCAN', 'CCMFECCUM', 'CCMFECINA'];
        foreach ($nullableDates as $nullableDate) {
            if (empty($data[$nullableDate])) {
                $data[$nullableDate] = null;
            }
        }
        // Insertar los datos en la base de datos
        DB::table('CMNCITMED')->insert($data);

        return response()->json(['message' => 'Registro creado con éxito.'], 201);
    }

}
