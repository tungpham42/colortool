<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $fillable = [
        'name', 'hex', 'rgb', 'hsl', 'cmyk',
        'pantone', 'category', 'description'
    ];

    protected $casts = [
        'rgb' => 'array',
        'hsl' => 'array',
        'cmyk' => 'array',
    ];

    public function getRgbArrayAttribute()
    {
        return $this->rgb ? json_decode($this->rgb, true) : null;
    }

    public function getHslArrayAttribute()
    {
        return $this->hsl ? json_decode($this->hsl, true) : null;
    }

    public function getCmykArrayAttribute()
    {
        return $this->cmyk ? json_decode($this->cmyk, true) : null;
    }
}
