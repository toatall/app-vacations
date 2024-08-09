<?php

namespace app\controllers;

use app\components\SharedDataFilter;
use app\models\Vacation;
use tebe\inertia\web\Controller;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * Информация об отпусках
 */
class VacationsController extends Controller
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
                ]
            ],
            [
                'class' => SharedDataFilter::class,
            ]
        ];
    }
    

    /**
     * Сотрудники в отпуске на текущий момент
     * @param string $code_org код организации
     * @param string $year отчетный год
     * @return array
     */
    public function actionEmployeesOnVacations(string $code_org, string $year)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Vacation::employeesOnVacations($code_org, $year);
    }

    /**
     * Сотрудники идущие в отпуск в заданный интервал (например, неделя)
     * @param string $code_org код организации
     * @param string $year отчетный год
     * @param int $int_days_from интервал дней (от) (по умолчанию 1)
     * @param int $int_days_to интервал дней (до) (по умолчанию 7)
     * @return array
     */
    public function actionEmployeesWillBeOnVacations(string $code_org, string $year, int $int_days_from, int $int_days_to)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Vacation::employeesWillBeOnVacations($code_org, $year, $int_days_from, $int_days_to);
    }

    /**
     * Страница поиска сотрудников, отпусков
     * @return array|string
     */
    public function actionFind()
    {        
        return $this->inertia('Find/Index');
    }

    /**
     * Данные для страницы поиска сотрудников и отпусков
     * @param string $code_org код организации
     * @param string $year отчетный год
     * @return array
     */
    public function actionFindData(string $code_org, string $year)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Vacation::mergedVacationsPerDayByDepartments($code_org, $year); 
    }
    
}
