<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class POSSettings extends Model
{
    protected $connection="mysql_pos";
    protected $table = "settings";
    protected $guarded = [];
    protected $primaryKey = "setting_name";

    public static function get($setting)
    {
        $item =self::where("setting_name",$setting)->get()->first();
        if($item != null)
        {
            //Arrays & Single Values

            return  $item->setting_value;;
        }else {
            return null;
        }

    }

    public static function set($name,$value)
    {
        if(is_array($value))
        {
            $setting = self::firstOrNew(["setting_name"=>$name]);
            $setting->setting_name = $name;
            $setting->setting_value = serialize($value);
            $setting->save();
        }else {
            $setting = self::firstOrNew(["setting_name"=>$name]);
            $setting->setting_name = $name;
            $setting->setting_value = $value;
            $setting->save();
        }
    }
}
