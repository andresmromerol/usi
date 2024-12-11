<?php

namespace App\Http\Controllers\Api\MedicalConsultation;

use App\Http\Controllers\Api\Shared\ResponseApi;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\MedicalConsultation\MedicalConsultationMainController;
use App\Http\Controllers\Core\Patient\PatientMainController;
use Artisan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class MedicalConsultationController extends Controller
{
    public function generatelink()
    {

        Artisan::call('storage:link');

    }



    public function cancelMedicalConsultation(Request $request)
    {
        $responseApi = new ResponseApi();
        $medicalConsultation = new MedicalConsultationMainController();
        $patient = new PatientMainController();

        try {



            $validationParams = Validator::make(
                $request->all(),

                [
                    'oid' => ['required', 'integer', 'max:10000000'],
                    'feccan' => ['required', 'string', 'max:255']

                ]

            );


            $validationParams->setAttributeNames(
                [

                    'oid' => 'identificador de la consulta medica',
                    'feccan' => 'fecha de cancelación de la consulta medica'


                ]
            );

            $customErrors = $responseApi->getParametersErros($validationParams);

            if (count($customErrors) > 0) {
                return $responseApi->response("Se ha encontrado errores en los parametros", [], $customErrors, 211, "ERROR", false, 442);
            }



            $timeAllowedCancellationHasExpired = $medicalConsultation->checkTimeDifference($request->oid, $request->feccan, 24);


            if ($timeAllowedCancellationHasExpired) {
                return $responseApi->response("El periodo de tiempo para cancelar la cita medica ha vencido", $timeAllowedCancellationHasExpired, [], 214, "ERROR", false, 204);

            }

            $wasUpdated = $medicalConsultation->cancelConsultation($request->oid, $request->feccan, $request->caucan);

            if ($wasUpdated) {
                return $responseApi->response("Cita medica cancelada", [], [], 212, "OK", true, 212);

            } else {
                return $responseApi->response("La citas medica no fue cancelada", [], [], 213, "ERROR", false, 500);

            }


        } catch (\Exception $e) {
            return $responseApi->response("Error al cancelar la cita medica", $e->getMessage(), [], 210, "ERROR", false, 500);
        }



    }






    public function getMedicalConsultation(Request $request)
    {
        $responseApi = new ResponseApi();
        $medicalConsultation = new MedicalConsultationMainController();
        $patient = new PatientMainController();

        try {

            $validationParams = Validator::make(
                $request->all(),

                [
                    'feccit' => ['required', 'string', 'max:255'],
                    'fincit' => ['required', 'string', 'max:255'],
                    'pacien_id' => ['required', 'integer', 'max:10000000'],
                    'estado' => ['required', 'integer', 'max:10000000'],


                ]

            );

            $validationParams->setAttributeNames(
                [

                    'feccit' => 'fecha inicial de la cita',
                    'fincit' => 'fecha final de la cita',
                    'pacien_id' => 'paciente',
                    'estado' => 'estado'
                ]
            );

            $customErrors = $responseApi->getParametersErros($validationParams);

            if (count($customErrors) > 0) {
                return $responseApi->response("Se ha encontrado errores en los parametros", [], $customErrors, 201, "ERROR", false, 442);
            }

            $resp = $medicalConsultation->getConsultationsForMonthWithStatus($request->feccit, $request->fincit, $request->pacien_id, $request->estado);

            return $responseApi->response("Citas medicas obtenida", $resp, [], 192, "OK", true, 202);

        } catch (\Exception $e) {
            return $responseApi->response("Error al obtener las citas medicas", $e->getMessage(), [], 200, "ERROR", false, 500);
        }



    }



    public function getAllMedicalConsultation(Request $request)
    {
        $responseApi = new ResponseApi();
        $medicalConsultation = new MedicalConsultationMainController();
        $patient = new PatientMainController();

        try {



            $validationParams = Validator::make(
                $request->all(),

                [
                    'pacien_id' => ['required', 'integer', 'max:10000000'],
                    'estado' => ['required', 'integer', 'max:10000000'],


                ]

            );


            $validationParams->setAttributeNames(
                [


                    'pacien_id' => 'paciente',
                    'estado' => 'estado'
                ]
            );

            $customErrors = $responseApi->getParametersErros($validationParams);

            if (count($customErrors) > 0) {
                return $responseApi->response("Se ha encontrado errores en los parametros", [], $customErrors, 241, "ERROR", false, 442);
            }

            $resp = $medicalConsultation->getAllConsultationsForMonthWithStatus($request->pacien_id, $request->estado);

            return $responseApi->response("Citas medicas obtenida", $resp, [], 242, "OK", true, 202);

        } catch (\Exception $e) {
            return $responseApi->response("Error al obtener las citas medicas", $e->getMessage(), [], 240, "ERROR", false, 500);
        }



    }




    public function create(Request $request)
    {
        $responseApi = new ResponseApi();
        $medicalConsultation = new MedicalConsultationMainController();
        $patient = new PatientMainController();

        try {



            $validationParams = Validator::make(
                $request->all(),

                [
                    'authorization_file' => ['required', 'mimes:pdf', 'max:2048'],
                    'authorization' => ['required', 'string', 'max:255'],
                    'hormed_id' => ['required', 'integer', 'max:10000000'],
                    'tipact_id' => ['required', 'integer', 'max:10000000'],
                    'especi_id' => ['required', 'integer', 'max:10000000'],
                    'feccit' => ['required', 'string', 'max:255'],
                    'fincit' => ['required', 'string', 'max:255'],
                    'estcit' => ['required', 'integer', 'max:10000000'],
                    'pacien_id' => ['required', 'integer', 'max:10000000'],
                    'medico_id' => ['required', 'integer', 'max:10000000'],
                    'fecpac' => ['required', 'string', 'max:255'], // fecha propuesta por paciente
                    'fecasi' => ['required', 'string', 'max:255'], //fecha actual


                ]

            );


            $validationParams->setAttributeNames(
                [
                    'authorization_file' => 'archivo de autorizacion',
                    'authorization' => 'número de autorización',
                    'hormed_id' => 'turno medico',
                    'tipact_id' => 'tipo de actividad',
                    'especi_id' => 'especialidad',
                    'feccit' => 'fecha inicial de la cita',
                    'fincit' => 'fecha final de la cita',
                    'estcit' => 'tipo de la cita',
                    'pacien_id' => 'paciente',
                    'medico_id' => 'medico',
                    'fecpac' => 'fecha propuesta por el paciente',
                    'fecasi' => 'fecha de asignación',
                ]
            );

            $customErrors = $responseApi->getParametersErros($validationParams);

            if (count($customErrors) > 0) {
                return $responseApi->response("Se ha encontrado errores en los parametros", [], $customErrors, 191, "ERROR", false, 442);
            }


            // validar si existe alguna especialidad del mismo tipo y estado asignado

            $MEDICAL_CONSULTATION_STATUS_ASSIGNMENT = 0; //ENUMERACIÓN: DG.Modelos.CitasMedicas.eCMEstadoCita
            $ifSameSpecialtyExists = !$medicalConsultation->getBySpecialityAndStatusAndPatient($request->especi_id, $MEDICAL_CONSULTATION_STATUS_ASSIGNMENT, $request->pacien_id)->isEmpty();


            if ($ifSameSpecialtyExists) {
                return $responseApi->response("El paciente tiene una cita asignada con la misma especialidad", [], [], 193, "ERROR", false, 500);

            }

            // validar si en el mes tiene mas de cuatro citas con cualquier especialidad

            list($start, $end) = $responseApi->getMonthStartEnd($request->fecasi);

            $consultationsForMonth = $medicalConsultation->getConsultationsForMonth($start, $end, $request->pacien_id);

            if ($consultationsForMonth->count() > 0) {
                return $responseApi->response("El paciente tiene mas de cuatro citas asignada en este mes", [], [], 194, "ERROR", false, 500);
            }




            // obtener paciente
            $pacientModel = $patient->getPatientById($request->pacien_id);


            $archive_path = $request->authorization_file->store('public/authorization_file');
            $resp = str_replace('public', 'storage', $archive_path);




            $firstMedicalAppointment = $request->estcit ? 1 : 0; //ESTILO DE CITA: PRIMERA_VEZ = 0, CONTROL = 1, REMISIÓN = 2 ENUMERACIÓN: DG.MODELOS.CITASMEDICAS.ECMESTILOCITA

            $defaultValues = [
                'CMNHORMED' => $request->hormed_id, //TURNOMEDICO
                'CMNTIPACT' => $request->tipact_id, //ACTIVIDAD
                'GENESPECI' => $request->especi_id, //ESPECIALIDAD
                'CCMFECCIT' => Carbon::parse($responseApi->dateFormat($request->feccit))->format('d-m-Y H:i:s'), //FECHA INICIAL CITA
                'CCMFINCIT' => Carbon::parse($responseApi->dateFormat($request->fincit))->format('d-m-Y H:i:s'), //FECHA FINAL CITA
                'CCMESTADO' => 0, // asignado - ESTADO CITA
                'CCMTIPASI' => 4, // internet - TIPO ASIGNACION
                'CCMESTCIT' => $firstMedicalAppointment, //ESTILO CITA 
                'CCMTIPCIT' => 0, // normal - TIPO CITA  
                'GENPACIEN' => $request->pacien_id, //PACIENTE

                'CCMPACDOC' => $pacientModel->pluck('PACNUMDOC')->first(), //PACIENTEDOCUMENTO


                //  'CCMPACNOM' => $pacientModel->pluck('PACPRINOM')->first() . " " . $pacientModel->pluck('PACSEGNOM')->first(), // PACIENTENOMBRE
                'CCMPACNOM' => 'xxJoxhnxDoexx', // PACIENTENOMBRE


                'CCMPACTEL' => $pacientModel->pluck('GENPACIENT')->first(), //PACIENTETELEFONO
                'CCMOBSERV' => '', //OBSERVACIONES
                'SLNFACTUR' => null, // FACTURA
                'GENUSUARIO1' => 569, // superusuario //USUARIO QUE ASIGNA 
                'CCMFECASI' => Carbon::parse($responseApi->dateFormat($request->fecasi))->format('d-m-Y H:i:s'), //FECHA ASIGNACION
                'GENUSUARIO2' => null, //USUARIO QUE CANCELA
                'CCMFECCAN' => null, //FECHA CANCELACION 
                'CMNCAUCAN' => null, //CAUSA DE CANCELACION
                'GENUSUARIO3' => null, //USUARIO QUE CUMPLE 
                'CCMFECCUM' => null, // FECHA CUMPLIMIENTO
                'SLNSERHOJ' => null, //SERVICIO HOJA TRABAJO
                'GENMEDICO1' => $request->medico_id, //MEDICO
                'GENMEDICO2' => null, //MEDICOAUXILIAR
                'CPNSOLICIC' => null, //SOLICITUDINSUMOS
                'GENSERIPS' => null, // SERVICIO IPS --------------------- QUEDA NULL
                'GENARESER' => 187, // area de servicio ----------------- AREA SERVICIO
                'CCMNUMAUTO' => $request->authorization, //NUMERO AUTORIZACION 
                'CCMTIPATE' => 1, // electiva - TIPO ATENCION
                'CCMCONPAC' => 0, // ninguna - CITA CONDICIONADA POR PACIENTE
                'CMNREMCIT' => null, // REMISION CITA ESPECIALIZADA 
                'GENUSUARIO4' => null, //USUARIO INATENCION
                'CCMFECINA' => null, //FECHA INATENCION
                'CCMREFERE' => null, // REFERENCIA
                'GENDIAGNO' => null, //DIAGNOSTICO
                'CCMFECPAC' => Carbon::parse($responseApi->dateFormat($request->fecpac))->format('d-m-Y H:i:s'), //FECHA REQUIERE CITA
                'CCMESTCOD' => null, //ESTABLECIMIENTO CODIGO 
                'CCMESTNOM' => null, //ESTABLECIMIENTO NOMBRE
                'CCMFECINCM' => null, //FECHA INCUMPLIMIENTO
                'CCMOBSINCM' => null, //OBSERVACION INCUMPLIMIENTO
                'GENUSUARIO5' => null, //USUARIO QUE INCUMPLE
                'OptimisticLockField' => 0, // que es OptimisticLockField
                'GENDETCON' => null, // obtener el gendetcon del paciente eps - PLAN BENEFICIO ATENCION
                'CCMREPUSU' => null, // revisar enum - REPROGRAMACIÓN USUARIO
                'CCMREPINS' => null, // revisar enum - ES REPROGRAMADA 
                'CMNCITMED1' => null, // REPROGRAMACIÓN INSTITUCIÓN
                'CCMCITFRE' => null, // revisar enum - CITA DE FRECUENCIA
                'CCMCONLLE' => null, // revisar enum - CONFIRMA LLEGADA
                'CCMFECLLE' => null, //FECHA CONFIRMA LLEGADA
                'GEENENTADM1' => null, //ENTIDAD A FACTURAR
                'GENDETCON1' => null, //PLAN BENEFICIO FACTURAR
                'GENUSUARIO6' => null, //USUARIO QUE MARCA LLEGADA
                'CCMCITGRUFRE' => null, //CITA AGRUPACIÓN FRECUENCIA 
                'HCNORDPRESONC' => null, //ORDEN DE PRESCRIPCION ONCOLOGICA
                'HCNCONTRDT' => null, //DIGITURNO
                'CMTELEMED' => null, //TELEMEDICINA
                'CMTELEMEDNS' => null, //TELEMEDICINANOMBRESESION
                'CCMPACTIPDOC' => null, //PACIENTETIPODOCUMENTO
                'CMTIPASICIT' => null, //TIPO DE ASIGNACIÓN CITA ************************
                'CMCODCOTIZAN' => null,
                'HCNSOLPNQX' => null,
                'ADNCOMDER' => null, //VERIFICACIÓN DE DERECHOS 
                'CMVALSERVI' => null,
                'CMVALSERVREA' => null, //VALOR DEL SERVICIO FUE REALIZADO
                'CMVALFECPAGO' => null,  //FECHA DE PAGO (PRESENCIAL O VIRTUAL)
                'CMTIPOPAGO' => 0, // confirmar enum - TIPO DE PAGO 
                'CMCLAVEPAGO' => null,
                'PYPNPROMCIC' => null, //PROGRAMAS(CICLOS)
                'GENPACIENRA' => null, //PACIENTE REASIGNADO
                'CCMRESPAGO' => null, // OBJETO OBTENIDO EN EL CONSUMO DEL PAGO DE LA CITA (COLPATRIA O PLACETOPAY)
                'CCMIDTRANSAC' => null, //IDENTIFICACIÓN DE LA TRANSACCIÓN DE(COLPATRIA O PLACETOPAY)
                'CCMESTTRAN' => null,
                'CCMIDTRANHIS' => null, //IDENTIFICACIÓN HISTÓRICOS DE LAS TRANSACCIONES PLACETOPAY

                'CCMURLPAGLIN' => null, //URL DONDE SE EFECTÚO O SE DEBE EFECTÚAR EL PAGO EN LÍNEA PLACE TO PAY

                'CCMEMAILENV' => 0, // MUESTRA SI EL CORREO FUE ENVIADO CORRECTAMENTE

                'CCMEMAIL' => null, //CONTIENE EL CORREO DEL PACIENTE SI NO EXISTE
                'CCMTELPRINCIP' => null, //CONTIENE EL TELEFONO DEL PACIENTE SI NO EXISTE

                'ADNINGRESO' => null, // INGRESO
                'TSNMRECIB' => null, //RECIBOCAJADETALLE
                'CCMIDMAQAUTPAG' => null, //IDENTIFICADOR DE LA MÁQUINA DE AUTOPAGO QUE REALIZO EL PAGO

                'CMNSERDET2' => null, // SOLICITUDSERVICIOSDETALLE
                'GENPLADIA' => null // DIAGNOSTICOPLANBENEFICIOS
            ];


            // return $responseApi->response("Cita medica creada", $defaultValues, [], 192, "OK", true, 200);

            $medicalConsultation->create($defaultValues);


            return $responseApi->response("Cita medica creada", $resp, [], 192, "OK", true, 200);

        } catch (\Exception $e) {
            return $responseApi->response("Error al crear la cita medica", $e->getMessage(), [], 190, "ERROR", false, 500);
        }



    }
}
