<?php
namespace app\commands;

use DateInterval;
use Faker\Factory;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Загрузка в БД данных об отпусках, сотрудниках, отделах
 * 
 * yii vacations/generate X Y file.csv
 *   X - количество сотрудников
 *   Y - максимальное количество периодов отпусков у сотрудника (не рекомендуется указывать большое число) 
 *   file.csv - файл для сгенерированных данных 
 */
class VacationsController extends Controller
{

    /** @var \Faker\Generator */
    private $faker;

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();
        $this->faker = Factory::create('ru_RU');
    }

    /**
     * Генерирование отпусков в csv файл
     * @param int $countUsers
     * @param int $maxPeriodsByUser
     * @return void
     */
    public function actionGenerate(int $countUsers = 50, int $maxPeriodsByUser = 8, ?string $csvFile = null)
    {
        $this->stdout("Генерация отпусков: количество пользователей - {$countUsers}, максимальное количество периодов - {$maxPeriodsByUser}, файл - {$csvFile}..." . PHP_EOL);
        $vacations = [];
        for ($iUser = 0; $iUser < $countUsers; $iUser++) {
            $employeeName = $this->generateFullName();
            $employeeDepartment = $this->generateDepartment();
            $employeePost = $this->generatePost();
            $employeePeriods = $this->generatePeriods($maxPeriodsByUser);

            foreach ($employeePeriods as $period) {
                $vacations[] = implode(';', [
                    $period['from']->format('Y-m-d'),
                    $period['to']->format('Y-m-d'),
                    $employeeName,
                    $employeeDepartment,
                    $employeePost,
                    $period['type'],
                    '',
                ]);
            }
        }
        if (!$csvFile) {
            $csvFile = \Yii::getAlias('@runtime') . '/vacations.csv';
        }
        file_put_contents(filename: $csvFile, data: implode("\n", $vacations));
        $this->stdout("Генерация завершена" . PHP_EOL, Console::FG_GREEN);
    }

    /**
     * Генерирование периодов отпусков
     * @param int $maxPeriodsByUser
     * @return array
     */
    private function generatePeriods(int $maxPeriodsByUser): array
    {
        $periodsDates = [];

        $periods = rand(1, $maxPeriodsByUser);
        for ($iPeriod = 1; $iPeriod <= $periods; $iPeriod++) {

            while (true) {
                $res = false;
                $d = new \DateTimeImmutable($this->faker->dateTimeThisYear(date('Y-12-31'))->format('Y-m-d'));
                foreach ($periodsDates as $periodDate) {
                    if ($d >= $periodDate['from'] && $d <= $periodDate['to']) {
                        $res = true;
                    }
                }
                if (!$res) {
                    break;
                }
            }

            $dockingDates = rand(0, 4) === 0;
            if (count($periodsDates) && $dockingDates) {
                $d = $periodsDates[count($periodsDates) - 1]['to']->add(new DateInterval('P1D'));
            }
            $days = rand(7, 25);
            $periodsDates[] = [
                'from' => $d,
                'to' => $d->add(new DateInterval("P{$days}D")),
                'type' => $this->generateTypeVacation(),
            ];
        }

        return $periodsDates;
    }

    /**
     * Генерирование ФИО сотрудника
     * @return string
     */
    private function generateFullName()
    {
        return "{$this->faker->lastName()} {$this->faker->firstName()} {$this->faker->middleName()}";
    }

    /**
     * Генерирование отдела сотрудника
     * @return string
     */
    private function generateDepartment(): string
    {
        return $this->randElementFromArray([
            'Руководство' => 2,
            'Бухгалтерия' => 5,
            'Отдел информационных технологий' => 5,
            'Отдел кадров' => 5,
            'Юридический отдел' => 5,
            'Отдел контроля' => 5,
        ]);
    }

    /**
     * Генерирование должности сотрудника
     * @return string
     */
    private function generatePost(): string
    {
        return $this->randElementFromArray([
            'начальник' => 2,
            'заместитель начальника' => 3,
            'стажер' => 4,
            'специалист' => 10,
            'старший специалист' => 10,
        ]);
    }

    /**
     * Генерирование вида отпуска
     * @return string
     */
    private function generateTypeVacation(): string
    {
        return $this->randElementFromArray([
            'Дополнительный отпуск за работу в районах Крайнего Севера' => 4,
            'Основной отпуск' => 6,
            'Дополнительный отпуск' => 10
        ]);
    }

    /**
     * Случайный элемент массива с использованием весов
     * @param array $arr
     * @return mixed
     */
    private function randElementFromArray(array $arr): mixed
    {
        $sumWeight = array_sum($arr);
        $rand = rand(1, $sumWeight);
        $acc = 0;

        foreach ($arr as $value => $weight) {
            $acc += $weight;

            if ($acc >= $rand) {
                return $value;
            }
        }
        return null;
    }

}