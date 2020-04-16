<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CarOrder extends Model
{
    //
    protected $table = 'car_order';
    protected $casts = [
        'information' => 'json',
    ];

    public function getInformationAttribute($extra)
    {
        return array_values(json_decode($extra, true) ?: []);
    }

    public function setInformationAttribute($extra)
    {
        $this->attributes['Information'] = json_encode(array_values($extra));
    }
}
