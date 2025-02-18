<?php

namespace app\commands;

use app\models\collections\MssqlVacationCollection;
use app\models\importers\MssqlImporter;
use Yii;
use yii\console\Controller;

class MssqlLoaderController extends Controller
{
    public function actionIndex($codeOrganization, $year): void
    {    
        $sqlConfig = require Yii::getAlias('@app/config/dbImport.php');
        (new MssqlImporter(
            sqlConfig: $sqlConfig, 
            collection: new MssqlVacationCollection(codeOrganization: $codeOrganization), 
            codeOrganization: $codeOrganization, 
            year: $year)
        )->run();
    }
}
