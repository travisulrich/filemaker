<?php namespace FileMaker;

class Layout {

    /**
     * @var array
     */
    protected $fields;

    /**
     * @param array $fields
     */
    public function __construct($fields = array())
    {
        $this->fields = $fields;
    }

    /**
     * @param Field $field
     */
    public function addField(Field $field)
    {
        $this->fields[$field->name] = $field;
    }

    /**
     * @param $name
     */
    public function removeField($name)
    {
        if(isset($this->fields[$name])) {
            unset($this->fields[$name]);
        }
    }
} 
