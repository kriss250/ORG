<?php

/**
 * Invoice short summary.
 *
 * Invoice description.
 *
 * @version 1.0
 * @author kris
 */
namespace App;

use Illuminate\Database\Eloquent\Model;


class CategoryStore extends Model
{
    protected $primaryKey= "category_id";
    public $timestamps = false;
    protected $table = "category_store";
    protected $connection = "mysql_pos";
    public $guarded = [];

    public function store()
    {
        return $this->hasOne("\App\Store","idstore","store_id");
    }


    public function category()
    {
        return $this->hasOne("\App\Category","id","category_id");
    }
}
