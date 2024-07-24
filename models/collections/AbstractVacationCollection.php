<?php
namespace app\models\collections;
use app\models\VacationItem;

/**
 * Коллекция элементов app\models\VacationItem
 * Возможно только добавление
 */
abstract class AbstractVacationCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var VacationItem[]
     */
    protected $collection = [];

    /**
     * {@inheritDoc}
     * @return \Traversable
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->collection);
    }

    /**
     * {@inheritDoc}
     * @return int
     */
    public function count(): int
    {        
        return count($this->collection);
    }

    /**
     * Добавить элемент в коллекцию
     * @param mixed $row
     * @return void
     */
    public function add($row)
    {        
        $this->collection[] = $this->prepare($row);
    }

    /**
     * Извлечение данных для записи в модель VacationItem
     * перед добавлением в коллекцию
     * @param mixed $row
     * @return \app\models\VacationItem
     */
    abstract protected function prepare($row): VacationItem;
    
}