<?php


namespace ScionAPI\Collections;


use ScionAPI\Generic\RecordList;

class AllGods extends RecordList
{
    protected $pantheons = [];

    public function __construct($data = false, $autoSave = false, $createFile = false)
    {
        $data = __DIR__ . '../../json_src/all_gods.json';
        $autoSave = 'file';
        parent::__construct($data, $autoSave, $createFile);
    }

}
