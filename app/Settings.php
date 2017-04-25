<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $connection="main_db";
    protected $table = "settings";
    protected $guarded = [];
    protected $primaryKey = "name";

    public static function get($setting)
    {
        $item =self::where("name",$setting)->get()->first();
        if($item != null)
        {
            //Arrays & Single Values

            return (!is_null($item)) && $item->serialized == "1"  ? unserialize($item->value) : $item->value;;
        }else {
            return null;
        }

    }

    public static function set($name,$value)
    {
        if(is_array($value))
        {
            $setting = self::firstOrNew(["name"=>$name]);
            $setting->name = $name;
            $setting->value = serialize($value);
            $setting->serialized = "1";
            $setting->save();
        }else {
            $setting = self::firstOrNew(["name"=>$name]);
            $setting->name = $name;
            $setting->value = $value;
            $setting->serialized = "0";
            $setting->save();
        }
    }
}
