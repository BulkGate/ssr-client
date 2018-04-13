<?php

namespace BulkGate\Ssr\Http;


class Header implements IHeader
{
    /** @var string  */
    protected $name;

    /** @var string  */
    protected $value;

    /**
     * Header constructor.
     * @param string $name
     * @param string $value
     */
    public function __construct($name, $value)
    {
        $this->name = $this->formatName($name);
        $this->value = $value;
    }

    private function formatName($name)
    {
        return ucwords(ucfirst(strtolower($name)), "-");
    }

    public static function createFromRaw($raw)
    {
        preg_match('~([\w-]+?)(?:\h+)?:(?:\h+)?(.*?)$~s', $raw, $m);
        list($name, $value) = [$m[1], $m[2]];

        return new self($name, $value);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->name . ": " . $this->value;
    }
}