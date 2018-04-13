<?php

namespace BulkGate\Ssr\Http;


interface IResponse
{
    public function getHeaders();

    public function getBody();

    public function getInfo($key = "");
}