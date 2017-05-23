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


class ProformaItem extends Model
{
    protected $connection = "mysql_backoffice";
    protected $table = "proforma_items";
    protected $guarded = [];
    public $timestamps = false;

    public function proforma()
    {
        $this->belongsTo("\App\Proforma","proforma_id","idproforma");
    }
}