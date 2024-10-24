<?php
namespace app\models;
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
            ->distinct(true)
            ->select('year')
            ->from('{{departments}}')
            ->where(['{{departments}}.[[org_code]]' => $codeOrganization])
            ->orderBy('year')
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
                        LEFT JOIN {{vacations_merged}} ON {{vacations_merged}}.[[id_employee]] = {{employees}}.[[id]]
                            AND {{vacations_merged}}.[[year]] = {{departments}}.[[year]]
                    WHERE {{departments}}.[[org_code]] = :org_code
                        AND CURRENT_DATE >= {{vacations_merged}}.[[date_from]] AND CURRENT_DATE <= {{vacations_merged}}.[[date_to]]
                        AND {{departments}}.[[year]] = :year
                ) AS [[total_on_vacations]]
                ,(
                    SELECT 
                        COUNT(DISTINCT {{employees}}.[[id]])
                    FROM {{employees}}
                        RIGHT JOIN {{departments}} ON {{departments}}.[[id]] = {{employees}}.[[id_department]]
                        LEFT JOIN {{vacations_merged}} ON {{vacations_merged}}.[[id_employee]] = {{employees}}.[[id]]			
                    WHERE {{departments}}.[[org_code]] = :org_code                        
                        AND {{vacations_merged}}.[[date_from]] >= (NOW() + MAKE_INTERVAL(DAYS => :intervalDaysFrom))::DATE
                            AND {{vacations_merged}}.[[date_from]] <= (NOW() + MAKE_INTERVAL(DAYS => :intervalDaysTo))::DATE
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