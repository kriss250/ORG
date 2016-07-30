<?php

/**
 * Company short summary.
 *
 * Company description.
 *
 * @version 1.0
 * @author kris
 */

namespace Kris\Frontdesk;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $connection = "mysql_book";
    protected $table = "companies";
    public $timestamps = false;
    public $primaryKey = "idcompanies";
    protected $guarded = [];
}