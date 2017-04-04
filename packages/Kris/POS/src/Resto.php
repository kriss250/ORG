<?php

/**
 * Resto short summary.
 *
 * Resto description.
 *
 * @version 1.0
 * @author kris
 */
namespace App;
use Illuminate\Database\Eloquent\Model;


class Resto extends Model
{
    protected $primaryKey= "id";
    public $timestamps = false;
    protected $table = "resto_info";
    protected $connection = "mysql_pos";
}