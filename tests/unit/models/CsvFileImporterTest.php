<?php

namespace tests\unit\models;

use app\fixtures\OrganizationFixture;
use app\models\collections\SimpleVacationCollection;
use app\models\importers\CsvFileImporter;

class CsvFileImporterTest extends \Codeception\Test\Unit
{    
    public function _fixtures()
    {
        return [
            'user' => [
                'class' => OrganizationFixture::class,
                'dataFile' => codecept_data_dir() . 'organization.php'
            ]
        ];
    }
    
    public function testLoad()
    {
        $codeOrg = '8600';
        $year = '2024';
        $csvFile = \Yii::getAlias('@app/tests/_files/import-data.csv');
        $csvFileImporter = new CsvFileImporter($csvFile, new SimpleVacationCollection($codeOrg), $codeOrg, $year);
        $csvFileImporter->load();
        
        $resQuery = \Yii::$app->db->createCommand('SELECT * FROM "vacations"')->queryAll();
        $this->assertCount(10, $resQuery);
    }

    public function testLoadBadFileLong()
    {
        $codeOrg = '8600';
        $year = '2024';
        $csvFile = \Yii::getAlias('@app/tests/_files/import-data-bad-long.csv');
        $csvFileImporter = new CsvFileImporter($csvFile, new SimpleVacationCollection($codeOrg), $codeOrg, $year);
        try {
            $csvFileImporter->load(); 
            $this->assertTrue(false);
        }
        catch(\Exception $exception) {
            $this->assertTrue(true);
        }
    }

    public function testLoadBadFileShort()
    {
        $codeOrg = '8600';
        $year = '2024';
        $csvFile = \Yii::getAlias('@app/tests/_files/import-data-bad-short.csv');
        $csvFileImporter = new CsvFileImporter($csvFile, new SimpleVacationCollection($codeOrg), $codeOrg, $year);
        try {
            $csvFileImporter->load(); 
            $this->assertTrue(false);
        }
        catch(\Exception $exception) {
            $this->assertTrue(true);
        }
    }

}