<?php

namespace App\Http\Controllers\Api\MedicalConsultation;

use App\Http\Controllers\Api\Shared\ResponseApi;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\GeneralSecurity\SpecialtyMainController;
use App\Http\Controllers\Core\MedicalConsultation\MedicalConsultationMainController;
use App\Http\Controllers\Core\MedicalConsultation\MedicalTurnMainController;
use App\Http\Controllers\Core\MedicalConsultation\TypeActivityMainController;
use Illuminate\Http\Request;
use Validator;

class MedicalTurnController extends Controller
{


    public function getMedicalCenterByMedicalTurn(Request $request)
    {


        $responseApi = new ResponseApi();
        $specialityId = $request->specialty_id;
        $typeActivity = new TypeActivityMainController();

        $medicalTurn = new MedicalTurnMainController();
        try {



            $validationParams = Validator::make(
                $request->all(),

                [
                    'cmnhormed_id' => ['required', 'integer', 'max:10000000'],

                ]
            );

            $validationParams->setAttributeNames(['cmnhormed_id' => 'turno medico']);

            $customErrors = $responseApi->getParametersErros($validationParams);

            if (count($customErrors) > 0) {
                return $responseApi->response("Se ha encontrado errores en los parametros", [], $customErrors, 231, "ERROR", false, 442);
            }


            $resp = $medicalTurn->getMedicalTurnById($request->cmnhormed_id);

            return $responseApi->response("Centro de costo obtenido correctamente", $resp, [], 232, "OK", true, 200);

        } catch (\Exception $e) {
            return $responseApi->response("Error al obtener el centro de costo", $e->getMessage(), [], 230, "ERROR", false, 500);
        }

    }






    public function getAvailableMedicalTurn(Request $request)
    {



        $responseApi = new ResponseApi();
        $specialtyMain = new SpecialtyMainController();

        $availableMedicalTurnGroupParams = [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'medical_center_id' => $request->medical_center_id,
            'type_activity_id' => $request->type_activity_id,
            'status' => $request->status,
            'status_turn_completed' => $request->status_turn_completed,
            'specialty_id' => $request->specialty_id,
            'reserved' => $request->reserved,
            'type_turn' => $request->type_turn,
        ];




        $medicalTurn = new MedicalTurnMainController();
        $typeActivity = new TypeActivityMainController();
        $medicalConsultation = new MedicalConsultationMainController();


        try {



            $validationParams = Validator::make(
                $request->all(),

                [
                    'start_date' => ['required', 'string', 'max:255'],
                    'end_date' => ['required', 'string', 'max:255'],
                    'medical_center_id' => ['required', 'integer', 'max:10000000'],
                    'type_activity_id' => ['required', 'integer', 'max:10000000'],
                    'status' => ['required', 'integer', 'max:10000000'],
                    'status_turn_completed' => ['required', 'integer', 'max:10000000'],
                    'specialty_id' => ['required', 'integer', 'max:10000000'],
                    'reserved' => ['required', 'integer', 'max:10000000'],
                    'type_turn' => ['required', 'integer', 'max:10000000'],

                    'pacien_id' => ['required', 'integer', 'max:10000000'],
                    'fecasi' => ['required', 'string', 'max:255'] //fecha actual

                ]

            );


            $validationParams->setAttributeNames(
                [
                    'start_date' => ' fecha inicial',
                    'end_date' => ' fecha final',
                    'medical_center_id' => ' centro medico',
                    'type_activity_id' => ' tipo de actividad',
                    'status' => ' estado del turno',
                    'status_turn_completed' => ' el estado',
                    'specialty_id' => ' especialidad',
                    'reserved' => ' estado de reserva',
                    'type_turn' => 'tipo de turno',



                    'pacien_id' => 'paciente',
                    'fecasi' => 'fecha de asignación'

                ]
            );
            $customErrors = $responseApi->getParametersErros($validationParams);
            if (count($customErrors) > 0) {
                return $responseApi->response("Se ha encontrado errores en los parametros", [], $customErrors, 181, "ERROR", false, 442);
            }










            $MEDICAL_CONSULTATION_STATUS_ASSIGNMENT = 0; //ENUMERACIÓN: DG.Modelos.CitasMedicas.eCMEstadoCita
            $ifSameSpecialtyExists = !$medicalConsultation->getBySpecialityAndStatusAndPatient($request->specialty_id, $MEDICAL_CONSULTATION_STATUS_ASSIGNMENT, $request->pacien_id)->isEmpty();


            if ($ifSameSpecialtyExists) {
                return $responseApi->response("El paciente tiene una cita asignada con la misma especialidad", [], [], 183, "ERROR", false, 500);

            }

            // validar si en el mes tiene mas de cuatro citas con cualquier especialidad

            list($start, $end) = $responseApi->getMonthStartEnd($request->fecasi);

            $consultationsForMonth = $medicalConsultation->getConsultationsForMonth($start, $end, $request->pacien_id);

            if ($consultationsForMonth->count() > 0) {
                return $responseApi->response("El paciente tiene mas de cuatro citas asignada en este mes", [], [], 184, "ERROR", false, 500);
            }



            $rangeMedicalTurn = $medicalTurn->getMedicalTurnGroup($availableMedicalTurnGroupParams);



            $intervalsBetweenMedicalTurn = $typeActivity->getDurationFromTypesActivity($availableMedicalTurnGroupParams["type_activity_id"]); // obtener el tiempo permitido por cada actividad ej:00:20:00 







            $scheduledAppointments = $medicalTurn->generateScheduledAppointments($rangeMedicalTurn, $intervalsBetweenMedicalTurn); // crea rangos de posibles consultas teniendo en cuenta el tiempo para la actividad







            $availableTurn = $medicalConsultation->filterScheduledAppointmentsWithExlude($scheduledAppointments);

            return $responseApi->response("Turnos medicos disponibles obtenidas correctamente", $availableTurn, [], 182, "OK", true, 200);
        } catch (\Exception $e) {
            return $responseApi->response("Error al obtener los turnos medicos disponibles", $e->getMessage(), [], 180, "ERROR", false, 500);
        }
    }
}

