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

class Occurancy extends Model
{
    const ONETIME = 1;
    const MONTHLY = 2;
    const WEEKLY = 3;
    const YEARLY = 4;
}