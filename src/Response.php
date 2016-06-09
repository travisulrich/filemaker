<?php 

namespace FileMaker;

use IteratorAggregate;

class Response implements IteratorAggregate
{
    /**
     * @var Error
     */
    protected $error;

    /**
     * @var Layout
     */
    protected $layout;

    /**
     * @var ResultSet
     */
    protected $resultSet;

    /**
     * @param Error $error
     * @param Layout $layout
     * @param ResultSet $resultSet
     * @throws FileMakerQueryException
     */
    public function __construct(Error $error, Layout $layout, ResultSet $resultSet)
    {
        $this->error = $error;
        $this->layout = $layout;
        $this->resultSet = $resultSet;

        if (!$this->error->isAllowed()) {
            throw new FileMakerQueryException(
                $this->error->message(),
                $this->error->code()
            );
        }
    }

    /**
     * @return mixed
     */
    public function getIterator()
    {
        return $this->resultSet->getIterator();
    }

    /**
     * @param string $name
     * @param array  $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array(
            array($this->resultSet, $name),
            $arguments
        );
    }
}
