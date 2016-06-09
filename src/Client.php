<?php 

namespace FileMaker;

use FileMaker\Http\Client as HttpClient;
use FileMaker\Parser\Parser;
use FileMaker\Query\Builder;

class Client
{
    /**
     * @var Server
     */
    protected $server;

    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @param Server $server
     * @param Parser $parser
     */
    public function __construct(Server $server, Parser $parser)
    {
        $this->server = $server;
        $this->parser = $parser;
    }

    /**
     * @return HttpClient
     */
    public function getHttpClient()
    {
        return new HttpClient(
            $this->server->host,
            $this->server->port,
            $this->server->username,
            $this->server->password
        );
    }

    /**
     * @return mixed
     */
    public function newQuery()
    {
        $query = new Builder(
            $this->parser,
            $this->getHttpClient()
        );

        return $query->database(
            $this->server->database
        );
    }
}
