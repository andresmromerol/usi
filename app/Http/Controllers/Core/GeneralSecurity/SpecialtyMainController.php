<?php

namespace App\Http\Controllers\Core\GeneralSecurity;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
enum EpsEnum: string
{
    case PRECODGIO_SANITAS = 'EPS005';
}

enum EspecialidadesEnum: string
{
    case GEECODIGO_PSICOLOGIA = '590';
    case GEECODIGO_PSIQUIATRIA = '591';
    case GEECODIGO_GINICOLOGIA_OBSTETRICIA = '341';
    case GEECODIGO_NUTRICION_CLINICA = '450';
    case GEECODIGO_MEDICINA_FAMILIAR = '385';
    case GEECODIGO_PEDIATRIA = '550';
    case GEECODIGO_MEDICINA_GENERAL = '998';
    case GEECODIGO_ODONTOLOGIA_GENERAL = '997';
}

class SpecialtyMainController extends Controller
{

    public function getSanitasSpecialtyById($id)
    {

        return $query = DB::table('GENESPECI')
            ->where('OID', 1)
            ->get();


    }

    public function getSanitasSpecialties()
    {
        return DB::table('GENESPECI')->select('OID', 'GEECODIGO', 'GEEDESCRI', 'GEETIEMPO', 'GEECTLCON', 'GEENOTRIAGE', 'GENARESER', 'GECODSIUS', 'OptimisticLockField', 'GENOSOLINTCON', 'GECODHOEF')
            ->whereIn(
                'GEECODIGO',
                [
                    EspecialidadesEnum::GEECODIGO_GINICOLOGIA_OBSTETRICIA,
                    EspecialidadesEnum::GEECODIGO_MEDICINA_FAMILIAR,
                    EspecialidadesEnum::GEECODIGO_MEDICINA_GENERAL,
                    EspecialidadesEnum::GEECODIGO_NUTRICION_CLINICA,
                    EspecialidadesEnum::GEECODIGO_ODONTOLOGIA_GENERAL,
                    EspecialidadesEnum::GEECODIGO_PEDIATRIA,
                    EspecialidadesEnum::GEECODIGO_PSICOLOGIA,
                    EspecialidadesEnum::GEECODIGO_PSIQUIATRIA
                ]
            )
            ->get();

    }

    public function getSpecialtiesOtherThanSanitas()
    {

        return DB::table('GENESPECI')->select('OID', 'GEECODIGO', 'GEEDESCRI', 'GEETIEMPO', 'GEECTLCON', 'GEENOTRIAGE', 'GENARESER', 'GECODSIUS', 'OptimisticLockField', 'GENOSOLINTCON', 'GECODHOEF')
            ->whereIn(
                'GEECODIGO',
                [
                    EspecialidadesEnum::GEECODIGO_MEDICINA_GENERAL,
                    EspecialidadesEnum::GEECODIGO_ODONTOLOGIA_GENERAL,
                ]
            )
            ->get();
    }
}
