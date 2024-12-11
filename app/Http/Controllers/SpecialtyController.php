<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

enum EpsEnum: string { 
    case PRECODGIO_SANITAS = 'EPS005'; 
}

enum EspecialidadesEnum: string { 
    case GEECODIGO_PSICOLOGIA = '590'; 
    case GEECODIGO_PSIQUIATRIA = '591';
    case GEECODIGO_GINICOLOGIA_OBSTETRICIA = '341';
    case GEECODIGO_NUTRICION_CLINICA = '450';
    case GEECODIGO_MEDICINA_FAMILIAR = '385';
    case GEECODIGO_PEDIATRIA = '550';
    case GEECODIGO_MEDICINA_GENERAL = '998';
    case GEECODIGO_ODONTOLOGIA_GENERAL = '997';
}



class SpecialtyController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        try {
  
            $validatedData = $request->validate([ 
                'identification_number' => 'required|string|max:255',  
            ]); 
    
    // comprobar que el paciente tenga la eps sanitas
    
  
    $extraer_ids_sanitas = DB::table('GENDETCON') 
    ->where('GDENOMBRE', 'LIKE', '%SANITAS%')
     ->pluck('OID');


    $paciente_pertenece_sanitas = DB::table('GENPACIEN') 
    ->where('PACNUMDOC', $request->identification_number) 
    ->whereIn('GENDETCON', $extraer_ids_sanitas) ->exists();
    


    $results=[];
    if($paciente_pertenece_sanitas){
    
        $results = DB::table('GENESPECI') ->select('OID', 'GEECODIGO', 'GEEDESCRI', 'GEETIEMPO', 'GEECTLCON', 'GEENOTRIAGE', 'GENARESER', 'GECODSIUS', 'OptimisticLockField', 'GENOSOLINTCON', 'GECODHOEF') 
        ->whereIn('GEECODIGO', 
        [
            EspecialidadesEnum::GEECODIGO_GINICOLOGIA_OBSTETRICIA, 
            EspecialidadesEnum::GEECODIGO_MEDICINA_FAMILIAR,
            EspecialidadesEnum::GEECODIGO_MEDICINA_GENERAL,
            EspecialidadesEnum::GEECODIGO_NUTRICION_CLINICA,
            EspecialidadesEnum::GEECODIGO_ODONTOLOGIA_GENERAL,
            EspecialidadesEnum::GEECODIGO_PEDIATRIA,
            EspecialidadesEnum::GEECODIGO_PSICOLOGIA,
            EspecialidadesEnum::GEECODIGO_PSIQUIATRIA
            ]) 
        ->limit(1000) ->get();
    
    }else{
        $results = DB::table('GENESPECI') ->select('OID', 'GEECODIGO', 'GEEDESCRI', 'GEETIEMPO', 'GEECTLCON', 'GEENOTRIAGE', 'GENARESER', 'GECODSIUS', 'OptimisticLockField', 'GENOSOLINTCON', 'GECODHOEF') 
        ->whereIn('GEECODIGO', 
        [
            EspecialidadesEnum::GEECODIGO_MEDICINA_GENERAL,
            EspecialidadesEnum::GEECODIGO_ODONTOLOGIA_GENERAL,
            ]) 
        ->limit(1000) ->get();
    
    
    
    }
    
    
    
              return response()->json([
                "content" => [
                    "message" => "Especialidades obtenido correctamente",
                    "data" => $results
                ],
                "status" => [
                    "code" => 109,
                    "reason" => "OK",
                    "success" => true
                ]
    
            ], 200);
    
        } catch (\Exception $e) {
    
            return response()->json([
                "content" => [
                    "message" => "Error al obtener las especialidades",
                    "data" => $e->getMessage()
                ],
                "status" => [
                    "code" => 500,
                    "reason" => "ERROR",
                    "success" => false
                ]
    
            ], 200);
    
    
    
    
        }
    
    
    
    
    
    
    













    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
