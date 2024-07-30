<?php
namespace app\models;

/**
 * График
 */
class Chart
{
    /**
     * График количества сотрудников в отпуске по дням по организации и за отчетный год
     * @param string $year год
     * @param string $codeOrganization код организации 
     * @return array
     */
    public static function countOfVacationsPerYearByDay(string $year, string $codeOrganization)
    {
        $db = \Yii::$app->db;
        $data = $db->createCommand(<<<SQL
            WITH {{dates}} AS (
                SELECT generate_series(
                    '$year-01-01'::date,
                    '$year-12-31'::date,
                    '1 day'    
                ) AS [[date_of_year]]
            )
            
            SELECT 
                [[date_of_year]]::date AS "x", 
                COUNT(DISTINCT {{vacation_filtered}}.[[id]]) AS "y"
            FROM {{dates}}              
                LEFT JOIN (
                    SELECT 
                        {{vacations}}.[[id]],
                        {{vacations}}.[[date_from]],
                        {{vacations}}.[[date_to]]
                    FROM {{vacations}}
                        LEFT JOIN {{employees}} ON {{employees}}.[[id]] = {{vacations}}.[[id_employee]]
                        LEFT JOIN {{departments}} ON {{departments}}.[[id]] = {{employees}}.[[id_department]]
                    WHERE {{departments}}.[[org_code]] = :org_code
                ) AS {{vacation_filtered}} ON {{dates}}.[[date_of_year]] BETWEEN {{vacation_filtered}}.[[date_from]] AND {{vacation_filtered}}.[[date_to]]
            GROUP BY [[date_of_year]]::date
            ORDER BY [[date_of_year]]::date
        SQL, [
            ':org_code' => $codeOrganization,
        ])->queryAll();

        return $data;        
    }

}