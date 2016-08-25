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


class Invoice extends Model
{
    protected $connection = "mysql_backoffice";
    protected $table = "invoices";
    public $primaryKey = "idinvoices";
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany("\App\InvoiceItem","invoice_id","idinvoices");
    }

    public function user()
    {
        return $this->belongsTo("\App\User","user_id","id");
    }
}