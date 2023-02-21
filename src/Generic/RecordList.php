<?php

namespace ScionAPI\Generic;

use ArrayAccess;
use Countable;
use Iterator;
use ReturnTypeWillChange;
use RuntimeException;
use stdClass;

/**
 * Class RecordList
 * This is a collection type object to hold sets of Records. It can behave like an Object or an Array at your preference.
 * @package ScionAPI\Generic\
 */
class RecordList implements Countable, ArrayAccess, Iterator
{
    protected $data = [];
    protected $autoSave = false;
    protected $saveFile;

    /**
     * RecordList constructor.
     * @param bool|array|string|stdClass $data
     * @param bool $autoSave
     * @param bool $createFile
     * @throws RuntimeException
     */
    public function __construct(bool|array|string|stdClass $data = false, bool $autoSave = false, bool $createFile = false)
    {
        if ($data !== false) {
            $this->loadData($data, $autoSave, $createFile);
        }
    }

    public function loadData($data, $autoSave = false, $createFile = false)
    {
        if (is_string($data) && (file_exists($data) || $createFile === true)) {
            $this->saveFile = $data;
            if (!file_exists($data)) {
                file_put_contents($data, '{}', LOCK_EX);
            }
            $data = json_decode(file_get_contents($data), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new RuntimeException('Error decoding File: JSON ERROR: ' . json_last_error_msg());
            }
            if ($autoSave !== false) {
                $this->autoSave = 'file';
            }
        }
        if (is_string($data)) {
            $data = json_decode($data, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new RuntimeException('Error decoding string: JSON ERROR: ' . json_last_error_msg());
            }
            if ($autoSave !== false && $this->autoSave === false) {
                $this->autoSave = 'string';
            }
        }
        if (is_array($data) || $data instanceof stdClass) {
            if ($autoSave !== false && $this->autoSave === false) {
                $this->autoSave = 'array';
                if ($data instanceof stdClass) {
                    $this->autoSave = 'stdClass';
                }
            }
            foreach ($data as $key => $value) {
                $record = new Record($value);
                $this->addRecord($record, $key, true);
            }
        }
    }

    /**
     * @param Record $record the Record to add to the List
     * @param bool $key Optional array key to store Record at.
     * @param bool $force If array key already exists, overwrite with new Record?
     * @return bool|mixed
     */
    public function addRecord(Record $record, $key = false, $force = false)
    {
        if ($key === false && !$this->recordExists($record)) {
            $this->data[] = $record;
            if ($this->autoSave !== false) {
                return $this->save();
            }
            return true;
        }

        if ($force === true || array_key_exists($key, $this->data) === false) {
            $this->data[$key] = $record;
            if ($this->autoSave !== false) {
                return $this->save();
            }
            return true;
        }

        return false;
    }

    /**
     * @param Record $record
     * @return bool
     */
    public function recordExists(Record $record): bool
    {
        return in_array($record, $this->data, true);
    }

    /**
     * @param null|string $type
     * @param null|string $saveFile
     * @return false|int|mixed|string
     * @throws RuntimeException
     */
    public function save($type = null, $saveFile = null)
    {
        if (!$type) {
            $type = $this->autoSave;
        }
        if (!$saveFile) {
            $saveFile = $this->saveFile;
        }

        $out = json_encode($this->data, JSON_PRETTY_PRINT);

        switch ($type) {
            case 'string':
                return $out;
                break;
            case 'array':
                return json_decode($out, true);
                break;
            case 'stdClass':
                return json_decode($out, false);
                break;
            case 'file':
                if (!is_string($saveFile)) {
                    throw new RuntimeException('No save file specified.');
                }
                return file_put_contents($saveFile, $out, LOCK_EX);
                break;
            default:
                throw new RuntimeException('Save Type not specified.');
                break;
        }
    }

    public function loadFile($filePath, $autosave = true)
    {
        $this->loadData($filePath, $autosave);
    }

    /**
     * @param string|int $key The array key to look up
     * @return mixed
     */
    public function getRecordByKey($key)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        return null;
    }

    /**
     * Look up a Record by one of it's properties
     * NOTE: This will return the first (and only the first) Record with said property/value combo.
     * @param string $property The Record property to look up.
     * @param string $value The Value of the Property we're looking for
     * @return Record|null
     */
    public function getRecordByProperty($property, $value)
    {
        foreach ($this->data as $record) {
            if (property_exists($record, $property) && $record->{$property} === $value) {
                return $record;
            }
        }
        return null;
    }

    /**
     * @param Record $record
     * @return bool|mixed
     */
    public function deleteRecord(Record $record)
    {
        if ($this->recordExists($record)) {
            $key = $this->getRecordKey($record);
            return $this->deleteRecordByKey($key);
        }
        return false;
    }

    /**
     * @param Record $record
     * @return false|int|string
     */
    public function getRecordKey(Record $record)
    {
        return array_search($record, $this->data, true);
    }

    /**
     * @param string|int $key
     * @return bool|mixed
     */
    public function deleteRecordByKey($key)
    {
        if ($this->offsetExists($key)) {
            unset($this->data[$key]);
            if ($this->autoSave !== false) {
                return $this->save();
            }
            return true;
        }
        return false;
    }

    /**
     * @return bool|string
     */
    public function getAutoSave()
    {
        return $this->autoSave;
    }

    /**
     * @param bool|string $autoSave
     */
    public function setAutoSave($autoSave)
    {
        $this->autoSave = $autoSave;
    }

    /**
     * @return bool|string
     */
    public function getSaveFile()
    {
        return $this->saveFile;
    }

    /**
     * @param string $saveFile
     */
    public function setSaveFile($saveFile)
    {
        $this->saveFile = $saveFile;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * @inheritDoc
     */
    #[ReturnTypeWillChange] public function current()
    {
        return current($this->data);
    }

    /**
     * @inheritDoc
     */
    #[ReturnTypeWillChange] public function next()
    {
        return next($this->data);
    }

    /**
     * @inheritDoc
     */
    #[ReturnTypeWillChange] public function key()
    {
        return key($this->data);
    }

    /**
     * @inheritDoc
     */
    #[ReturnTypeWillChange] public function valid()
    {
        return $this->offsetExists(key($this->data));
    }

    /**
     * @inheritDoc
     */
    #[ReturnTypeWillChange] public function rewind()
    {
        return reset($this->data);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[$offset] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }
}
