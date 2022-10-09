<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['id', 'slug', 'name', 'created_by'];

    public $incrementing = false;

    public function products()
    {
        return $this->belongsToMany('App\Product');
    }
}
