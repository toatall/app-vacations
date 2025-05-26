<?php

namespace app\commands;

use app\models\collections\FileVacationCollection;
use app\models\importers\CsvFileImporter;
use yii\console\Controller;
use yii\helpers\Console;

class CsvLoaderController extends Controller
{
    public function actionIndex($filename, $codeOrganization, $year, $csvSeparator = ',')
    {        
        $this->stdout("Загрузка отпусков: файл {$filename}, код организации {$codeOrganization}, отчетный год {$year}, csv-разделитель '{$csvSeparator}'". PHP_EOL);
        (new CsvFileImporter($filename, new FileVacationCollection($codeOrganization), $codeOrganization, $year, $csvSeparator))->run();        
        $this->stdout('Загрузка завершена' . PHP_EOL, Console::FG_GREEN);
    }
}
