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

class Banquet extends Model
{
    protected $connection = "mysql_book";
    protected $table= "banquets";
    protected $primaryKey = "idbanquet";
    public $timestamps = false;

    public function getBooking($startdate,$enddate)
    {
        $halls = $this->all();
        $data = [];
        foreach($halls as $hall)
        {
            $bookings = \DB::connection($this->connection)->select("select * from banquet_booking where banquet_id=? and date between ? and ?",[$hall->idbanquet,$startdate,$enddate]);

            foreach($bookings as $booking){
                if(isset($data[$hall->banquet_name][$booking->date])){
                    $data[$hall->banquet_name][$booking->date] .=$booking->orderid."^~".$booking->orderid."^".$booking->info;
                }else {
                    $data[$hall->banquet_name][$booking->date] = $booking->orderid."^".$booking->info;
                }
            }
        }

        return $data;
    }

    public static function deleteOrder($id)
    {
        return \DB::connection("mysql_book")->delete("delete from banquet_booking where orderid=".$id);
    }
}