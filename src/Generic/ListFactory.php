<?php

namespace ScionAPI\Generic;

use stdClass;

class ListFactory
{
    /**
     * Creates a new RecordList or a child class of RecordList with the given data.
     *
     * @param bool|array|string|stdClass $data The data to create the RecordList from.
     * @param bool $autoSave Whether the created RecordList should automatically save changes.
     * @param bool $createFile Whether to create a new file if the given file does not exist.
     * @param string $listType The name of the class to create. Must be a child of RecordList.
     * @return RecordList The created RecordList object.
     * @throws \InvalidArgumentException If $listType is not a child of RecordList or does not exist.
     */
    public static function create($data = false, $autoSave = false, $createFile = false): RecordList
    {
        return new RecordList($data, $autoSave, $createFile);
    }
}
