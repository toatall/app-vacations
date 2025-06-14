<?php
namespace app\models\importers;

use app\models\collections\AbstractVacationCollection;
use yii\db\Expression;
use yii\db\Query;

/**
 * Импорт данных в базу данных
 */
abstract class AbstractImporter
{
    /**
     * Хеш, который будет подставлен в таблицы в обновленные данные
     * @var string
     */
    private $updateHash;

    /**
     * Коллекция элементов VacationItem
     * @var AbstractVacationCollection|\app\models\VacationItem[]
     */
    protected $collection;

    /**
     * Код организации
     * @var string
     */
    protected $codeOrganization;

    /**
     * Отчетный год
     * @var string
     */
    protected $year;

    /**
     * Ошибки
     * @var string
     */
    protected $error;

    /**
     * Подробности ошибки
     * @var string
     */
    protected $errorTrace;

    /**
     * @var \Throwable
     */
    protected $throwable;


    /**
     * Внедрение класса обработки коллекции
     * @param AbstractVacationCollection $collection
     */
    public function __construct(AbstractVacationCollection $collection, string $code, string|null $year)
    {
        $this->collection = $collection;
        $this->codeOrganization = $code;
        $this->year = $year;
        $this->updateHash = $this->generateUpdateHash();
    }

    /**
     * @return \yii\db\Connection
     */
    protected function getDb()
    {
        return \Yii::$app->db;
    }

    /**
     * Обработка (получение) данных из определенного источника
     * Возвращаться должна наполненная коллекция VacationItem
     * @return \app\models\collections\AbstractVacationCollection|\app\models\VacationItem[]
     */
    abstract protected function loadData(): AbstractVacationCollection;

    /**
     * @throws \app\models\importers\LoadDataException
     * @return \app\models\collections\AbstractVacationCollection
     */
    protected function getLoadData(): AbstractVacationCollection
    {
        try {
            return $this->loadData();
        }
        catch (\Exception $ex) {
            throw new LoadDataException($ex->getMessage(), 0, $ex);
        }
    }

    /**
     * Наименование источника загрузки информации
     * @return string
     */
    abstract protected function getSource(): string;

    /**
     * Дополнительная информация источника загрузки информации
     * @return string
     */
    abstract protected function getSourceDescription(): string;


    /**
     * Загрузка в базу данных     
     */
    public function run()
    {
        // получение данных, преобразование в коллекцию
        try {            
            $data = $this->getLoadData();
            $this->saveDataToDb($data);
            $this->mergeVacations();
            $this->purgeData();
        }
        catch (LoadDataException $ex) {
            $this->error .= "Ошибка при получении данных!\n" . $ex->getMessage();
            $this->errorTrace .= $ex->getTraceAsString() . "\n";
            $this->throwable = $ex;
        }
        catch (SaveDataToDbException $ex) {
            $this->error .= "Ошибка в БД. Загрузка данных!\n" . $ex->getMessage();
            $this->errorTrace .= $ex->getTraceAsString() . "\n";
            $this->throwable = $ex;
        }
        catch (PurgeDataException $ex) {
            $this->error .= "Ошибка в БД. Очистка от неактуальных данных!\n" . $ex->getMessage();
            $this->errorTrace .= $ex->getTraceAsString() . "\n";
            $this->throwable = $ex;
        }
        catch (\Exception $ex) {
            $this->error .= "Неизвестная ошибка!\n" . $ex->getMessage();
            $this->errorTrace .= $ex->getTraceAsString() . "\n";
            $this->throwable = $ex;
        }
        finally {
            // сохранение истории загрузки
            $this->saveHistory();
        }

        if ($this->throwable) {
            throw $this->throwable;
        }
    }

    /**
     * Очистка таблиц от неактуальных данных
     * @return void
     */
    private function purgeData()
    {
        try {
            $db = $this->getDb();
            
            // удаление неактуальных данных из таблицы vacations_kind
            $db->createCommand(<<<SQL
                DELETE FROM {{vacations_kind}}
                WHERE [[id]] IN (
                    SELECT DISTINCT {{vacations_kind}}.[[id]] FROM {{vacations_kind}} 
                        LEFT JOIN {{vacations}} ON {{vacations}}.[[id_kind]] = {{vacations_kind}}.[[id]]
                    WHERE {{vacations_kind}}.[[org_code]] = :org_code AND {{vacations}}.[[id]] IS NULL
                )        
            SQL, [
                ':org_code' => $this->codeOrganization,
            ])->execute();


            // удаление неактуальных данных из таблиц departments
            $db->createCommand(<<<SQL
                DELETE FROM {{departments}}
                WHERE {{departments}}.[[year]] = :year AND {{departments}}.[[org_code]]=:org_code
                    AND {{departments}}.[[update_hash]]<>:update_hash                
            SQL, [
                ':update_hash' => $this->updateHash,
                ':org_code' => $this->codeOrganization,
                ':year' => $this->year,
            ])->execute();

            // удаление неактуальных данных из таблиц employees
            $db->createCommand(<<<SQL
                DELETE FROM {{employees}}
                WHERE [[id]] IN (
                    SELECT {{employees}}.[[id]] FROM {{employees}}
                        RIGHT JOIN {{departments}} ON {{departments}}.[[id]] = {{employees}}.[[id_department]]                       
                    WHERE {{employees}}.[[update_hash]]<>:update_hash AND {{departments}}.[[org_code]]=:org_code
                        AND {{departments}}.[[year]] = :year
                )
            SQL, [
                ':update_hash' => $this->updateHash,
                ':org_code' => $this->codeOrganization,
                ':year' => $this->year,
            ])->execute();

            // удаление неактуальных данных из таблиц vacations
            $db->createCommand(<<<SQL
                DELETE FROM {{vacations}}
                WHERE [[id]] IN (
                    SELECT {{vacations}}.[[id]] FROM {{vacations}}
                        RIGHT JOIN {{employees}} ON {{employees}}.[[id]] = {{vacations}}.[[id_employee]]
                        RIGHT JOIN {{departments}} ON {{departments}}.[[id]] = {{employees}}.[[id_department]]                       
                    WHERE {{vacations}}.[[update_hash]]<>:update_hash AND {{departments}}.[[org_code]]=:org_code
                        AND {{departments}}.[[year]] = :year
                )
            SQL, [
                ':update_hash' => $this->updateHash,
                ':org_code' => $this->codeOrganization,
                ':year' => $this->year,
            ])->execute();


            // удаление неактуальных данных из таблицы vacations_merged
            $db->createCommand(<<<SQL
                DELETE FROM {{vacations_merged}}
                WHERE [[id]] IN (
                    SELECT {{vacations_merged}}.[[id]] FROM {{vacations_merged}}
                        RIGHT JOIN {{employees}} ON {{employees}}.[[id]] = {{vacations_merged}}.[[id_employee]]  
                        RIGHT JOIN {{departments}} ON {{departments}}.[[id]] = {{employees}}.[[id_department]]
                            AND {{vacations_merged}}.[[year]] = :year
                    WHERE {{departments}}.[[org_code]] = :org_code AND {{departments}}.[[year]] = :year 
                        AND {{vacations_merged}}.[[update_hash]] <> :update_hash                
                )
            SQL, [                
                ':org_code' => $this->codeOrganization,
                ':year' => $this->year,
                ':update_hash' => $this->updateHash,
            ])->execute();
        }
        catch (\Exception $ex) {
            throw new PurgeDataException($ex->getMessage(), 0, $ex);
        }
    }

    /**
     * Загрузка данных в таблицы
     * @param \app\models\VacationItem[]|AbstractVacationCollection $collection
     * @return void
     */
    protected function saveDataToDb(AbstractVacationCollection $collection)
    {
        try {
            $db = $this->getDb();       

            foreach($collection as $item) {
                
                // добавление записи об отделе (departments)           
                $this->upsert('departments', [
                    'org_code' => $this->codeOrganization,
                    'year' => $this->year,
                    'name' => $item->department,
                    'sort_index' => $item->departmentSortIndex,
                    'update_hash' => $this->updateHash, 
                ], [
                    'update_hash' => $this->updateHash,
                    'updated_at' => new Expression('NOW()'),
                ]);
                
                $departmentId = $db->createCommand(
                    'SELECT "id" FROM "departments" WHERE "org_code"=:org_code AND "name"=:name AND "year"=:year', 
                    ['org_code' => $this->codeOrganization, ':name' => $item->department, ':year' => $this->year]
                )->queryOne()['id'] ?? null;
                
                if ($departmentId === null) {
                    throw new \Exception('Не удалось получить id из таблицы departments');
                }


                // добавление записи о виде отпуска (vacations_kind)            
                $this->upsert('vacations_kind', [
                    'org_code' => $this->codeOrganization,                
                    'name' => $item->kindVacation,                
                ], [
                    'updated_at' => new Expression('NOW()'),
                ]);

                $vacationsKindId = $db->createCommand(
                    'SELECT [[id]] FROM {{vacations_kind}} WHERE [[name]]=:name AND [[org_code]]=:org_code', 
                    [':org_code' => $this->codeOrganization, ':name' => $item->kindVacation])
                ->queryOne()['id'] ?? null;
                if ($vacationsKindId === null) {
                    throw new \Exception('Не удалось получить id из таблицы vacations_kind');
                }

                // добавление записи о сотруднике (employee)           
                $this->upsert('employees', [
                    'id_department' => $departmentId,                    
                    'full_name' => $item->fullName,   
                    'post' => $item->post,
                    'sort_index' => $item->employeeSortIndex,
                    'update_hash' => $this->updateHash,
                ], [
                    'update_hash' => $this->updateHash,
                    'updated_at' => new Expression('NOW()'),
                ]);
                
                $employeeId = $db->createCommand(
                    'SELECT "id" FROM "employees" WHERE "id_department"=:id_department AND "full_name"=:full_name', 
                    [':id_department' => $departmentId, ':full_name' => $item->fullName])
                ->queryOne()['id'] ?? null;
                if ($employeeId === null) {
                    throw new \Exception('Не удалось получить id из таблицы employees');
                }

                // добавление записи об отпуске (vacations)           
                $this->upsert('vacations', [
                    'id_kind' => $vacationsKindId, 
                    'id_employee' => $employeeId, 
                    'date_from' => $item->dateStartUTC, 
                    'date_to' => $item->dateEndUTC,
                    'status' => $item->status,
                    'update_hash' => $this->updateHash,
                ], [
                    'update_hash' => $this->updateHash,
                    'updated_at' => new Expression('NOW()'),
                ]);
                $vacationId = $db->createCommand(<<<SQL
                    SELECT "id" FROM {{vacations}} 
                    WHERE [[id_kind]]=:id_kind AND [[id_employee]]=:id_employee 
                        AND [[date_from]]=TO_DATE(:date_from, 'YYYY-MM-DD') AND [[date_to]]=TO_DATE(:date_to, 'YYYY-MM-DD')
                    SQL, 
                    [
                        ':id_kind' => $vacationsKindId, 
                        ':id_employee' => $employeeId,
                        ':date_from' => $item->dateStartUTC,
                        ':date_to' => $item->dateEndUTC,
                    ])
                ->queryOne()['id'] ?? null;
                if (!$vacationId) {                    
                    throw new \Exception('Не удалось вставить запись в таблицу vacations');
                }                
            }
        }
        catch (\Exception $ex) {
            throw new SaveDataToDbException($ex->getMessage(), 0, $ex);
        }
    }

    /**
     * Объединение отпусков (следующих друг за другом)
     * @return void
     */
    protected function mergeVacations()
    {
        try {
            $db = $this->getDb();
            $db->createCommand(<<<SQL
                CALL public.merge_vacations(:org_code, :year, :update_hash)              
            SQL, [
                ':org_code' => $this->codeOrganization,
                ':year' => $this->year,
                ':update_hash' => $this->updateHash,
            ])->execute();
        }
        catch (\Exception $ex) {
            throw new SaveDataToDbException($ex->getMessage(), 0, $ex);
        }
    }

    /**
     * Вставка или обновление записи
     * @param string $table таблица
     * @param array $insert данные для вставки
     * @param array $update данные для обновления
     * @return void
     */
    protected function upsert(string $table, array $insert, array $update = [])
    {
        $db = $this->getDb();
        $indexColumns = $this->getIndexesByTable($table);
        $where = array_intersect_key($insert, array_flip($indexColumns));
        
        $query = (new Query())
            ->from($table)
            ->where($where)
            ->exists($db);
        
        if (!$query) {
            $db->createCommand()
                ->insert($table, $insert)
                ->execute();
        }        
        elseif(!empty($update)) {
            $db->createCommand()
                ->update($table, $update, $where)
                ->execute();
        }
    }

    /**
     * Получение индексов (без первичного ключа) таблицы 
     * @param string $table наименование таблицы
     * @return array массив индексов (без первичного ключа)
     */
    protected function getIndexesByTable(string $table)
    {
        $items = $this->getDb()->createCommand(<<<SQL
            SELECT   
                "a"."attname" AS "column_name"	
            FROM
                "pg_class" AS "t",
                "pg_class" AS "i",
                "pg_index" AS "ix",
                "pg_attribute" AS "a"
            WHERE
                "t"."oid" = "ix"."indrelid"
                AND "i"."oid" = "ix"."indexrelid"
                AND "a"."attrelid" = "t"."oid"
                AND "a"."attnum" = ANY("ix"."indkey")
                AND "t"."relkind" = 'r'
                AND "t"."relname" LIKE :table
                AND "ix"."indisprimary" = FALSE
            ORDER BY
                "t"."relname",
                "i"."relname";
        SQL, [':table' => "$table"])->queryAll();        
        return array_map(fn($item) => $item['column_name'], $items);
    }


    /**
     * Генерирование хеша по дате-времени
     * @return string
     */
    private function generateUpdateHash()
    {        
        return md5(time());
    }   

    /**
     * Сохранение лога о загрузке
     * @return void
     */
    protected function saveHistory()
    {
        $this->getDb()->createCommand()
            ->insert('load_history', [
                'org_code' => $this->codeOrganization,
                'year' => $this->year,
                'source' => $this->getSource(),
                'source_description' => $this->getSourceDescription(),
                'error' => $this->error,
                'error_trace' => $this->errorTrace,
            ])
            ->execute();
    }


}