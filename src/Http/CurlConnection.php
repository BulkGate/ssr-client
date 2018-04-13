<?php

namespace BulkGate\Ssr\Http;

use BulkGate,
    BulkGate\Utils;


class CurlConnection implements IConnection
{
    use BulkGate\Strict;

    protected $client;

    protected $headers = [];

    public function __construct()
    {
        $this->client = curl_init();
    }

    public function request($url, $data, $method = "post", $mime = "application/json")
    {
        $method = strtolower($method);
        $this->setHeader("Content-Type", $mime);

        curl_setopt_array($this->client, [
            CURLOPT_URL => $url,
            CURLOPT_POST => $method === "post",
            CURLOPT_HTTPGET => $method === "get",
            CURLOPT_HTTPHEADER => $this->headers,
            CURLOPT_POSTFIELDS => Utils\Json::encode($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            //CURLINFO_HEADER_OUT => true,
        ]);

        return new CurlResponse($this, curl_exec($this->client));
    }

    public function setHeader($name, $value)
    {
        $this->headers[$name] = "$name: $value";
    }

    public function setHeaders($headers)
    {
        foreach($headers as $name => $value)
        {
            $this->setHeader($name, $value);
        }
    }

    public function getHandle()
    {
        return $this->client;
    }
}