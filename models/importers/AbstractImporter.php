<?php
namespace app\models\importers;

use app\models\collections\AbstractVacationCollection;
use yii\db\Expression;

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
     * @var \app\models\VacationItem[]
     */
    protected $collection;

    /**
     * @var string
     */
    protected $codeOrganization;


    /**
     * Внедрение класса обработки коллекции
     * @param AbstractVacationCollection $collection
     */
    public function __construct(AbstractVacationCollection $collection, string $code)
    {
        $this->collection = $collection;
        $this->codeOrganization = $code;
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
     * @return \app\models\VacationItem[]
     * @return \app\models\collections\AbstractVacationCollection
     */
    abstract protected function getData(): AbstractVacationCollection;

    /**
     * Загрузка в базу данных     
     */
    public function load()
    {
        // получение данных, преобразование в коллекцию
        $data = $this->getData();
        
        // загрузка данных в буферные таблицы
        $this->insertToBufferTables($data);

        // очистка таблиц от неактуальных данных
        $this->clearDataInTables();
    }

    /**
     * Очистка таблиц от неактуальных данных
     * @return void
     */
    private function clearDataInTables()
    {
        $db = $this->getDb();
        
        // удаление неактуальных данных из таблицы vacations
        $db->createCommand(<<<SQL
            DELETE FROM {{vacations}}
            WHERE [[id]] IN (
                SELECT {{vacations}}.[[id]] FROM {{vacations}}
                    INNER JOIN {{employees}} ON {{employees}}.[[id]] = {{vacations}}.[[id_employee]]
                    INNER JOIN {{departments}} ON {{departments}}.[[id]] = {{employees}}.[[id_department]]
                WHERE {{vacations}}.[[update_hash]]<>:update_hash AND {{departments}}.[[org_code]]=:org_code
            )
        SQL, [
            ':update_hash' => $this->updateHash,
            ':org_code' => $this->codeOrganization,
        ])->execute();

        // удаление неактуальных данных из таблицы employees
        $db->createCommand(<<<SQL
            DELETE FROM {{employees}}
            WHERE [[id]] IN (
                SELECT {{employees}}.[[id]] FROM {{employees}}                
                    INNER JOIN {{departments}} ON {{departments}}.[[id]] = {{employees}}.[[id_department]]
                WHERE {{employees}}.[[update_hash]]<>:update_hash AND {{departments}}.[[org_code]]=:org_code
            )
        SQL, [
            ':update_hash' => $this->updateHash,
            ':org_code' => $this->codeOrganization,
        ])->execute();

        // удаление неактуальных данных из таблицы vacations_kind
        $db->createCommand(<<<SQL
            DELETE FROM {{vacations_kind}}                                
            WHERE [[update_hash]]<>:update_hash AND [[org_code]]=:org_code
        SQL, [
            ':update_hash' => $this->updateHash,
            ':org_code' => $this->codeOrganization,
        ])->execute();

        // удаление неактуальных данных из таблицы vacations_kind
        $db->createCommand(<<<SQL
            DELETE FROM {{departments}}                                
            WHERE [[update_hash]]<>:update_hash AND [[org_code]]=:org_code
        SQL, [
            ':update_hash' => $this->updateHash,
            ':org_code' => $this->codeOrganization,
        ])->execute();        
    }

    /**
     * Summary of insertToBufferTables
     * @param \app\models\VacationItem[]|AbstractVacationCollection $collection
     * @return void
     */
    protected function insertToBufferTables(AbstractVacationCollection $collection)
    {
        $db = $this->getDb();       

        foreach($collection as $item) {
            
            // добавление записи об отделе (departments)
            $db->createCommand()->upsert('departments', [                
                'org_code' => $this->codeOrganization,
                'name' => $item->department,
                'update_hash' => $this->updateHash,             
            ], [
                'update_hash' => $this->updateHash,
                'updated_at' => new Expression('NOW()'),
            ])->execute();
            
            $departmentId = $db->createCommand(
                'SELECT "id" FROM "departments" WHERE "org_code"=:org_code AND "name"=:name', 
                ['org_code' => $this->codeOrganization, ':name' => $item->department]
            )->queryOne()['id'] ?? null;
            if ($departmentId === null) {
                throw new \Exception('Не удалось получить id из таблицы departments');
            }


            // добавление записи о виде отпуска (vacations_kind)
            $db->createCommand()->upsert('vacations_kind', [
                'org_code' => $this->codeOrganization,
                'name' => $item->kindVacation,
                'update_hash' => $this->updateHash,   
            ], [
                'update_hash' => $this->updateHash,
                'updated_at' => new Expression('NOW()'),
            ])->execute();

            $vacationsKindId = $db->createCommand(
                'SELECT "id" FROM "vacations_kind" WHERE "name"=:name AND "org_code"=:org_code', 
                [':org_code' => $this->codeOrganization, ':name' => $item->kindVacation])
            ->queryOne()['id'] ?? null;
            if ($vacationsKindId === null) {
                throw new \Exception('Не удалось получить id из таблицы vacations_kind');
            }            
            

            // добавление записи о сотруднике (employee)
            $db->createCommand()->upsert('employees', [
                'id_department' => $departmentId,                    
                'full_name' => $item->fullName,   
                'update_hash' => $this->updateHash,
            ], [
                'update_hash' => $this->updateHash,
                'updated_at' => new Expression('NOW()'),
            ])->execute();
            
            $employeeId = $db->createCommand(
                'SELECT "id" FROM "employees" WHERE "id_department"=:id_department AND "full_name"=:full_name', 
                [':id_department' => $departmentId, ':full_name' => $item->fullName])
            ->queryOne()['id'] ?? null;
            if ($employeeId === null) {
                throw new \Exception('Не удалось получить id из таблицы employees');
            }    
            

            // добавление записи об отпуске (vacations)
            $db->createCommand()->upsert('vacations', [
                'id_kind' => $vacationsKindId, 
                'id_employee' => $employeeId, 
                'date_from' => $item->dateStartUTC, 
                'date_to' => $item->dateEndUTC,
                'status' => $item->status,
                'update_hash' => $this->updateHash,
            ], [
                'update_hash' => $this->updateHash,
                'updated_at' => new Expression('NOW()'),
            ])->execute();
            $vacationId = $db->createCommand(<<<SQL
                SELECT "id" FROM {{vacations}} 
                WHERE [[id_kind]]=:id_kind AND [[id_employee]]=:id_employee 
                    AND [[date_from]]=TO_DATE(:date_from, 'YYYY-MM-DD') AND [[date_to]]=TO_DATE(:date_to, 'YYYY-MM-DD')
                    AND [[status]]=:status
                SQL, 
                [
                    ':id_kind' => $vacationsKindId, 
                    ':id_employee' => $employeeId,
                    ':date_from' => $item->dateStartUTC,
                    ':date_to' => $item->dateEndUTC,
                    ':status' => $item->status,
                ])
            ->queryOne()['id'] ?? null;
            if (!$vacationId) {
                throw new \Exception('Не удалось вставить запись в таблицу vacations');
            }
            
        }
    }

    /**
     * Генерирование хеша по дате-времени
     * @return string
     */
    private function generateUpdateHash()
    {        
        return md5(time());
    }   


}