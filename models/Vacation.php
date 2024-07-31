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

}