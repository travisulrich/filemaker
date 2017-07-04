<?php

namespace FileMaker\Http;

use Exception;
use FileMaker\Record;
use FileMaker\Server;
use GuzzleHttp\Client as GuzzleClient;

class Client
{
    const URI = '/fmi/xml/fmresultset.xml';

    /**
     * @var Server
     */
    private $server;

    /**
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * @param array $data
     * @return Record
     */
    public function post($data = [])
    {
        return $this->call('POST', $data);
    }

    /**
     * Performs an API call to the FileMaker server.
     *
     * @param string $method
     * @param array $data
     * @return Record
     * @throws Exception
     */
    protected function call($method, array $data = [])
    {
        $params = [
            'form_params' => $data,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8',
            ],
        ];

        if ($this->server->getUsername()) {
            $params['auth'] = [
                $this->server->getUsername(),
                $this->server->getPassword()
            ];
        }

        if ($this->server->getOption('pretend_php')) {
            $params['headers']['X-FMI-PE-ExtendedPrivilege'] = 'IrG6U+Rx0F5bLIQCUb9gOw==';
        }

        $uri = static::URI;
        $response = $this->getGuzzleClient()->request($method, $uri, $params);

        if ($response->getStatusCode() !== 200) {
            throw new Exception("Error executing API request: $method $uri");
        }

        return $response->getBody()->getContents();
    }

    /**
     * @return GuzzleClient
     */
    protected function getGuzzleClient()
    {
        static $client;
        if ( ! $client) {
            $client = new GuzzleClient([
                'base_uri' => $this->createBaseUrl(),
            ]);
        }

        return $client;
    }

    /**
     * @return string
     */
    protected function createBaseUrl()
    {
        return sprintf(
            '%s://%s%s',
            $this->server->getOption('ssl') ? 'https' : 'http',
            $this->server->getHost(),
            $this->server->getPort() == 80 ? '' : ":{$this->server->getPort()}"
        );
    }
}
