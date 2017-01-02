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
        $item =self::where("name",$setting)->get()->first();
        if($item != null)
        {
            //Arrays & Single Values
            return isset($item[0]) && (!is_null($item)) && $item->serialized == "1"  ? unserialize($item->value) : $item->value;;
        }else {
            return null;
        }

    }

    public static function set($name,$value)
    {
        if(is_array($value))
        {
            self::updateOrCreate([
                "name"=>$name,
                "value"=>serialize($value),
                "serialized"=>"1"
                ]);
        }else {
            self::updateOrCreate([
               "name"=>$name,
               "value"=>$value,
               "serialized"=>"0"
               ]);
        }
    }
}
