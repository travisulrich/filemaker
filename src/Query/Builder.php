<?php namespace FileMaker\Query;

use FileMaker\Http\Client;
use FileMaker\Parser\Parser;
use FileMaker\Response;
use FileMaker\Server;

class Builder {

    /**
     * @var array
     */
    protected static $operators = array(
        '=' => 'eq',
        '>' => 'gt',
        '>=' => 'gte',
        '<' => 'lt',
        '<=' => 'lte'
    );

    /**
     * @var string
     */
    protected $database;

    /**
     * @var string
     */
    protected $layout;

    /**
     * @var array
     */
    protected $wheres = array();

    /**
     * @var array
     */
    protected $orders = array();

    /**
     * @var string
     */
    protected $findCommand;

    /**
     * @var int
     */
    protected $recordId;

    /**
     * @var int
     */
    protected $skip;

    /**
     * @var int
     */
    protected $take;

    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var HttpClient
     */
    protected $client;

    /**
     * @param Parser $parser
     * @param Client $client
     */
    public function __construct(Parser $parser, Client $client)
    {
        $this->parser = $parser;
        $this->client = $client;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function database($name)
    {
        $this->database = $name;

        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function layout($name)
    {
        $this->layout = $name;

        return $this;
    }

    /**
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @param bool $omit
     * @return $this
     */
    public function where($column, $operator, $value = null, $omit = false)
    {
        if($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = (object) compact(
            'column',
            'omit',
            'operator',
            'value'
        );

        return $this;
    }

    /**
     * @param string $column
     * @param string $operator
     * @param string $value
     * @param bool $omit
     * @return $this
     */
    public function orWhere($column, $operator, $value, $omit = false)
    {
        return $this->where($column, $operator, $value, $omit, 'or');
    }

    /**
     * @param string $column
     * @param int    $first
     * @param int    $second
     * @param bool   $omit
     * @return $this
     */
    public function whereRange($column, $first, $second, $omit = false)
    {
        $value = sprintf('%d...%d', $first, $second);

        return $this->where($column, '', $value, $omit);
    }

    /**
     * @param string $column
     * @param array  $values
     * @param bool   $omit
     * @return $this
     */
    public function whereIn($column, $values, $omit = false)
    {
        foreach($values as $value) {
            $this->where($column, '=', $value, $omit);
        }

        return $this;
    }

    /**
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        $this->orders[] = (object) compact(
            'column',
            'direction'
        );

        return $this;
    }

    /**
     * @param int $amount
     * @return $this
     */
    public function skip($amount)
    {
        $this->skip = $amount;

        return $this;
    }

    /**
     * @param int $amount
     * @return $this
     */
    public function take($amount)
    {
        $this->take = $amount;

        return $this;
    }

    /**
     *
     */
    protected function execute()
    {
        $query = $this->buildQueryString($this->build());

        $response = $this->client->post($query);

        return $this->parser->parse($response);
    }

    /**
     *
     */
    protected function build()
    {
        $params = array(
            '-db' => $this->database,
            '-lay' => $this->layout,
            $this->findCommand => true
        );

        switch($this->findCommand) {
            case '-find':
                if($this->recordId) {
                    return array_merge($params, $this->buildFindByRecordId());
                } else {
                    return array_merge($params, $this->buildFind());
                }
            case '-findall':
                return array_merge($params, $this->buildFindAll());
            case '-findany':
                return array_merge($params, $this->buildFindAny());
            case '-findquery':
                return array_merge($params, $this->buildFindQuery());
        }

        return $params;
    }

    /**
     * @return array
     */
    protected function buildFindQuery()
    {
        $query = array();
        foreach ($this->wheres as $index => $where) {
            $key = "q{$index}";

            $params["-{$key}"] = $where->column;
            $params["-{$key}.value"] = $where->operator.$where->value;

            $query[] = sprintf(
                '%s(%s)',
                $where->omit ? '!' : '',
                $key
            );
        }

        $params['-query'] = implode(';', $query);

        return $params;
    }

    /**
     *
     */
    public function buildFind()
    {
        $params = array();
        foreach($this->wheres as $where) {
            $params[$where->column] = $where->value;
            $params[$where->column.'.op'] = static::$operators[$where->operator];
        }

        return $params;
    }

    /**
     *
     */
    public function buildFindByRecordId()
    {
        return array(
            '-recid' => $this->recordId
        );
    }

    /**
     * @return array
     */
    public function buildFindAll()
    {
        return array();
    }

    /**
     * @return array
     */
    public function buildFindAny()
    {
        return array();
    }

    /**
     * @param int $recordId
     * @return Response
     */
    public function find($recordId)
    {
        $this->findCommand = '-find';
        $this->recordId = $recordId;

        return $this->execute()->first();
    }

    /**
     *
     */
    public function get()
    {
        if(count($this->wheres) > 0) {
            $this->findCommand = '-findquery';
        } else {
            $this->findCommand = '-findall';
        }

        return $this->execute();
    }

    /**
     *
     */
    public function first()
    {
        $this->skip(0)->take(1);
        if ($this->recordId or count($this->wheres) > 0) {
            $this->findCommand = '-find';
        } else {
            $this->findCommand = '-findany';
        }

        return $this->execute()->first();
    }

    /**
     * @return Response
     */
    public function all()
    {
        $this->findCommand = '-findall';

        return $this->execute();
    }

    /**
     * @param array $query
     * @return string
     */
    protected function buildQueryString($query = array())
    {
        $params = array();
        foreach($query as $key => $value) {
            if($value === true) {
                $params[] = $key;
            } else {
                $params[] = implode('=', array($key, $value));
            }
        }

        return implode('&', $params);
    }
}
