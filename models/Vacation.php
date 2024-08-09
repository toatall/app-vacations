<?php
namespace app\models;

class Vacation
{

    /**
     * Сотрудники в отпуске
     * @param mixed $codeOrganization
     * @param mixed $year
     * @return array
     */
    public static function employeesOnVacations(string $codeOrganization, string $year)
    {
        $result = \Yii::$app->getDb()->createCommand(<<<SQL
            SELECT DISTINCT                
                {{employees}}.[[full_name]]
                ,{{employees}}.[[post]]
                ,{{departments}}.[[name]]
                ,{{vacations_merged}}.[[date_from]]
                ,{{vacations_merged}}.[[date_to]]                
            FROM {{employees}}
                RIGHT JOIN {{departments}} ON {{departments}}.[[id]] = {{employees}}.[[id_department]]
                LEFT JOIN {{vacations_merged}} ON {{vacations_merged}}.[[id_employee]] = {{employees}}.[[id]]                
            WHERE {{departments}}.[[org_code]] = :org_code 
                AND NOW() BETWEEN {{vacations_merged}}.[[date_from]] AND {{vacations_merged}}.[[date_to]]
                AND  {{vacations_merged}}.[[year]] = :year
        SQL, [
            ':org_code' => $codeOrganization,
            ':year' => $year,
        ])->queryAll();
        return $result;
    }

    /**
     * Сотрудники идущие в отпуск в указанный интервал
     * @param string $codeOrganization код организации
     * @param string $year отчетный год
     * @param int $intervalDaysFrom интервал дней (от) (по умолчанию 1)
     * @param int $intervalDaysTo интервал дней (до) (по умолчанию 7)
     * @return array
     */
    public static function employeesWillBeOnVacations(string $codeOrganization, string $year, int $intervalDaysFrom, int $intervalDaysTo)
    {
        $result = \Yii::$app->getDb()->createCommand(<<<SQL
            SELECT DISTINCT                
                {{employees}}.[[full_name]]
                ,{{employees}}.[[post]]
                ,{{departments}}.[[name]]
                ,{{vacations_merged}}.[[date_from]]
                ,{{vacations_merged}}.[[date_to]]                
            FROM {{employees}}
                RIGHT JOIN {{departments}} ON {{departments}}.[[id]] = {{employees}}.[[id_department]]
                LEFT JOIN {{vacations_merged}} ON {{vacations_merged}}.[[id_employee]] = {{employees}}.[[id]]                
            WHERE {{departments}}.[[org_code]] = :org_code 
            AND {{vacations_merged}}.[[date_from]] BETWEEN (NOW() + MAKE_INTERVAL(DAYS => :intervalDaysFrom))::DATE AND (NOW() + MAKE_INTERVAL(DAYS => :intervalDaysTo))::DATE
                AND  {{vacations_merged}}.[[year]] = :year
        SQL, [
            ':org_code' => $codeOrganization,
            ':year' => $year,
            ':intervalDaysFrom' => $intervalDaysFrom,
            ':intervalDaysTo' => $intervalDaysTo,
        ])->queryAll();
        return $result;
    }

    /**
     * Список сотрудников с отпусками
     * @param string $codeOrganization код организации
     * @param string $year отчетный год
     * @return array
     */
    public static function vacationsPerDayByDepartments(string $codeOrganization, string $year)
    {
        $result = \Yii::$app->getDb()->createCommand(<<<SQL
            
            SELECT 
                {{departments}}.[[id]] AS "id_department"
                ,{{departments}}.[[name]] AS "department"
                ,{{departments}}.[[sort_index]] AS "sort_index_department"
                ,{{employees}}.[[id]] AS "id_employee"
                ,{{employees}}.[[full_name]]
                ,{{employees}}.[[post]]
                ,{{employees}}.[[sort_index]] AS "sort_index_employee"
                ,{{vacations}}.[[date_from]]
                ,{{vacations}}.[[date_to]]
                ,{{vacations}}.[[status]]                
                ,{{vacations}}.[[id_kind]]
                ,{{vacations_kind}}.[[name]] AS "vacation_kind"
            FROM {{departments}}
                LEFT JOIN {{employees}} ON {{employees}}.[[id_department]] = {{departments}}.[[id]]
                LEFT JOIN {{vacations}} ON {{vacations}}.[[id_employee]] = {{employees}}.[[id]]
                LEFT JOIN {{vacations_kind}} ON {{vacations_kind}}.[[id]] = {{vacations}}.[[id_kind]]
            WHERE {{departments}}.[[org_code]] = :org_code AND {{departments}}.[[year]] = :year
            ORDER BY 
                {{departments}}.[[sort_index]] ASC
                ,{{departments}}.[[name]] ASC
                ,{{employees}}.[[sort_index]] ASC
                ,{{employees}}.[[full_name]] ASC
                ,{{vacations}}.[[date_from]] ASC

        SQL, [
            ':org_code' => $codeOrganization,
            ':year' => $year,
        ])->queryAll();

        return $result;
    }

    /**
     * Список сотрудников с объединенными отпусками
     * @param string $codeOrganization
     * @param string $year
     * @return array
     */
    public static function mergedVacationsPerDayByDepartments(string $codeOrganization, string $year)
    {
        $result = \Yii::$app->getDb()->createCommand(<<<SQL
            
            SELECT 
                {{departments}}.[[id]] AS "id_department"
                ,{{departments}}.[[name]] AS "department"
                ,{{departments}}.[[sort_index]] AS "sort_index_department"
                ,{{employees}}.[[id]] AS "id_employee"
                ,{{employees}}.[[full_name]]
                ,{{employees}}.[[post]]
                ,{{employees}}.[[sort_index]] AS "sort_index_employee"
                ,{{vacations_merged}}.[[date_from]]
                ,{{vacations_merged}}.[[date_to]]
            FROM {{departments}}
                LEFT JOIN {{employees}} ON {{employees}}.[[id_department]] = {{departments}}.[[id]]
                LEFT JOIN {{vacations_merged}} ON {{vacations_merged}}.[[id_employee]] = {{employees}}.[[id]]                
            WHERE {{departments}}.[[org_code]] = :org_code AND {{departments}}.[[year]] = :year
                AND {{vacations_merged}}.[[year]] = {{departments}}.[[year]]
            ORDER BY 
                {{departments}}.[[sort_index]] ASC
                ,{{departments}}.[[name]] ASC
                ,{{employees}}.[[sort_index]] ASC
                ,{{employees}}.[[full_name]] ASC
                ,{{vacations_merged}}.[[date_from]] ASC

        SQL, [
            ':org_code' => $codeOrganization,
            ':year' => $year,
        ])->queryAll();

        return $result;
    }


}