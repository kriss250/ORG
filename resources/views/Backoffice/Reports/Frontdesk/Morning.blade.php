@extends(isset($_GET['import']) ? "ORGFrontdesk.Reports.Master" : "Backoffice.Master")


@section("contents")
<style type="text/css">
    .room_end td {
        border-top-width: 3px  !important;
    }


    .group-reservation td {
        border-top: 1px dashed #ccc !important;
         position: relative;
        border-bottom: 1px dashed #000 !important;
        background:rgb(245, 245, 245)
    }

</style>

<?php
$rows = "";
$cc =  0 ;
$cash = 0 ;
$check = 0;
$span = 0 ;
$row = "";
$spans = [];
$firstRow = false;
$i=0;
static $spanx  = 1;
    foreach($data as $res)
    {
       
        $rows .="<tr>";
        #region IS IT THE FIRT ROW
        $span = $res->gsize > 1 ? "rowspan='$res->gsize'" : "";
        if($i==0)
        {
            //first Row of the table
            $firstRow  = true;
        }else if($data[$i-1]->gsize != $res->gsize || $res->gsize  < 2){
            //First Row of the group or row with span < 1
            $firstRow = true;
        }else {
            $firstRow = false;
        }

        #endregion

        $rows .= "<td>".$res->room_number."</td>";
        $rows .="<td>".$res->guest."</td>";

        if($firstRow){
            $rows .= "<td $span>".$res->company ."</td>";
            $rows .= "<td $span>".$res->cash."</td>" ;
            $rows .=  "<td $span>".$res->cc."</td>" ;
            $rows .= "<td $span>".$res->chec."</td>" ;
            $rows .= "<td $span>0</td>" ;
            $rows .=  "<td $span>0</td>" ;
        }


        $rows .="</tr>";

        $i++;
    }

?>
<div class="page-contents">

<div class="report-filter">
<table style="width:100%">
    <tr>
        <td><h3>Morning Report</h3> </td>
        <td>
          <form style="float:right" action="" class="form-inline" method="get">
                <label>Date</label> 
                <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control"> - 
                <input name="enddate" type="text" value="{{\ORG\Dates::$RESTODATE }}" class="date-picker form-control">
                        @if(isset($_GET['import']))
                        <input type="hidden" name="import" value="" />
                        @endif

                        <input type="submit" class="btn btn-success btn-sm" value="Go">
                 <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Frontoffice Control" class="btn btn-default report-print-btn">Print</button>
           </form> 
        </td>
    </tr>

     <tr>
      <td>
      <p class="text-danger"><b>Date : {{isset($_GET['startdate']) && isset($_GET['enddate'])  ?  \App\FX::Date($_GET['startdate'])." - ".\App\FX::Date($_GET['enddate']) : \App\FX::Date(\ORG\Dates::$RESTODATE) }}</b></p>
      </td>
    </tr>
    
</table>
</div>

    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th rowspan="2">Room</th>
                <th rowspan="2">Guest</th>
                <th rowspan="2">Company</th>
                <th class="text-center" colspan="4">Mode of Pay.</th>
                <th rowspan="2">Total</th>
            </tr>

            <tr>
                <th>Cash</th>
                <th>CC</th>
                <th>Check</th>
                <th>Credit</th>
            </tr>
        </thead>

        {!! $rows !!}
    </table>


<div class="text-center print-footer">
       <table style="margin-bottom:85px;width:100%;" class="table">
           <tr>
               <td>
                   Cashier
               </td>

               <td>
                   CONTROLLER
               </td>

               <td>
                    ACCOUNTANT
               </td>

               <td>
                   DAF
               </td>

               <td>
                   G. MANAGER
               </td>
           </tr>
       </table>
        <div class="clearfix"></div>
    </div>

</div>

@stop

