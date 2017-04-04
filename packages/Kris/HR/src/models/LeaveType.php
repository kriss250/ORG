<?php

/**
 * Room short summary.
 *
 * Room description.
 *
 * @version 1.0
 * @author kris
 */
namespace Kris\HR;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    const MEDICAL = 1;
    const LOSS = 2;
    const YEARLY =3;
    const OTHER =4;
}