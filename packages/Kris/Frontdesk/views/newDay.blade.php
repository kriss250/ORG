@extends("Frontdesk::MasterIframe")

@section("contents")

<div class="panel-desc">
    <p class="title">New Day Process</p>
    <p class="desc">Guest without reservation</p>
</div>

<div style="padding:15px;">
    <?php $date = \Kris\Frontdesk\Env::WD(); ?>
Current Date {{$date->format("d/m/Y")}}
<p class="section-title">
    <span>Day Summary</span>
</p>

   <div class="row">
       <div style="padding:0" class="col-xs-4">
           <div class="list-wrapper">
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
                               <td>{{number_format($charge->amount)}}</td>
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
               <div style="padding:6px;">
                   <p>Guest inhouse : 8</p>
                   <p>Departures : 8</p>
                   <p>Arrivals : 8</p>
               </div>
           </div>

           <div class="list-wrapper" style="height: 100px !important;min-height: 50px;">
               <p class="list-wrapper-title">
                   <span>Occupancy Summary</span>
               </p>
               <div style="padding:6px;">
                   <p>Guest inhouse : 8</p>
                   <p>Departures : 8</p>
                   <p>Arrivals : 8</p>
               </div>
           </div>
       </div>

       <div style="padding:0" class="col-xs-4">
           <div class="list-wrapper">
               <p class="list-wrapper-title">
                   <span>Upcoming Reservations</span>
               </p>
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