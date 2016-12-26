<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $connection="main_db";
    protected $table = "settings";
    protected $guarded = [];
    protected $primaryKey = "idsettings";

    public static function get($setting)
    {
        return self::where("name",$setting)->get()->first();
    }

    public static function set($name,$value)
    {
        if(is_array($value))
        {
            self::create([
                "name"=>$name,
                "value"=>serialize($value),
                "serialized"=>"1"
                ]);
        }else {
            self::create([
               "name"=>$name,
               "value"=>$value,
               "serialized"=>"0"
               ]);
        }
    }
}
