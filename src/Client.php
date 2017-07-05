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
    private $server;

    /**
     * @var Parser
     */
    private $parser;

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
    private function getHttpClient()
    {
        return new HttpClient(
            $this->server
        );
    }

    /**
     * @return Builder
     */
    public function newQuery()
    {
        $query = new Builder(
            $this->parser,
            $this->getHttpClient()
        );

        return $query->database(
            $this->server->getDatabase()
        );
    }
}
