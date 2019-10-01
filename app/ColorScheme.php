<?php

namespace App;

class ColorScheme
{
    protected $colors;

    public function addColor(string $hex, float $amount)
    {
        $this->colors[$hex] = $amount;
    }

    public function getColors()
    {
        return $this->colors;
    }
}