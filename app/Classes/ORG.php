<?php

namespace ORG;
use DB;
use Auth;


if(isset(\Auth::user()->level) && \Auth::user()->level==10) \Config::set('app.debug', true);

class POS extends Settings
{
    public static function Log($activity,$type)
    {
        $done = \DB::insert("insert into logs (user_id,type,action,date) values(?,?,?,?)",[Auth::user()->id,$type,$activity,Dates::$RESTODT]);
    }
}

/**
* Biller
*/
class Bill extends POS
{
    public static $FOOTER = "Thank you for choosing";
    const SUSPENDED = 1;
    const PAID = 2;
    const ASSIGNED = 3;
    const CREDIT =5;
    const OFFTARIFF = 4;
}

class RoomStatus
{
    const  RESERVED = 4;
    const  VACANT = 1;
    const  OCCUPIED = 2;
    const  BLOCKED = 3;
    const  DIRTY = 5;    
}

class Reservation
{
    const  ACTIVE = 1;
    const  CANCELLED = 2;
    const   VOID = 3;
    const  NOSHOW = 4;
    const CHECKEDIN = 5;
    const CHECKEDOUT = 6;
}




class Dates extends Settings {
    
    const DBDATEFORMAT = "Y-m-d H:i:s";
    const DSPDATEFORMAT = "d/m/Y";
    
    public static function WORKINGDATE($dbformat = false,$date_only=false)
    {
        $time = " ".date("H:i:s");
        if($date_only){
            $time = "";
        }
        $date  = strtotime( DB::connection("mysql")->select('select date(new_date) as new_date from night_audit order by idnight_audit desc limit 1')[0]->new_date);

        if($dbformat){
            $format = $date_only ? "Y-m-d" : self::DBDATEFORMAT;
            return date($format,$date);
        }else {
            return date(self::DSPDATEFORMAT,$date);
        }
    }
    
    public static function ToDBFormat ($date){
        $tsp = strtotime(str_replace("/","-", $date));
        return date(self::DBDATEFORMAT,$tsp);
    }
    
    public static function ToDSPFormat($date){
        $tsp = strtotime(str_replace("/","-", $date));
        return date(self::DSPDATEFORMAT,$tsp);
    }

    public static $RESTODATE;
    public static $RESTODT;

    public static function setDate($date="")
    {
       
        
        if(strlen($date)==0){
            $dateObj = DB::select("SELECT date(new_date) as new_date FROM night_audit order by idnight_audit desc limit 1");
            if($dateObj){
                $date =$dateObj[0];
                self::$RESTODATE = $date->new_date;
            }else{
                self::$RESTODT = date("Y-m-d H:i:s");
                self::$RESTODATE = date("Y-m-d");
            }
        }else {
            self::$RESTODATE = $date;
        }

        self::$RESTODT = self::$RESTODATE." ".date("H:i:s");
        //self::$RESTODATE = self::$RESTODATE." ".date("H:i:s");
    }
    
}

Dates::setDate();

