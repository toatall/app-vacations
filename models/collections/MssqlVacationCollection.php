<?php
namespace app\models\collections;
use app\models\VacationItem;

/**
 * {@inheritDoc}
 * 
 * Метод prepare используется для извлечения информации из массива.
 * Предполагается использовать для извлечения данных полученных от MSSQL Server с кадровой БД
 */
class MssqlVacationCollection extends AbstractVacationCollection
{
    /**
     * Код организации
     * @var string
     */
    private $codeOrganization;

    /**
     * @param string $codeOrganization
     */
    public function __construct(string $codeOrganization)
    {
        $this->codeOrganization = $codeOrganization;
    }

    /**
     * {@inheritDoc}
     * Извлечение данных из массива
     * @param array $row массив данных (строка)
     * @return \app\models\VacationItem
     */
    protected function prepare($row): VacationItem
    {        
        return new VacationItem([
            'dateStart' => trim($row['DATE_BEGIN']),
            'dateEnd' => trim($row['DATE_END']),
            'fullName' => trim($row['FIO']),
            'organization' => $this->codeOrganization,
            'department' => trim($row['DEPARTMENT_NAME']),
            'departmentSortIndex' => trim($row['DEPARTMENT_CODE']),
            'post' => trim($row['POST_NAME']),
            'employeeSortIndex' => trim($row['POST_CODE']),
            'kindVacation' => $this->extractKindVacation(trim($row['KIND_VACATION'])),
            'status' => '',
        ]);
    }
    /**
     * Извлечение вида отпуска (создание сокращенного варианта)
     * @param string $value
     * @return string
     */
    private function extractKindVacation($value)
    {
        if (preg_match('/основн/', $value)) {
            return 'ежегодный основной отпуск';
        }
        if (preg_match('/ненормированный/', $value)) {
            return 'ежегодный дополнительный отпуск за ненормированный рабочий день';
        }
        if (preg_match('/Крайнего Севера/', $value)) {
            return 'ежегодный дополнительный отпуск за работу в местности, приравненной к районам Крайнего Севера';
        }
        if (preg_match('/выслугу/', $value)) {
            return 'ежегодный дополнительный отпуск за выслугу лет';
        }
        return $value;
    }  

}