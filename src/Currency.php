<?php

namespace Orpheus\LaravelCountries;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class Currency implements Arrayable, Jsonable, JsonSerializable
{
    protected $name;
    protected $code;
    protected $symbol;

    public function __construct(string $code, string $name, string $symbol)
    {
        $this->code = $code;
        $this->name = $name;
        $this->symbol = $symbol;
    }

    /**
     * Create a new instance of Currency
     *
     * @param array $data
     * @return void
     */
    public static function make(array $data)
    {
        if (!isset($data['code'], $data['name'], $data['symbol'])) {
            throw new \InvalidArgumentException('The currency data is invalid.');
        }

        return new static(
            $data['code'],
            $data['name'],
            $data['symbol']
        );
    }

    /**
     * Get the currency's name.
     *
     * @return string   The currency's name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the currency's code.
     *
     * @return string   The currency's code
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get the currency's symbol.
     *
     * @return string   The currency's symbol
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'code' => $this->code,
            'symbol' => $this->symbol,
        ];
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    public function __toString()
    {
        return $this->name;
    }
}
