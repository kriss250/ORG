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


class InvoiceItem extends Model
{
    protected $connection = "mysql_backoffice";
    protected $table = "invoice_items";
    protected $guarded = [];
    public $timestamps = false;

    public function invoice()
    {
        $this->belongsTo("\App\Invoice","invoice_id","idinvoices");
    }
}