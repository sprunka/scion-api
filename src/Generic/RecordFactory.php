<?php

namespace ScionAPI\Generic;

use stdClass;

class RecordFactory
{
    /**
     * Record constructor.
     * @param bool|string|array|stdClass $json
     * @return Record
     */
    public static function create($json = false): Record
    {
        return new Record($json);
    }

}
