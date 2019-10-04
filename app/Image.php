<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'path',
        'colors',
    ];

    protected $casts = [
        'colors' => 'json',
    ];

    const COLOR_AMOUNT_THRESHOLD = 0.03;

    public function getColors()
    {
        return array_filter($this->colors, function ($amount) {
            return $amount >= self::COLOR_AMOUNT_THRESHOLD;
        });
    }

    public function getColorDistribution()
    {
        return new ColorDistribution($this->getColors());
    }

    public function getUrl()
    {
        return sprintf(
            '%s/%s',
            trim(config('images.path'), '/'),
            $this->path
        );
    }
}
