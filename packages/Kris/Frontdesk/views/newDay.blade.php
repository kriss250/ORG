@extends("Frontdesk::MasterIframe")

@section("contents")

<style>
    .list-wrapper {
        min-height:308px;
    }
</style>
<div class="panel-desc">
    <p class="title">New Day Process</p>
    <p class="desc">Making a new Day</p>
</div>
<script>
    $(document).ready(function(){
        if($(".list-wrapper.scrollable").height() > 317){
            $(".list-wrapper.scrollable").slimScroll({
                height:"318px"
            });
        }

        $("#date-today").datepicker();
        
    })
    
</script>
<div style="padding:15px;">
    <?php $date = \Kris\Frontdesk\Env::WD();

    ?>

<p class="section-title">
    <span>Day Summary</span>
</p>

   <div class="row">
       <div style="padding:0" class="col-xs-4">
           <div class="list-wrapper scrollable">
               <p class="list-wrapper-title">
                   <span>Room Charges</span>
               </p>

               <table class="table table-bordered table-striped table-condensed">
                   <thead>
                       <tr>
                           <th>Room</th>
                           <th>Descr.</th>
                           <th>Amount</th>
                       </tr>
                   </thead>
                   <?php $charges = \Kris\Frontdesk\Charge::where(\DB::raw("date(date)"),$date->format("Y-m-d"))->get() ; ?>
                   @if($charges != null)
                       @foreach($charges as $charge)
                           <tr>
                               <td>{{$charge->room->room_number}}</td>
                               <td>{{$charge->motif}}.</td>
                               <td>{{    number_format($charge->amount)}}</td>
                           </tr>    
                       @endforeach

                   @else 

                   <tr>
                       <td colspan="3">
                           No Room charges available
                       </td>
                   </tr>
                   @endif
               </table>
           </div>
       </div>

       <div style="padding:0" class="col-xs-4">
           <div class="list-wrapper" style="height: 100px !important;min-height: 50px;">
               <p class="list-wrapper-title">
                   <span>Occupancy Summary</span>
               </p>
               <div style="padding:6px;background:#f3f3f3">
                   <p>Guest inhouse : {{\Kris\Frontdesk\Room::where("status","2")->count()}}</p>
                   <p>Departures : {{\Kris\Frontdesk\Reservation::where("status",6)->where(\DB::raw("date(checked_out)"),$date->format("Y-m-d"))->count() }}</p>
                   <p>Arrivals : {{\Kris\Frontdesk\Reservation::where("status",5)->where(\DB::raw("date(checked_in)"),$date->format("Y-m-d"))->count() }} </p>
               </div>
           </div>

           <div class="list-wrapper" style="height: 220px !important;font-size:12px; min-height: 50px;">
               <p class="list-wrapper-title">
                   <span>Calendar</span>
               </p>
               <div id="date-today" data-date="{{$date->format("Y-m-d")}}" data-date-format="yyyy-mm-dd"></div>
           </div>
       </div>

       <div style="padding:0" class="col-xs-4">
           <div class="list-wrapper scrollable">
               <p class="list-wrapper-title">
                   <span>Upcoming Reservations</span>
               </p>

               <table class="table table-bordered table-condensed table-striped">
                   <thead>
                       <tr>
                           <th>Room</th>
                           <th>Guest</th>
                       </tr>
                   </thead>

                   @foreach(\Kris\Frontdesk\Reservation::where("status",1)->where(\DB::raw("date(checkin)"),$date->addDay()->format("Y-m-d"))->get() as $res)
                   <tr>
                       <td>{{$res->room->room_number}}</td>
                       <?php $g = $res->guest;?>
                       <td>{{$g->firstname}} {{$g->lastname}}</td>
                   </tr>
                   @endforeach
               </table>
           </div>
       </div>

   </div>
<footer>
<p>Make sure you check all room rates , and the total number of guests in house before making a new  day.</p>
<form method="post" action="{{action("\Kris\Frontdesk\Controllers\OperationsController@frame","newDay")}}">
    <input type="hidden" name="_token" value="{{csrf_token()}}" />

    <input class="btn btn-danger" type="submit" value="New Day" />
</form>
    </footer>

<script>
    {{\Session::has('refresh') ? ' window.opener.autoRefresh = true;' : ''}}
    $(document).ready(function(){
    @if(session('msg'))
        alert("{{session('msg')}}");
    @elseif(session('errors'))
        error('{{session("errors")->first()}}');
    @endif
    });

</script>
    </div>
@stop