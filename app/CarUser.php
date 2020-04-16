<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CarUser extends Model
{
    //
    protected $table = 'car_user';
    protected $casts = [
        'extra_info' => 'json',
    ];

    public function getExtraInfoAttribute($extra)
    {
        return array_values(json_decode($extra, true) ?: []);
    }

    public function setExtraInfoAttribute($extra)
    {
        $this->attributes['Extra_info'] = json_encode(array_values($extra));
    }
}
