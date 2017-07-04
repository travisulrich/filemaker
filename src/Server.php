<?php 

namespace FileMaker;

class Server
{
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
     * @var array
     */
    protected $options = [
        'ssl' => false,
        'pretend_php' => false,
    ];

    /**
     * @param string $host
     * @param string $database
     * @param int $port
     * @param string $username
     * @param string $password
     * @param array $options
     */
    public function __construct($host, $database, $port = 80, $username = null, $password = null, array $options = [])
    {
        $this->host = $host;
        $this->database = $database;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getOption($key)
    {
        if ( ! array_key_exists($key, $this->options)) {
            return false;
        }

        return $this->options[$key];
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
