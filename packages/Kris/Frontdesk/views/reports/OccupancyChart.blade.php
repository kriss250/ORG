@extends("Frontdesk::MasterIframe")

@section("contents")

<script type="text/javascript">
    $(document).ready(function(){
                          
                     
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
            data: JSON.parse('{!!$data!!}')
        }]
    });
         })

        </script>
<div class="home-chart" style="min-width: 500px; height: 500px; margin: 0 auto" id="room-chart"></div>
@stop

