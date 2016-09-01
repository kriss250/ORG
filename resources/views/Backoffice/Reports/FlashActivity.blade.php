@extends("Backoffice.Master")

@section("contents")
<div class="page-contents">

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

<div id="room-chart">

</div>
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
