<?php

namespace tests\unit\models;

use app\models\collections\FileVacationCollection;

class FileVacationCollectionTest extends \Codeception\Test\Unit
{

    private function getPrivateProperty($object, $property)
    {
        $reflectedClass = new \ReflectionClass($object);
        $reflection = $reflectedClass->getProperty($property);
        $reflection->setAccessible(true);
        return $reflection->getValue($object);
    }

    public function testAddToCollection()
    {
        $simpleDataCheck = [
            ['01.01.2023', '01.02.2023', 'Петров Иван Сергеевич', 'Организация\19 Отдел', 'Начальник', 'ежегодного основного отпуска', 'E'],
            ['02.02.2023', '05.02.2023', 'Петров Иван Сергеевич', 'Организация\19 Отдел', 'Начальник', 'ежегодного дополнительного отпуска за ненормированный рабочий день', 'E'],
            ['16.12.2024', '25.12.2024', 'Иванова Мария Александровна', 'Организация\18 Отдел', 'Специалист', 'ежегодного дополнительного отпуска за выслугу лет', 'E'],
            ['26.12.2024', '31.12.2024', 'Иванова Мария Александровна', 'Организация\18 Отдел', 'Специалист', 'ежегодного дополнительного оплачиваемого отпуска за работу в местности, приравненной к районам Крайнего Севера', 'E'],
        ];
        $simpleDataActual = [
            ['01.01.2023', '01.02.2023', 'Петров Иван Сергеевич', '19 Отдел', 'Начальник', 'ежегодный основной отпуск', 'E'],
            ['02.02.2023', '05.02.2023', 'Петров Иван Сергеевич', '19 Отдел', 'Начальник', 'ежегодный дополнительный отпуск за ненормированный рабочий день', 'E'],
            ['16.12.2024', '25.12.2024', 'Иванова Мария Александровна', '18 Отдел', 'Специалист', 'ежегодный дополнительный отпуск за выслугу лет', 'E'],
            ['26.12.2024', '31.12.2024', 'Иванова Мария Александровна', '18 Отдел', 'Специалист', 'ежегодный дополнительный отпуск за работу в местности, приравненной к районам Крайнего Севера', 'E'],
        ];

        $simpleVacationCollection = new FileVacationCollection('8600');
        $simpleVacationCollection->add($simpleDataCheck[0]);
        $simpleVacationCollection->add($simpleDataCheck[1]);
        $simpleVacationCollection->add($simpleDataCheck[2]);
        $simpleVacationCollection->add($simpleDataCheck[3]);

        $items = $this->getPrivateProperty($simpleVacationCollection, 'collection');
        $this->assertCount(count($simpleDataCheck), $items);

        $this->assertEquals($items[0]->toArray(), $simpleDataActual[0]);
        $this->assertEquals($items[1]->toArray(), $simpleDataActual[1]);
        $this->assertEquals($items[2]->toArray(), $simpleDataActual[2]);
        $this->assertEquals($items[3]->toArray(), $simpleDataActual[3]);
    }

    public function testNotExistsKindVacation()
    {
        $notExistsKind = 'не существующий тип отпуска';
        $simpleVacationCollection = new FileVacationCollection('8600');
        $simpleVacationCollection->add(['01.01.2023', '01.02.2023', 'Петров Иван Сергеевич', 'Организация\19 Отдел', 'Начальник', $notExistsKind, 'E']);

        $items = $this->getPrivateProperty($simpleVacationCollection, 'collection');
        $this->assertEquals($notExistsKind, $items[0]->kindVacation);
    }

    public function testCheckIteratorAggregate()
    {
        $simpleVacationCollection = new FileVacationCollection('8600');
        $this->assertInstanceOf(\ArrayIterator::class, $simpleVacationCollection->getIterator());
    }

    public function testCheckCountable()
    {
        $simpleVacationCollection = new FileVacationCollection('8600');
        $this->assertIsInt($simpleVacationCollection->count());
        $this->assertEquals(0, $simpleVacationCollection->count());

        $row = ['01.01.2023', '01.02.2023', 'Петров Иван Сергеевич', 'Организация\19 Отдел', 'Начальник', 'ежегодного основного отпуска', 'E'];

        $simpleVacationCollection->add($row);
        $this->assertEquals(1, $simpleVacationCollection->count());

        for ($i = 0; $i < 100; $i++) {
            $simpleVacationCollection->add($row);
        }
        $this->assertEquals(101, $simpleVacationCollection->count());
    }

}