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

    public static function LastInvoiceID()
    {
        $invoice = self::where(\DB::raw("year(invoices.created_at)"),"=",date("Y"))->orderBy("created_at","desc")->first();
        $id = $invoice == null ? 0 : explode("/", $invoice->code);
        return $invoice == null || !isset($id[0]) ||  !is_numeric($id[0]) ? 0 : $invoice->code;
    }

    public function payment()
    {
      return $this->hasMany("\App\InvoicePayment","invoice_id","idinvoices");
    }
}
