
@extends(isset($_GET['import']) ? "ORGFrontdesk.Reports.Master" : "Backoffice.Master")

@section("contents")
<style type="text/css">


</style>


<div class="page-contents">

<div class="report-filter">
<table style="width:100%">
    <tr>
        <td><h3>Room Sales Report </h3> </td>
        <td>
          <form style="float:right" action="{{URL::full()}}" class="form-inline" method="get">
                <label>Date</label> 
                <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control"> - 
                <input name="enddate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control">
               
                @if(isset($_GET['import']))
                    <input type="hidden" name="import" value="" />
                @endif
                <input type="submit" class="btn btn-success btn-sm" value="Go">
                 <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Room Sales Report" class="btn btn-default report-print-btn">Print</button>
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
    <br />
    <div class="row" style="padding-left:25px;padding-right:25px">
        <?php $peek = ["day"=>null,"sales"=>0]; $counter =0; $total = 0; foreach($data as $item) :
                  $counter ++;
              $total += $item->amount;
              $d = new \Carbon\Carbon($item->date);
              $peek = $peek['sales'] < $item->amount ? ["day"=>$d,"sales"=>$item->amount] : $peek;
        ?>

        <div style="border:1px solid;margin-right:-1px" class="col-xs-1 text-center">
            <b>{{$d->format("d")}}</b> <br />
            <b>({{ $item->sold }}) </b><br /> {{ number_format($item->amount) }}
        </div>

        <?php endforeach; ?>

        <div class="clearfix"></div>
        <br />
        <hr />
        <p>Peek : {{$peek['day']}} with {{number_format($peek['sales'])}}</p>
        <p>Average Daily Sales : {{$counter > 0  ? number_format($total/$counter) : 0 }} </p>
        <p style="font-size:22px">Total Sales : {{number_format($total)}}</p>
    </div>

<div class="text-center print-footer">
       <table style="margin-bottom:85px;width:100%;" class="table">
           <tr>
               <td>
                   CASHIER
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

