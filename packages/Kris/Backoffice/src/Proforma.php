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


class Proforma extends Model
{
    protected $connection = "mysql_backoffice";
    protected $table = "proforma";
    public $primaryKey = "idproforma";
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany("\App\ProformaItem","proforma_id","idproforma");
    }

    public function user()
    {
        return $this->belongsTo("\App\User","user_id","id");
    }

    public static function LastProformaID()
    {
        $invoice = self::where(\DB::raw("year(proforma.created_at)"),"=",date("Y"))->orderBy("created_at","desc")->first();
       return $invoice == null || !is_numeric($invoice->code) ? 0: $invoice->code;
    }

}
