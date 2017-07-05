<?php

namespace FileMaker;

use FileMaker\Parser\Parser;
use InvalidArgumentException;
use LogicException;

class FileMaker
{
    /**
     * @var array
     */
    protected $clients = array();

    /**
     * @var array
     */
    protected $servers = array();

    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var string
     */
    protected $defaultServer;

    /**
     * @param Parser $parser
     */
    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param string $name
     * @param Server $server
     */
    public function addServer($name, Server $server)
    {
        $this->servers[$name] = $server;
    }

    /**
     * @param string $name
     */
    public function setDefaultServer($name)
    {
        $this->defaultServer = $name;
    }

    /**
     * @return string
     * @throws LogicException
     */
    public function getDefaultServer()
    {
        if ($this->defaultServer) {
            return $this->defaultServer;
        }

        throw new LogicException(
            'You have to register a default server before trying to use it.'
        );
    }

    /**
     * @param string $serverName
     * @return Client
     * @throws InvalidArgumentException
     */
    public function client($serverName = null)
    {
        if ($serverName === null) {
            $serverName = $this->getDefaultServer();
        }

        if ( ! isset($this->servers[$serverName])) {
            throw new InvalidArgumentException(
                sprintf('Server not registered: %s', $serverName)
            );
        }

        if ( ! isset($this->clients[$serverName])) {
            $server = $this->servers[$serverName];
            $this->clients[$serverName] = new Client(
                $server,
                $this->parser
            );
        }

        return $this->clients[$serverName];
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array(
            array($this->client(), $name),
            $arguments
        );
    }
}
