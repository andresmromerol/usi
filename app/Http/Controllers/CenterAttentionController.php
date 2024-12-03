<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class CenterAttentionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        try {
        $results = DB::table('ADNCENATE') ->select('OID', 'ACACODIGO', 'ACANOMBRE', 'AFAPREFIJ', 'GENPRESAL', 'INNALMACE1', 'GENDEPEND1', 'ACACODIPS', 'CTNCUENTA', 'GENDETCON', 'ACAACTINAC', 'ACATIPFUE', 'CTNCUENTA1', 'CTNCENCOS1', 'GENMUNICI', 'ACADIRCENT', 'ACATELCENT', 'ACAEMAIL', 'OptimisticLockField', 'ACACODIGOHAB', 'CTNCENCOS', 'CTNCENCOS2', 'ACAPRINCIPAL', 'ACAIDENTISAP', 'TSNCAJA')
         ->where('ACAACTINAC', 1)
          ->get();

          return response()->json([
            "content" => [
                "message" => "Centro de atención obtenido correctamente",
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
                "message" => "Error al obtener centro de atención",
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
