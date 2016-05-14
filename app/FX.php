<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FX extends Model
{
    public static $RowGroupArray=[];
    public static $GroupCount = 0;
    public static function Date($date)
    {
        $tsp=strtotime($date);
        return date("d/m/Y",$tsp);
    }
    
    public static function Time($time)
    {
        $tsp=strtotime($time);
        return date("H:i:s",$tsp);
    }
    
    /*
     * Date and Time
     * */
    public static function DT($date)
    {
        $tsp=strtotime($date);
        if(is_null($date))
        {
            return "";
        }
        return date("d/m/Y H:i:s",$tsp);
    }

    public static function DBDate($date)
    {
        $tsp=strtotime($date);
        if(is_null($date))
        {
            return "";
        }
        return date("Y-d-m",$tsp);
    }

    public static function RowIsGrouped($identifier)
    {
        
    }

    public static function RowSpanner($identifier)
    {
        static $spanMatch = [];
        static $span = 1;

        if(strlen($identifier)>0){
            if(array_search($identifier,$spanMatch))
            {
                $span=$span+1;
            }else {
                //New Item
                $span=1; //last
                array_push($spanMatch,$identifier);
            }
        }else {$span=1;}

       
        return $span;
    }

    public static function GetCashiers()
    {
       return \DB::select("select id,username from users where level < 7 and is_active=1");
    }
}
