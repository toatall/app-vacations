<?php
namespace app\models\importers;
use app\models\collections\AbstractVacationCollection;

class CsvFileImporter extends AbstractImporter
{
    /**
     * Путь к csv-файлу  
     * @var string
     */
    private $filename;

    /**
     * Разделитель строк в csv-файле
     * по-умолчанию ","
     * @var string
     */
    private $csvSeparator;

    /**
     * @param string $filename имя csv-файла
     * @param \app\models\collections\AbstractVacationCollection $collection коллекция объектов подлежащих загрузке
     * @param string $codeOrganization код организации
     * @param string $year год
     * @param mixed $csvSeparator разделитель строк в csv-файле
     */
    public function __construct(string $filename, AbstractVacationCollection $collection, string $codeOrganization, string $year, $csvSeparator = ',')
    {
        parent::__construct($collection, $codeOrganization, $year);
        $this->filename = $filename;
        $this->csvSeparator = $csvSeparator;
    }

    /**
     * {@inheritDoc}
     */
    public function getSource(): string
    {
        return 'csv';
    }

    /**
     * {@inheritDoc}
     */
    public function getSourceDescription(): string
    {
        return $this->filename;
    }

    /**
     * {@inheritDoc}
     */
    protected function loadData(): AbstractVacationCollection
    {
        if (($handle = fopen($this->filename, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, null, $this->csvSeparator)) !== FALSE) {
                if (count($data) !== 7) {
                    throw new \Exception('Количество полей должно быть равно 7');
                }
                $this->collection->add($data);
            }
            fclose($handle);
        }
        return $this->collection;
    }

}