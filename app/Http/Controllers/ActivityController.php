<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function  index(Request $request)
    {
        



                     try {

                        
                        $results = DB::table('CMNESPACT') ->select('Actividades')
                         ->where('Especialidades', $request->especialidades_id)
                         ->pluck('Actividades');




                         $actividades = DB::table('CMNTIPACT') ->select('*') 
                         ->whereIn('OID', $results)
                         ->where('CMAUSOWEB', 1)
                         ->get();

                
                          return response()->json([
                            "content" => [
                                "message" => "Tipos de actividades obtenido correctamente",
                                "data" => $actividades
                            ],
                            "status" => [
                                "code" => 220,
                                "reason" => "OK",
                                "success" => true
                            ]
                
                        ], 200);
                
                    } catch (\Exception $e) {
                
                        return response()->json([
                            "content" => [
                                "message" => "Error al obtener los tipos de actividades",
                                "data" => $e->getMessage()
                            ],
                            "status" => [
                                "code" => 230,
                                "reason" => "ERROR",
                                "success" => false
                            ]
                
                        ], 400);
                
                
                
                
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
