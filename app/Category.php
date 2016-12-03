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

class Category extends Model
{
    protected $primaryKey= "idcategory";
    public $timestamps = false;
    protected $table = "categories";
    protected $connection = "mysql_pos";

    public function store()
    {
        return $this->belongsToMany("\App\CategoryStore","category_store","category_id","store_id");
    }
}
