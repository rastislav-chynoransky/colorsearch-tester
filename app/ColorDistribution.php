<?php

namespace App;

use Primal\Color\Parser;

class ColorDistribution implements \Iterator
{
    protected $colors = [];

    public function __construct(array $colors)
    {
        $this->colors = $colors;
    }

    public function current()
    {
        return current($this->colors);
    }

    public function next()
    {
        next($this->colors);
    }

    public function key()
    {
        return Parser::Parse(key($this->colors));
    }

    public function valid()
    {
        return key($this->colors) !== null;
    }

    public function rewind()
    {
        reset($this->colors);
    }

}
