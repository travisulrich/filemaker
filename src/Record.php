<?php namespace FileMaker;

class Record {
    /**
     * @var int
     */
    protected $recordId;

    /**
     * @var int
     */
    protected $modId;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @param int $recordId
     * @param int $modId
     * @param array $attributes
     */
    public function __construct($recordId, $modId, $attributes = array())
    {
        $this->recordId = $recordId;
        $this->modId = $modId;
        $this->attributes = $attributes;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function addAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * @param string $column
     * @param int    $repetition
     * @return null
     */
    public function get($column, $repetition = 1)
    {
        if(isset($this->attributes[$column])) {
            $attribute = $this->attributes[$column];
            $index = $repetition -1;

            return isset($attribute[$index]) ? $attribute[$index] : null;
        }
    }

    /**
     * @param string $key
     * @return null
     */
    public function __get($key)
    {
        switch($key) {
            case 'recordId':
            case 'modId':
            case 'attributes':
                return $this->{$key};
            default:
                return $this->get($key);
        }
    }
} 
