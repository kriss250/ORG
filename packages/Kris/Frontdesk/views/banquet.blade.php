@extends("Frontdesk::MasterIframe")

@section("contents")
<script>
    function deleteEntry(id)
    {
        if(confirm("Are you sure you want to delete this entry ?"))
        {
            $.ajax({
                url: '{{action("\Kris\Frontdesk\Controllers\OperationsController@deleteBanquetOrder")}}?id=' + id,
                type: "get",
                success:function(data)
                {
                    if(data=="1")
                    {
                        location.reload();
                    } else {
                        alert("Error deleting entry");
                    }
                }
            })
        }
    }
</script>
<div class="panel-desc">
    <p class="title">Banquet Booking</p>
    <p class="desc"></p>
</div>
<style>
    .b-booking-table td .dropdown-menu li {
        padding: 2px 10px;
        border-bottom: 1px solid rgb(230,230,230);
    }
</style>
<?php $bans = \Kris\Frontdesk\Banquet::all();

    $date1 = isset($_GET['startdate']) ? $_GET['startdate'] : \Kris\Frontdesk\Env::WD()->format("Y-m-d") ;
    $date2 = isset($_GET['enddate']) ? $_GET['enddate'] : \Kris\Frontdesk\Env::WD()->addDays(14)->format("Y-m-d") ;

      $startdate = new \Carbon\Carbon($date1);
      $enddate = new \Carbon\Carbon($date2);

      $count = $enddate->diff($startdate)->days+1;
      $booking = (new \Kris\Frontdesk\Banquet)->getBooking($startdate->format("Y-m-d"),$enddate->format("Y-m-d"));
      $hallsCounter = 0;
?>

<div class="list-filter">
    <?php $wd = \Kris\Frontdesk\Env::WD(); ?>
    <form action="" method="get" class="form-inline">
        

        <fieldset class="bordered">
            <label>From Date</label>
            <input type="text" name="startdate" value="{{$startdate->format("Y-m-d")}}" class="form-control datepicker" placeholder="From" />
        </fieldset>

        <fieldset class="bordered">
            <label>To Date</label>
            <input type="text" name="enddate" value="{{$enddate->format("Y-m-d")}}" class="form-control datepicker" placeholder="To" />
        </fieldset>

        <button style="margin-left:5px" title="Banquet Event" onclick="window.openDialog('{{action("\Kris\Frontdesk\Controllers\OperationsController@forms",'banquetEvent')}}','Banquet','width=410,height=350,resizable=no',this)" class="btn btn-primary btn-xs" type="button">Add Event</button>
        <input  type="submit" value="Find" class="btn btn-success btn-xs" />
    </form>

    <div class="clearfix"></div>
</div>

<div class="list-wrapper">
     <p class="list-wrapper-title">
        <span>Banquet Booking</span>
    </p>

<table class="table table-striped table-bordered table-condensed b-booking-table">
    <thead>
        <tr>
            <th>Date</th>
            @foreach($bans as $ban)
            <th>{{$ban->banquet_name}}</th>
            <?php  $hallsCounter++; ?>
            @endforeach
        </tr>
    </thead>
    <?php $date = $startdate; ?>
    @for($i=0;$i<$count;$i++)
    <tr>

        <td>
            
            {{$date->format("d/m/Y")}}
        </td>
        @foreach($bans as $ban)
        

        <td class="b_{{$ban->idbanquet}}">
            @if(isset($booking[$ban->banquet_name][$date->format("Y-m-d")]))
            <span data-toggle="dropdown" style="cursor:pointer;display:block" class="dropdown-toggle">{{ preg_replace("~[0-9](.*?)(\^)~","",$booking[$ban->banquet_name][$date->format("Y-m-d")]) }}</span>
            <ul class="dropdown-menu">
               @foreach(explode('~',$booking[$ban->banquet_name][$date->format("Y-m-d")]) as $item)
                   <li>
                      <span style="display:inline">{{explode('^',$item)[1]}}</span>  <a  style="display:inline;color:#c20303" onclick="deleteEntry({{explode('^',$item)[0]}})" class="text-danger" href="#"><i class="fa fa-trash"></i></a>
                   </li>
               @endforeach
            </ul>
            @endif
        </td>
       
        @endforeach
        <?php $date = $startdate->addDays(1); ?>
    </tr>
    @endfor
</table>
</div>
@stop