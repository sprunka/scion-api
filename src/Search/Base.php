<?php

namespace ScionAPI\Search;

use ScionAPI\AbstractRoute;
use ScionAPI\Generic\ListFactory;
use ScionAPI\Generic\Record;
use ScionAPI\Generic\RecordFactory;
use ScionAPI\Generic\RecordList;

abstract class Base extends AbstractRoute
{
    protected $matchedDeities = [];
    /**
     * @var RecordList
     */
    protected $pantheonList;
    /**
     * @var Record
     */
    protected $pantheon;
    /**
     * @var Record
     */
    protected $deity;

    public function __construct(ListFactory $listFactory, RecordFactory $recordFactory)
    {
        $fullList = $listFactory::create();
        $fullList->loadFile(__DIR__ . '/../../json_src/all_gods.json');
        $this->pantheonList = $fullList;
        $this->matchedDeities = [];
        $this->pantheon = $recordFactory::create();
        $this->deity = $recordFactory::create();
    }
}
