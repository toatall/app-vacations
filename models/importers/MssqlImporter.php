<?php
namespace app\models\importers;
use app\models\collections\AbstractVacationCollection;
use Yii;

class MssqlImporter extends AbstractImporter
{
    /**
     * Настройки подключения к SQL серверу
     * @var string
     */
    private $sqlConfig;    
    
    /**
     * @param array $sqlConfig 
     * @param AbstractVacationCollection $collection
     * @param string $codeOrganization
     * @param string $year
     */
    public function __construct(array $sqlConfig, AbstractVacationCollection $collection, string $codeOrganization, string $year)
    {        
        parent::__construct($collection, $codeOrganization, $year);
        $this->sqlConfig = $sqlConfig;
    }    

    /**
     * {@inheritDoc}
     */
    public function getSource(): string
    {
        return 'mssql';
    }

    /**
     * {@inheritDoc}
     */
    public function getSourceDescription(): string
    {
        return '';
    }

    /**
     * @return \Yii\db\Connection
     */
    private function getImportDb()
    {
        return Yii::createObject($this->sqlConfig);
    }

    /**
     * {@inheritDoc}
     */
    protected function loadData(): AbstractVacationCollection
    {                
        foreach($this->runQuery() as $item) {
            $this->collection->add($item);
        }
        return $this->collection;
    }

    /**
     * Запрос к кадровой БД с отпусками
     * @return array
     */
    private function runQuery()
    {
        $db = $this->getImportDb();
        
        $items = $db->createCommand(
<<<SQL
SELECT 
        [DICTION_SONO_KLASSIF_TBL].[CODE] [ORG_CODE]
        ,[FM].[FIO]
        ,[VAC].[NAME] AS [KIND_VACATION]
        ,[A].[DATE_BEGIN]
        ,[A].[DATE_END]
        ,[SUBDIV].[CODE] [DEPARTMENT_CODE]
        ,[SUBDIV].[NAME] [DEPARTMENT_NAME]
        ,[POST].[CODE] [POST_CODE]
        ,[POST].[NAME] [POST_NAME]
FROM [KADR_DOC_VACATION_GRAPHIC_LIST_TABLE] [A]
	INNER JOIN [KADR_DOC_VACATION_GRAPHIC_HEAD_TABLE] ON [KADR_DOC_VACATION_GRAPHIC_HEAD_TABLE].LINK = A.[LINK_UP]
	LEFT JOIN [DEPARTMENTS_TBL] ON [DEPARTMENTS_TBL].LINK = [KADR_DOC_VACATION_GRAPHIC_HEAD_TABLE].[LINK_DEP_OWN]
	LEFT JOIN [DICTION_SONO_KLASSIF_TBL] ON [DICTION_SONO_KLASSIF_TBL].[LINK] = [DEPARTMENTS_TBL].[SONO_LINK]

    INNER JOIN [EMPLOYERS_TBL] [E] ON [E].[LINK] = [A].[LINK_EMPL] AND ([E].[DATE_OUT] IS NULL OR [E].[DATE_OUT] > GETDATE())
    INNER JOIN [FACES_MAIN_TBL] [FM] ON [FM].[LINK] = [E].[FACE_LINK]
    LEFT JOIN [EMPLOYERS_SID_TBL] [EMPL] ON [EMPL].[LINK_EMPL] = [A].[LINK_EMPL]

    INNER JOIN [DICTIONARY_VACATION_TABLE] [VAC] ON [A].[VAC_LINK] = [VAC].[LINK]
    LEFT OUTER JOIN [DICTION_VIDOTP_KLASSIF_TBL] [VAC_KLS] ON [VAC].[LINK_EX] = [VAC_KLS].[LINK]

    LEFT JOIN [ITEM_MOVE] ON [ITEM_MOVE].[LINK_EMPL] = [A].[LINK_EMPL] AND [ITEM_MOVE].[DATE_END] > GETDATE()
    LEFT JOIN [STAFF] ON [STAFF].[LINK] = [ITEM_MOVE].[STAFF_LINK] AND [STAFF].[DATE_END] > GETDATE()
    LEFT JOIN [SUBDIV] ON [SUBDIV].[LINK_UP] = [STAFF].[SUBDIV_LINK] AND [SUBDIV].[DATE_END] > GETDATE()
    LEFT JOIN [POST] ON [POST].[LINK] = [STAFF].[POST_LINK] AND [POST].[DT_END] > GETDATE()

WHERE [DICTION_SONO_KLASSIF_TBL].[CODE] = :org_code AND [KADR_DOC_VACATION_GRAPHIC_HEAD_TABLE].[YEAR] = :year
    AND [E].[LINK_DEP_OWN] = [VAC].[LINK_DEP_OWN]
ORDER BY [SUBDIV].[CODE] ASC, [SUBDIV].[NAME] ASC, [POST].[CODE] ASC, [FM].[FIO] ASC
        
SQL, [
    ':org_code' => $this->codeOrganization,
    ':year' => $this->year,
])->queryAll(); 

        return $items;
    }

}