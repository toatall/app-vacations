<?php
namespace app\models;
use yii\base\BaseObject;

/**
 * Элемент коллекции
 * 
 * @property string $dateStartUTC
 * @property string $dateEndUTC
 */
class VacationItem extends BaseObject
{
    /**
     * Дата начала
     * @var string
     */
    public $dateStart;

    /**
     * Дата окончания
     * @var string
     */
    public $dateEnd;

    /**
     * ФИО сотрудника
     * @var string
     */
    public $fullName;

    /**
     * Должность
     * @var string
     */
    public $post;

    /**
     * Код организации
     * @var string
     */
    public $organization;

    /**
     * Наименование отдела
     * @var string
     */
    public $department;

    /**
     * Вид отпуска
     * @var string
     */
    public $kindVacation;

    /**
     * Сортировка сотрудника
     * @var string
     */
    public $employeeSortIndex;

    /**
     * Сортировка отдела
     * @var string
     */
    public $departmentSortIndex;

    /**
     * Статус
     * @var string
     */
    public $status;

    public function getDateStartUTC()
    {
        return $this->dateToUTC($this->dateStart);
    }

    public function getDateEndUTC()
    {
        return $this->dateToUTC($this->dateEnd);
    }

    private function dateToUTC($value)
    {
        return \Yii::$app->formatter->asDate($value, 'yyyy-MM-dd');
    }

    public function toArray()
    {
        return [$this->dateStart, $this->dateEnd, $this->fullName, $this->department, $this->post, $this->kindVacation, $this->status];
    }

}