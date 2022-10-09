<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    protected $table = "shops";
    
    public function products()
    {
        return $this->belongsToMany('App\Product');
    }
}
