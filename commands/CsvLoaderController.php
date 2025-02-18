<?php

namespace app\commands;

use app\models\collections\FileVacationCollection;
use app\models\importers\CsvFileImporter;
use yii\console\Controller;

class CsvLoaderController extends Controller
{
    public function actionIndex($filename, $codeOrganization, $year, $csvSeparator = ',')
    {        
        (new CsvFileImporter($filename, new FileVacationCollection($codeOrganization), $codeOrganization, $year, $csvSeparator))->run();        
    }
}
