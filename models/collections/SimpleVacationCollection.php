<?php
namespace app\models\collections;
use app\models\VacationItem;

/**
 * {@inheritDoc}
 * 
 * Метод prepare используется для извлечения информации из массива.
 * Предполагается использовать для извлечения данных полученных:
 * - из csv-файла
 * - из soap-сервиса
 */
class SimpleVacationCollection extends AbstractVacationCollection
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
            'dateStart' => $this->getDateStartFromArray($row),
            'dateEnd' => $this->getDateEndFromArray($row),
            'fullName' => $this->getFullNameFromArray($row),
            'organization' => $this->codeOrganization,
            'department' => $this->getDepartmentFromArray($row),
            'kindVacation' => $this->getKindVacationFromArray($row),
            'status' => $this->getStatusFromArray($row),
        ]);
    }

    /**
     * Извлечение даты начала
     * @param array $row
     * @return string
     */
    protected function getDateStartFromArray(array $row): string
    {
        return $row[0];
    }

    /**
     * Извлечение даты окончания
     * @param array $row
     * @return string
     */
    protected function getDateEndFromArray(array $row): string
    {
        return $row[1];
    }

    /**
     * Извлечение ФИО
     * @param array $row
     * @return string
     */
    protected function getFullNameFromArray(array $row): string
    {
        return $row[2];
    }

    /**
     * Извлечение наименования отдела
     * @param array $row
     * @return string
     */
    protected function getDepartmentFromArray(array $row): string
    {
        return $this->extractDepartment($row[3]);
    }

    /**
     * Извлечение имени отдела из строки содержащей организацию и отдел
     * @param string $organizationAndDepartment
     * @return string
     */
    private function extractDepartment(string $organizationAndDepartment): string
    {
        return explode('\\', $organizationAndDepartment)[1] ?? $organizationAndDepartment;
    }

    /**
     * Извлечение типа отпуска
     * @param array $row
     * @return string
     */
    protected function getKindVacationFromArray(array $row): string
    {
        return $row[4];
    }

    /**
     * Извлечение вида отпуска (создание сокращенного варианта)
     * @param string $value
     * @return string
     */
    private function extractKindVacation($value)
    {
        if (preg_match('/основн/', $value)) {
            return 'ежегодный основной отпуска';
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

    /**
     * Извлечение статуса
     * @param array $row
     * @return string
     */
    protected function getStatusFromArray(array $row): string
    {
        return $row[5];
    }

}