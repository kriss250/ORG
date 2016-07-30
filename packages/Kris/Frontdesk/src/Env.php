<?php

/**
 * Charge short summary.
 *
 * Charge description.
 *
 * @version 1.0
 * @author kris
 */

namespace Kris\Frontdesk;
use Illuminate\Database\Eloquent\Model;

class Env extends Model
{
    const dateFormat = "d/m/Y";
   /**
    * WORKING DATE
    */
   public static function WD($format=false)
   {
       $date = \DB::connection("mysql_book")->table("night_audit")->select("new_date")->orderBy("new_date","desc")->limit("1")->first();
       if($format)
       {
           //Convert the date
           return (new \Carbon\Carbon($date->new_date))->format(self::dateFormat);
       }else {
           return new \Carbon\Carbon($date->new_date);
       }
   }

   public static function formatDT($date)
   {
      return (new \Carbon\Carbon($date))->format(self::dateFormat); 
   }


}