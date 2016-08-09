<?php

/**
 * ReservationGroup short summary.
 *
 * ReservationGroup description.
 *
 * @version 1.0
 * @author kris
 */

namespace Kris\Frontdesk;
use Illuminate\Database\Eloquent\Model;

class ReservationGroup extends Model
{
    protected $connection = "mysql_book";
    protected $table = "reservation_group";
    public $primaryKey = "groupid";
    public $timestamps = false;
    protected $guarded = [];

    public function reservation()
    {
        return $this->hasMany("\Kris\Frontdesk\Reservation","group_id","groupid");
    }
}