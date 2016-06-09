<?php 

namespace FileMaker;

use ArrayIterator;
use IteratorAggregate;

class ResultSet implements IteratorAggregate
{
    /**
     * @var int
     */
    protected $count;

    /**
     * @var int
     */
    protected $fetchSize;

    /**
     * @var array
     */
    protected $records;

    /**
     * @param int $count
     * @param int $fetchSize
     * @param array $records
     */
    public function __construct($count, $fetchSize, $records = array())
    {
        $this->count = (int) $count;
        $this->fetchSize = (int) $fetchSize;
        $this->records = $records;
    }

    /**
     * @param Record $record
     */
    public function addRecord(Record $record)
    {
        $this->records[] = $record;
    }

    /**
     * @return mixed
     */
    public function first()
    {
        return reset($this->records);
    }

    /**
     * @param string      $column
     * @param string|null $key
     * @return array
     */
    public function pluck($column, $key = null)
    {
        $plucked = array();
        foreach ($this as $record) {
            if ($key === null) {
                $plucked[] = $record->{$column};
            } else {
                $plucked[$record->{$key}] = $$record->{$column};
            }
        }

        return $plucked;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->records);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        switch ($key) {
            case 'layout':
            case 'records':
                return $this->{$key};
        }
    }
}
