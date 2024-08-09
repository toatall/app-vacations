<?php
namespace app\models;
use yii\db\Expression;
use yii\db\Query;

class VacationStatistics
{

    /**
     * Summary of getYearsByOrganization
     * @param string $codeOrganization
     * @return array
     */
    public function getYearsByOrganization(string $codeOrganization): array
    {
        $resultQuery = (new Query())
            ->select(new Expression("DATE_PART('year', [[date_from]]) AS [[year]]"))
            ->distinct(true)
            ->from('{{vacations}}')
            ->leftJoin('{{employees}}', '{{employees}}.[[id]] = {{vacations}}.[[id_employee]]')
            ->leftJoin('{{departments}}', '{{departments}}.[[id]] = {{employees}}.[[id_department]]')
            ->where(['{{departments}}.[[org_code]]' => $codeOrganization])
            ->orderBy(new Expression("DATE_PART('year', [[date_from]])"))
            ->all();
        return $resultQuery;
    }    

    
    /**
     * Статистика по сотрудников (всего, в отпуске, пойдут в отпуск (дополнительно задается интервал))
     * @param string $codeOrganization код организации
     * @param string $year год (даты начала отпуска)
     * @param int $intervalDaysFrom интервал дней (от) для расчета будущих отпускников (по умолчанию 1)
     * @param int $intervalDaysTo интервал дней (до) для расчета будущих отпускников (по умолчанию 7)
     * @return array    
     */
    public function getTotals(string $codeOrganization, string $year, int $intervalDaysFrom, int $intervalDaysTo)
    {
        $resultQuery = \Yii::$app->db->createCommand(<<<SQL
            SELECT 
                {{t}}.[[total]]
                ,{{t}}.[[total_on_vacations]]
                ,{{t}}.[[total_will_be_on_vacations]]
            FROM
            (
            SELECT 
                (
                    SELECT 
                        COUNT(DISTINCT {{employees}}.[[id]]) 
                    FROM {{employees}}
                        RIGHT JOIN {{departments}} ON {{departments}}.[[id]] = {{employees}}.[[id_department]]
                        LEFT JOIN {{vacations}} ON {{vacations}}.[[id_employee]] = {{employees}}.[[id]]
                    WHERE {{departments}}.[[org_code]] = :org_code                       
                        AND {{departments}}.[[year]] = :year
                    ) AS [[total]]                        
                ,(
                    SELECT 
                        COUNT(DISTINCT {{employees}}.[[id]])
                    FROM {{employees}}
                        RIGHT JOIN {{departments}} ON {{departments}}.[[id]] = {{employees}}.[[id_department]]
                        LEFT JOIN {{vacations}} ON {{vacations}}.[[id_employee]] = {{employees}}.[[id]]
                    WHERE {{departments}}.[[org_code]] = :org_code 
                        AND NOW() BETWEEN {{vacations}}.[[date_from]] AND {{vacations}}.[[date_to]]
                        AND {{departments}}.[[year]] = :year
                ) AS [[total_on_vacations]]
                ,(
                    SELECT 
                        COUNT(DISTINCT {{employees}}.[[id]])
                    FROM {{employees}}
                        RIGHT JOIN {{departments}} ON {{departments}}.[[id]] = {{employees}}.[[id_department]]
                        LEFT JOIN {{vacations}} ON {{vacations}}.[[id_employee]] = {{employees}}.[[id]]			
                    WHERE {{departments}}.[[org_code]] = :org_code                        
                        AND {{vacations}}.[[date_from]] BETWEEN (NOW() + MAKE_INTERVAL(DAYS => :intervalDaysFrom))::DATE AND (NOW() + MAKE_INTERVAL(DAYS => :intervalDaysTo))::DATE
                        AND {{departments}}.[[year]] = :year
                ) AS [[total_will_be_on_vacations]]
            ) AS {{t}}
        SQL, [
            ':org_code' => $codeOrganization,
            ':year' => $year,
            ':intervalDaysFrom' => $intervalDaysFrom,
            ':intervalDaysTo' => $intervalDaysTo,
        ])->queryOne();

        return [
            'total' => $resultQuery['total'] ?? 0,
            'total_on_vacations' => $resultQuery['total_on_vacations'] ?? 0,
            'total_will_be_on_vacations' => $resultQuery['total_will_be_on_vacations'] ?? 0,
        ];
    }
}