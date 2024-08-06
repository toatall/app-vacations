<?php

namespace app\controllers;

use app\components\SharedDataFilter;
use app\models\Vacation;
use tebe\inertia\web\Controller;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * Табель отпусков
 */
class TableController extends Controller
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
                        'roles' => ['@']
                    ]
                ]
            ],
            [
                'class' => SharedDataFilter::class
            ]
        ];
    }

    /**
     * Табель
     * @return array|string
     */
    public function actionIndex()
    {
        return $this->inertia('Table/Index', []);
    }

    /**
     * Данные для табеля
     * @param string $code_org
     * @param string $year
     * @return array
     */
    public function actionTableData(string $code_org, string $year)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Vacation::vacationsPerDayByDepartments($code_org, $year);
    }
    
}
