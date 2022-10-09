<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductShop extends Model
{
    use SoftDeletes;
    protected $table = 'product_shop';
    protected $guarded = [];
}
