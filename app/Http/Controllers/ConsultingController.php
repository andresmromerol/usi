<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class ConsultingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
 

        $results = DB::table('CMNCONSUL') ->select('OID', 'CCNCODIGO', 'CCNNOMBRE', 'CCNURGEN', 'ADNCENATE', 'OptimisticLockField', 'GENARESER', 'CMNUBIFIS') 
        ->where('ADNCENATE', $request->adncenate_id) 
        ->limit(1000) ->get();

        return response()->json([
            "data" => $results
                     ], 201);
        
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
