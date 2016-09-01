@extends("Backoffice.Master")

@section("contents")
<div class="page-contents">
<style>
@media print {
  .summary-block div {
    border:1px solid #000 !important;
  }
}
</style>
<div class="report-filter">
<table style="width:100%">
    <tr>
        <td><h3>Flash Activity</h3> </td>
        <td>
           <form style="float:right" action="" class="form-inline" method="get">
                <label>Date</label>
                <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control"> -
                <input name="enddate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control">

                <input type="submit" class="btn btn-success btn-sm" value="Go">
                 <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['enddate']) ? $_GET['enddate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Flash Activity" class="btn btn-default report-print-btn">Print</button>
           </form>
        </td>
    </tr>
    <tr>
       <td>
      <p class="text-danger"><b>Date : {{ isset($_GET['startdate']) && isset($_GET['enddate'])  ?  \App\FX::Date($_GET['startdate'])." - ".\App\FX::Date($_GET['enddate']) : \App\FX::Date(\ORG\Dates::$RESTODATE) }}</b></p>
      </td>
    </tr>
</table>


</div>

<script type="text/javascript">
$(function () {
  Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function (color) {
      return {
          radialGradient: {
              cx: 0.5,
              cy: 0.3,
              r: 0.7
          },
          stops: [
              [0, color],
              [1, Highcharts.Color(color).brighten(-0.3).get('rgb')] // darken
          ]
      };
  });

  // Build the chart
  $('#room-chart').highcharts({
      chart: {
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false,
          type: 'pie'
      },
      title: {
          text: 'Room Status '
      },
      tooltip: {
          pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
      },
       credits: {
          enabled: false
      },
      plotOptions: {
          pie: {
              allowPointSelect: true,
              cursor: 'pointer',
              dataLabels: {
                  enabled: true,
                  format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                  style: {
                      color:'black'
                  },
                  connectorColor: 'silver'
              }
          }
      },
      series: [{
          name: 'Percentage',
          data: JSON.parse('{!!$room_status_chart!!}')
      }]
  });
});
</script>

<style>
  .summary-block p {
    font-size:18px;font-weight:bold;
    margin-top: 8px;
  }

  .summary-block {
    padding: 10px 30px
  }

    .summary-block p sup {
      font-weight: normal;
    }

    .summary-block div {
      border:1px solid rgb(200,200,200);
      padding: 8px 15px;
      margin-right: -1px;
      font-size: 16px;
      background: rgb(250,250,250)
    }

</style>
<p class="text-center">
  <h3 class="text-center">Summary</h3>
  <span style="display:block" class="text-center">Sales</span>
</p>


<div class="row summary-block">
  <div class="col-xs-4">
    Frontdesk turnover
    <p>
     {{number_format($fo_turnover)}}
      <sup class="text-{{$fo_turnover_rate  > 0 ? "success" :'danger'}}">{{$fo_turnover_rate > 0 ? "+" : "-"}}{{number_format($fo_turnover_rate,1)}}%</sup>
    </p>
  </div>

  <div class="col-xs-4">
    POS turnover
    <p>
      {{number_format($pos_turnover)}}
      <sup class="text-{{$pos_turnover_rate > 0 ? "success" : "danger"}}">{{$pos_turnover_rate > 0 ? "+" : ""}}{{number_format($pos_turnover_rate,1)}}%</sup>
    </p>
  </div>

  <div class="col-xs-4">
    Average Room Rate
    <p>
      {{number_format($avg_rate)}}
      <sup class="text-{{$avg_rate_rate  > 0 ? "success" :'danger'}}">{{$avg_rate_rate > 0 ? "+" : ""}}{{number_format($avg_rate_rate,1)}}%</sup>
    </p>
  </div>
</div>
<hr />
<h3>POS Sales Summary</h3>
<span style="margin-top:-10px;display:block;margin-bottom:15px">Sales</span>

<table class="table table-striped table-bordered table-condensed">

  <thead>
    <tr>
      @foreach($pos_stores["stores"] as $store)
        <th>
          {{$store->store_name}}
        </th>
      @endforeach
    </tr>
  </thead>

  <tr>
    <?php $counter = 0;$total_pos_amount= 0; $stores_count = count($pos_stores["stores"]); ?>
    @foreach($pos_stores["stores"] as $store)

      @foreach($pos_stores['sales'] as $sale)
          @if($store->store_name == $sale->store_name)
            <?php $counter++; $total_pos_amount += $sale->amount; ?>
            <td>
                {{ number_format($sale->amount) }}
            </td>
          @endif
      @endforeach

    @endforeach

    {!! str_repeat("<td>0</td>", ($stores_count-$counter) )!!}
  </tr>

  <tr>
  <td colspan="{{$stores_count}}" class="text-center">
     <h4>GT : {{number_format($total_pos_amount)}}</h4>
  </td>
  </tr>
</table>
<h3>Overall Payment</h3>
<span style="margin-top:-10px;display:block;margin-bottom:15px">Payments receive (Frontdesk,POS,Invoices)</span>

<table class="table table-striped table-bordered table-condensed">
  <thead>
    <tr>
      <th>
        Cash
      </th>

      <th>
      CC
      </th>
<th>
  Check
</th>
      <th>
        Bank
      </th>
    </tr>
  </thead>


  <tfoot>
    <tr>
      <th class="text-center" style="font-weight:bold;font-size:16px !important" colspan="4">
        GT : 620,000
      </th>
    </tr>
  </tfoot>
</table>

<div id="room-chart"></div>
    <div class="text-center print-footer">
       <table style="margin-bottom:85px;width:100%;" class="table">
           <tr>
               <td>
                   {{\Auth::user()->username}}
               </td>

               <td>
                   C.F.O.O
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
