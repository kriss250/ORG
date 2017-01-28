<?php

/**
 * Floor short summary.
 *
 * Floor description.
 *
 * @version 1.0
 * @author kris
 */

namespace Kris\Frontdesk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends Model
{
    protected $connection = "mysql_book";
    protected $table = "users";
    public $primaryKey = "idusers";
    public $timestamps = false;
    public $guarded=[];
    public static function me()
    {
        return \Session::get("fo_user");
    }

    public function reservation()
    {
        return $this->hasMany("Kris\Frontdesk\Reservation","user_id","idusers");
    }

    public function payment()
    {
        return $this->hasMany("Kris\Frontdesk\Payment","user_id","idusers");
    }

    public function log()
    {

        $this->belongsToMany("\Kris\Frontdesk\Log","user_id","idusers");
    }
}