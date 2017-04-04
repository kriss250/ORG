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

class Degree
{
    const NONE = 1;
    const PRIMARY =2;
    const SECONDARY =3;
    const COLLEGE = 4;
    const BACHELOR = 5;
    const MASTERS = 6;
    const DOCTOR = 7;
    const PROFESSOR = 8;
}