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

class UserType extends Model
{
    const ADMIN=1;
    const USER = 2;
    const VIEWER = 3;
}