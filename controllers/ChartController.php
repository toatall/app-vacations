<?php

namespace app\controllers;

use app\models\Chart;
use tebe\inertia\web\Controller;
use Yii;
use yii\web\Response;

/**
 * График отпусков
 */
class ChartController extends Controller
{

    /**
     * Данные для графика отпусков 
     * Ежедневное количество отпускников по организации за год
     * @param string $year год
     * @param string $code_org код организации
     * @return array[]
     */
    public function actionCountOfVacationsPerYearByDay(string $code_org, string $year)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'data' => Chart::countOfVacationsPerYearByDay($year, $code_org),
        ];
    }

}