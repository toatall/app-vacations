<?php
namespace app\models\importers;
use app\models\collections\AbstractVacationCollection;

class CsvFileImporter extends AbstractImporter
{
    private $filename;
    
    public function __construct(string $filename, AbstractVacationCollection $collection, string $codeOrganization)
    {        
        parent::__construct($collection, $codeOrganization);
        $this->filename = $filename;
    }    

    protected function getData(): AbstractVacationCollection
    {        
        if (($handle = fopen($this->filename, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, null, ",")) !== FALSE) {
                if (count($data) !== 6) {
                    throw new \Exception('Количество полей должно быть равно 6');
                }
                $this->collection->add($data);
            }
            fclose($handle);
        }
        return $this->collection;
    }

}