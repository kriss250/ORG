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
    public $timestamps = false;
    protected $guarded = [];
}