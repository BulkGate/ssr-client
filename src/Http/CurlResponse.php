<?php

namespace BulkGate\Ssr\Http;

use BulkGate;

class CurlResponse implements IResponse
{
    use BulkGate\Strict;

    protected $handle;

    protected $body;

    protected $headers;

    protected $info;

    /**
     * CurlResponse constructor.
     * @param CurlConnection $connection
     * @param string $response
     * @throws HttpFailedResponse
     */
    public function __construct($connection, $response)
    {
        $this->parseHttpResponse($response);
        $this->handle = $connection->getHandle();
        $status_code = $this->getInfo("http_code");

        if ($status_code !== 200)
        {
            throw new HttpFailedResponse("Status code is $status_code");
        }
    }

    private function parseHttpResponse($response)
    {
        list($headers, $body) = explode("\r\n\r\n", $response);

        $this->headers = $headers;
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getInfo($key = "")
    {
        if (!$this->info)
        {
            $this->info = curl_getinfo($this->handle);
        }

        return $key ? $this->info[$key] : $this->info;
    }
}