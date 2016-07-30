<?php

/**
 * Guest short summary.
 *
 * Guest description.
 *
 * @version 1.0
 * @author kris
 */

namespace Kris\Frontdesk;
use Illuminate\Database\Eloquent\Model;


class Guest extends Model
{
    protected $connection = "mysql_book";
    protected $table = "guest";
    public $primaryKey = "id_guest";
    public $timestamps = false;
    protected $guarded = [];

    public function reservation()
    {
        return $this->hasMany("\Kris\Frontdesk\Reservation","guest_id","id_guest");
    }
}