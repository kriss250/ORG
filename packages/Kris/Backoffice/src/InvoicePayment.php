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


class InvoicePayment extends Model
{
    protected $connection = "mysql_backoffice";
    protected $table = "invoice_payments";
    protected $guarded = [];
    public $primaryKey = "idpayment";

    public function invoice()
    {
        $this->belongsTo("\App\Invoice","invoice_id","idinvoices");
    }
}
