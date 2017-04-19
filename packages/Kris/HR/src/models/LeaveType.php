<?php

/**
 * Room short summary.
 *
 * Room description.
 *
 * @version 1.0
 * @author kris
 */
namespace Kris\HR\Models;
use Illuminate\Database\Eloquent\Model;

class LeaveType
{
    const MEDICAL = 1;
    const LOSS = 2;
    const MATERNITY =3;
    const ADMINISTRATIVE =4;
    const OTHER = 5;

    public static function get()
    {
        $obj = new \ReflectionClass(__CLASS__);
        return $obj->getConstants();
    }
}