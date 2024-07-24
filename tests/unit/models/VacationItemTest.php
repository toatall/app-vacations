<?php

namespace tests\unit\models;

use app\models\VacationItem;

class VacationItemTest extends \Codeception\Test\Unit
{    

    public function testDateUTC()
    {
        $data = ['01.01.2023', '21.02.2023', 'Петров Иван Сергеевич', 'Организация\19 Отдел', 'ежегодного основного отпуска', 'E'];
        $vacationItem = new VacationItem([
            'dateStart' => $data[0],
            'dateEnd' => $data[1],
            'fullName'  => $data[2],
            'department' => $data[3],
            'kindVacation' => $data[4],
            'status' => $data[5],
        ]);        
        $this->assertEquals('2023-01-01', $vacationItem->dateStartUTC);
        $this->assertEquals('2023-02-21', $vacationItem->dateEndUTC);
    }    

}