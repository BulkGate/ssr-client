<?php

namespace BulkGate\Ssr\Http;

interface IConnection
{
    public function request($url, $data, $method = "post");

    public function setHeader($name, $value);

    public function setHeaders($headers);

    public function getHandle();
}