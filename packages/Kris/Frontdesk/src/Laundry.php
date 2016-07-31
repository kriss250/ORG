<?php

/**
 * Room short summary.
 *
 * Room description.
 *
 * @version 1.0
 * @author kris
 */
namespace Kris\Frontdesk;
use Illuminate\Database\Eloquent\Model;

class Laundry extends Model
{
    protected $connection = "mysql_book";
    protected $table = "laundry";
    public $primaryKey = "idlaundry";
    public $timestamps = false;

    public function reservation()
    {
        return $this->belongsTo("\Kris\Frontdesk\Reservation","reservation_id","idreservation");
    }

    public function user()
    {
        return $this->belongsTo("\Kris\Frontdesk\User","user_id","idusers");
    }

}