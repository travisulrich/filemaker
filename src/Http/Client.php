<?php 

namespace FileMaker\Http;

use GuzzleHttp\Client as GuzzleClient;
use Exception;

class Client
{
    /**
     *
     */
    const URI = '/fmi/xml/fmresultset.xml?%s';

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
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var bool
     */
    protected $ssl;

    /**
     * @var
     */
    protected $client;

    /**
     * @param string $host
     * @param int    $port
     * @param string $username
     * @param string $password
     * @param bool   $ssl
     */
    public function __construct($host, $port, $username, $password, $ssl = false)
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->ssl = $ssl;
    }

    /**
     * @param string $query
     * @param array $parameters
     * @return mixed
     */
    public function post($query, $parameters = array())
    {
        return $this->call('POST', $query, $parameters);
    }

    /**
     * Performs an API call to the FileMaker server.
     * 
     * @param string $method
     * @param string $query
     * @param array $parameters
     * @return \FileMaker\Record
     * @throws Exception
     */
    protected function call($method, $query, $parameters)
    {
        $client = $this->getGuzzleClient();
        $uri = $this->createRequestUri($query);

        if ($this->username) {
            $auth = ['auth' => [$this->username, $this->password]];
            $parameters = array_merge($parameters, $auth);
        }
        
        $response = $client->request($method, $uri, $parameters);
        
        switch ($response->getStatusCode()) {
            case 200:
                return $response->getBody()->getContents();
            default:
                $msg = sprintf('Error executing API request: %s %s', $method, $uri);

                throw new Exception($msg);
        }
    }

    /**
     * @return GuzzleClient
     */
    protected function getGuzzleClient()
    {
        if (!$this->client) {
            $url = $this->createBaseUrl();
            $this->client = new GuzzleClient(['base_uri' => $url]);
        }

        return $this->client;
    }

    /**
     * @param array $query
     * @return mixed
     */
    protected function createRequest($query = array())
    {
        $client = $this->getGuzzleClient();
        $uri = $this->createRequestUri($query);

        return $client->post($uri);
    }

    /**
     * @return mixed
     */
    protected function createBaseUrl()
    {
        return sprintf(
            '%s://%s%s',
            $this->ssl ? 'https' : 'http',
            $this->host,
            $this->port == 80 ? '' : ":{$this->port}"
        );
    }

    /**
     * @param array $query
     * @return string
     */
    protected function createRequestUri($query)
    {
        return sprintf(static::URI, $query);
    }
}
