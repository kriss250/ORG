<?php

/**
 * Company short summary.
 *
 * Company description.
 *
 * @version 1.0
 * @author kris
 */

namespace Kris\Frontdesk;
use Illuminate\Database\Eloquent\Model;

class ReservationStatus extends Model
{
    public $timestamps = false;
    protected $table = "reservation_status";
    protected $primaryKey = "idreservation_status";
    protected $connection = "mysql_book";
}