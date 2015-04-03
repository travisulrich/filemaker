<?php namespace FileMaker;

class Server {
    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var string
     */
    protected $database;
    
    /**
     * @var bool
     */
    protected $secure;

    /**
     * @param string $host
     * @param string $database
     * @param int    $port
     * @param string $username
     * @param string $password
     */
    public function __construct($host, $database, $port = 80, $username = null, $password = null, $secure = false)
    {
        $this->host = $host;
        $this->database = $database;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->secure = $secure;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if(property_exists($this, $key)) {
            return $this->{$key};
        }
    }
} 
