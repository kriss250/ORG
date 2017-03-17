<?php

/**
 * Bill short summary.
 *
 * Invoice description.
 *
 * @version 1.0
 * @author kris
 */
namespace App;

use Illuminate\Database\Eloquent\Model;


class Cashbook extends Model
{
  protected $primaryKey= "idbills";
  public $timestamps = false;
  protected $table = "bills";
  protected $connection = "mysql_pos";

  public static function getCashExpenses()
  {
      $start_date = isset($_GET['startdate']) ? $_GET['startdate'] : \ORG\Dates::$RESTODATE;
      $end_date = isset($_GET['enddate']) ? $_GET['enddate'] : \ORG\Dates::$RESTODATE;

      $range = [$start_date,$end_date];

      $q = "SELECT cashbook_name, sum(amount) as amount FROM org_backoffice.cashbook_transactions
join cash_book on cash_book.cashbookid = cashbook_id
where type ='OUT' and deleted = 0 and cancelled=0 and (date(date) between ? and ?) group by cashbook_id";

      return \DB::connection("mysql_backoffice")->select($q,$range);

  }
}
