<?php

namespace app\controllers;

use app\models\VacationStatistics;
use tebe\inertia\web\Controller;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;

class StatisticsController extends Controller
{    

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => AccessControl::class,               
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],            
        ];
    }

    /**
     * @var VacationStatistics
     */
    private $modelStatistics;

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();
        $this->modelStatistics = new VacationStatistics();
        Yii::$app->response->format = Response::FORMAT_JSON;
    }

    /**
     * Статистика по сотрудников
     * - всего
     * - в отпуске
     * - пойдут в отпуск (дополнительно задается интервал)
     * @param string $code_org код организации
     * @param string $year год (даты начала отпуска)
     * @param int $int_days_from интервал дней (от) для расчета будущих отпускников (по умолчанию 1)
     * @param int $int_days_to интервал дней (до) для расчета будущих отпускников (по умолчанию 7)
     * @return array
     * [
     *   'total' => X,
     *   'total_on_vacations' => X,
     *   'total_will_be_on_vacations' => X,
     * ]
     */
    public function actionTotalEmployees(string $code_org, string $year, int $int_days_from, int $int_days_to) 
    {        
        return $this->modelStatistics->getTotals($code_org, $year, $int_days_from, $int_days_to);
    }

    /**
     * Вычисление годов (по дате начала отпуска) в рамках указанной организации
     * @param string $code_org код организации
     * @return array
     */
    public function actionYears(string $code_org)
    {        
        return $this->modelStatistics->getYearsByOrganization($code_org);        
    }

}