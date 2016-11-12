<?php

/**
 * Payment short summary.
 *
 * Payment description.
 *
 * @version 1.0
 * @author kris
 */
namespace Kris\Frontdesk;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $connection = "mysql_book";
    protected $table= "folio";
    protected $primaryKey = "id_folio";
    public $timestamps = false;
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo("\Kris\Frontdesk\User","user_id","idusers");
    }

    public function mode()
    {
        return $this->hasOne("\Kris\Frontdesk\PayMethod","idpay_method","paymethod");
    }

    public function reservation()
    {
        return $this->belongsTo("\Kris\Frontdesk\Reservation","reservation_id","idreservation");
    }

    public function currency()
    {
        return $this->hasOne("\Kris\Frontdesk\Currency","idcurrency","currency_id");
    }
}