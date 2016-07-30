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

class Charge extends Model
{
    protected $connection = "mysql_book";
    protected $table= "room_charges";
    protected $primaryKey = "idroom_charge";
    public $timestamps = false;

    public function addCharge($amount,$charge_type_id,$motif,$resid)
    {
        \DB::beginTransaction();
        try {
            $res  = Reservation::find($resid);

            $in = $this->insert([
                "room_id"=>$res->room_id,
                "charge"=>$charge_type_id,
                "amount"=>$amount,
                "motif"=>$motif,
                "reservation_id"=>$res->idreservation,
                "date"=>Env::WD()->format("Y-m-d"),
                "user_id"=>User::me()->idusers,
                "user"=>User::me()->username,
                "reserved_room_id"=>0]);

            $res->due_amount = $res->due_amount+$amount;
            $ix = $res->save();
            if($in > 0 && $ix > 0)
            {
                \DB::commit();
                return true;
            }else {
                \DB::rollBack();
                return false;
            }
        }catch(\Exception $e)
        {
            \DB::rollBack();
            return false;
        }
    }

}