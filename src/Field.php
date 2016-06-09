<?php 

namespace FileMaker;

class Field
{
    /**
     * @var bool
     */
    protected $autoEnter;

    /**
     * @var bool
     */
    protected $fourDigitYear;

    /**
     * @var bool
     */
    protected $global;

    /**
     * @var int
     */
    protected $maxRepeat;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $notEmpty;

    /**
     * @var bool
     */
    protected $numericOnly;

    /**
     * @var string
     */
    protected $result;

    /**
     * @var bool
     */
    protected $timeOfDay;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param string $autoEnter
     * @param string $fourDigitYear
     * @param string $global
     * @param string $maxRepeat
     * @param string $name
     * @param string $notEmpty
     * @param string $numericOnly
     * @param string $result
     * @param string $timeOfDay
     * @param string $type
     */
    public function __construct($autoEnter, $fourDigitYear, $global, $maxRepeat, $name, $notEmpty, $numericOnly, $result, $timeOfDay, $type)
    {
        $this->autoEnter = $autoEnter === 'yes';
        $this->fourDigitYear = $fourDigitYear === 'yes';
        $this->global = $global === 'yes';
        $this->maxRepeat = (int) $maxRepeat;
        $this->name = $name;
        $this->notEmpty = $notEmpty === 'yes';
        $this->numericOnly = $numericOnly === 'yes';
        $this->result = $result;
        $this->timeOfDay = $timeOfDay === 'yes';
        $this->type = $type;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->{$key};
    }
}
