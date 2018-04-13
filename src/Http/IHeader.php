<?php

namespace BulkGate\Ssr\Http;


interface IHeader
{
    public static function createFromRaw($raw);

    public function getName();

    public function getValue();
}