<?php

/**
 * RoomType short summary.
 *
 * RoomType description.
 *
 * @version 1.0
 * @author kris
 */

namespace Kris\Frontdesk;

class Helpers {
    public function log($activity)
    {
        Log::create([
            "user_id"=>User::me()->idusers,
            "type"=>"Default",
            "action"=>$activity,
            "date"=>Env::WD()->format("Y-m-d")." ".date("H:i:s")
            ]);
    }

    public function me()
    {
        return User::me();
    }
}