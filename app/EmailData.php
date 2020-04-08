<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailData extends Model
{
    //
     protected $table = 'email_data';
     protected $fillable = ['username'];
     
//    public function email_config()
//    {
//        return $this->belongsTo(EmailConfig::class,'id');
//    }
}
