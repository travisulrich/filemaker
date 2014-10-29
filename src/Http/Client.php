<?php namespace FileMaker\Http;

use Guzzle\Http\Client as GuzzleClient;

class Client {
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
     * @return mixed
     */
    public function post($query)
    {
        $request = $this->createRequest($query);

        if($this->username) {
            $request->setAuth(
                $this->username,
                $this->password
            );
        }

        $response = $request->send();
        
        return $response->getBody(true);
    }


    /**
     * @return GuzzleClient
     */
    protected function getGuzzleClient()
    {
        if(!$this->client) {
            $url = $this->createBaseUrl();
            $this->client = new GuzzleClient($url);
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
