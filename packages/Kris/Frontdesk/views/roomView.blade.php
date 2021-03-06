@extends("Frontdesk::MasterIframe")

@section("contents")

<script>
    
    {{\Session::has('refresh') ? ' window.opener.autoRefresh = true;' : ''}}

    $(document).ready(function () {
        initSelectBoxes();
       $("[data-toggle='tooltip']").tooltip();
        $(".update-btn").click(function(e){
            e.preventDefault();
            $(this).append("...");
            $("[name='update-info']").submit();
        });

        $(".guest-info-dropdown").click(function(e){
            
            if(e.target.className.split(' ')[0]=="select-wrapper")
            {
                $(e.target).parent().find(".dropdown-menu").toggleClass("open");
            }

            e.stopPropagation();
        });

        $(".guest-info-dropdown-toggle").click(function(){
            $(".guest-info-dropdown > div").slimScroll({height:"142px"});
        });

    });


</script>

<style>
    .room-st {
        border: 1px solid rgb(188, 225, 179);
border-radius: 4px;
background: rgb(240, 255, 230) none repeat scroll 0% 0%
    }

    body {
        background:#e6eeff !important;
    }

    hr {
        margin-top:7px;
        margin-bottom:7px;
    }

    html > body.ifr-body {
        overflow:visible !important
    }
</style>
<?php if(!isset($res)) echo die("<center><h1 class='text-center'>ERROR !</h1></center>"); ?>
<div class="panel-desc">
    <p class="title">Room View : {{$res->room->room_number}} - Rent ID # {{$res->idreservation}}</p>
    <p class="desc"></p>
</div>

<div style="padding-top:5px;" class="room-view-form">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#1" data-toggle="tab">General</a>
        </li>
        <li>
            <a href="#2" data-toggle="tab">Payments</a>
        </li>

        <li>
            <a href="#3" data-toggle="tab">Room</a>
        </li>

        <!--<li>
            <a href="#4" data-toggle="tab">Charges</a>
        </li>-->
    </ul>

    <div class="tab-content ">
        <div class="tab-pane active" id="1">
            <div style="margin-top:-10px;" class="row">

                <form name="update-info" action="{{action("\Kris\Frontdesk\Controllers\ReservationsController@update",$res->idreservation)}}" method="post">
                    <input type="hidden" name="_token" value="{{csrf_token()}}" />
                    <input type="hidden" name="_id" value="{{$res->idreservation}}" />
                    <div class="col-xs-4">
                        <p class="section-title">
                            <span>Stay information</span>
                        </p>
                        <?php
                    $checkin = (new \Carbon\Carbon($res->checkin))->format("Y-m-d");
                    $checkout = (new \Carbon\Carbon($res->checkout))->format("Y-m-d");
                        ?>
                        <fieldset class="readonly">
                            <label>Checkin</label>
                            <input type="text" {{$res->status == \Kris\Frontdesk\Reservation::ACTIVE ? "" :"readonly"}} name="checkin" value="{{$checkin}}" class="form-control datepicker" placeholder="Names of the guest" />
                        </fieldset>

                        <fieldset class="bordered">
                            <i class="fa fa-calendar"></i>
                            <label>Checkout</label>
                            <input type="text" name="checkout" value="{{$checkout}}" class="form-control datepicker" placeholder="" />
                        </fieldset>

                        <fieldset style="width:60px;display:table;float:left">
                            <label>Adults</label>
                            <input name="adults" type="number" min="1" value="{{$res->adults}}" placeholder="#" />
                        </fieldset>

                        <fieldset style="width:70px;display:table;float:right">
                            <label>Children</label>
                            <input name="children" type="number" value="{{$res->children}}" min="0" max="20" placeholder="#" />
                        </fieldset>

                        <div class="clearfix"></div>



                        <p class="section-title">
                            <span>Rate & Payment information</span>
                        </p>

                        <fieldset>
                            <label>Night Rate</label>
                            <div class="select-wrapper">
                                <i class="fa fa-angle-down"></i>
                                <select name="rate_type">
                                    @foreach(Kris\Frontdesk\RateType::all() as $rtype)
                                    <option value="{{$rtype->idrate_types}}">
                                        {{$rtype->name}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <input class="text-primary text-bold" autocomplete="off" value="{{$res->night_rate }}" type="text" name="rate" placeholder="#" />
                        </fieldset>
                        <fieldset>
                            <label>Package</label>
                            <span>BB</span>
                            <input title="Bed and Breakfast" name="package" value="BB" {{$res->package_name=="BB" ? "checked" : ""}} type="radio" />
                            <span>HB</span>
                            <input {{$res->package_name=="HB" ? "checked" : ""}} name="package" value="HB" type="radio" />
                            <span>FB</span>
                            <input {{$res->package_name=="FB" ? "checked" : ""}} name="package" value="FB" type="radio" />

                            <span>CR</span>
                            <input {{$res->package_name=="CR" ? "checked" : ""}} name="package" value="CR" type="radio" />
                            <div class="clearfix"></div>
                        </fieldset>

                    </div>

                    <div class="col-xs-4" style="padding:0">
                        <p class="section-title">
                            <span>Guest information</span>
                        </p>
                        <div class="light-fieldsets">
                            <fieldset>
                                <label>Guest Names</label>
                                <input name="names" autocomplete="off" value="{{$res->guest !=null  ? $res->guest->firstname : ""}} {{$res->guest !=null  ? $res->guest->lastname : ""}}" type="text" placeholder="Firstname & Lastname" />
                            </fieldset>
                            <fieldset>
                                <label>Country</label>
                                <div class="select-wrapper">
                                    <i class="fa fa-angle-down"></i>
                                    <select name="country">
                                        <option value="">Choose Country</option>
                                        @foreach(Kris\Frontdesk\Countries::$list as $country)
                                        <option {{$res->guest !=null && $country == $res->guest->country ? " selected " :""}}>
                                            {{$country}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </fieldset>
                           
                            

                            <fieldset>
                                <label>Company</label>
                                <input value="{{isset($res->company) ? $res->company->name:""}}" autocomplete="off" type="text" name="company" placeholder="Company name" />
                            </fieldset>
                            <button class="guest-info-dropdown-toggle" type="button" data-toggle="dropdown" style="padding: 0px 15px;
border: 1px solid rgb(213, 213, 213);
background: rgb(255, 255, 255) none repeat scroll 0% 0%;
margin: 5px auto -3px;
display: block;">
                                <i class="fa fa-angle-double-down" aria-hidden="true"></i>
                            </button>
                            <ul class="dropdown-menu guest-info-dropdown">

                                <div>

                                    <li>
                                        <fieldset>
                                            <label>Phone</label>
                                            <input autocomplete="off" value="{{$res->guest !=null ? $res->guest->phone : ""}}" type="text" name="phone" placeholder="Phone Number" />
                                        </fieldset>
                                     
                                    </li>


                                    <li>
                                        <fieldset>
                                            <label>Email</label>
                                            <input type="text" value="{{$res->guest !=null ? $res->guest->email :""}}" name="email" placeholder="@email" />
                                        </fieldset>
                                    </li>

                                    <li>
                                        <fieldset>
                                            <label>City</label>
                                            <input type="text" value="{{$res->guest !=null ? $res->guest->city : ""}}" name="city" placeholder="Address" />
                                        </fieldset>
                                    </li>


                                    <li>
                                        <fieldset>
                                            <label>ID / Passport #</label>
                                            <input value="{{$res->guest !=null ? $res->guest->id_doc:""}}" type="text" name="id_doc" placeholder="ID#" />
                                        </fieldset>
                                    </li>

                                   
                                </div>
                            </ul>
                        </div>
                        <?php
                    $status_text = "N/A";
                    switch($res->status)
                    {
                        case \Kris\Frontdesk\Reservation::ACTIVE :
                            $status_text = "Reserved";
                            break;
                        case \Kris\Frontdesk\Reservation::CHECKEDIN:
                            $status_text = "Occupied";
                            break;
                        case \Kris\Frontdesk\Reservation::NOSHOW:
                            $status_text = "No show";
                            break;
                        case \Kris\Frontdesk\Reservation::CHECKEDOUT:
                            $status_text = "Checked out";
                            break;
                        case \Kris\Frontdesk\Reservation::CANCELLED:
                            $status_text = "Cancelled";
                            break;
                        default:
                            $status_text = "N/A";
                            break;
                    }
                    $paymodes = Kris\Frontdesk\PayMethod::all();
                    $currencies = Kris\Frontdesk\Currency::all();
                        ?>
                        <div class="room-st text-center {{strtolower($status_text)}}">
                            <h4>{{$status_text}}</h4>
                        </div>
                        <hr />
                        <fieldset>
                            <label>Mode Of Payment</label>
                            <span>Credit</span> <input {{$res->pay_by_credit=="1" ? "checked" : ""}} name="mode" value="1" type="radio" /> <span>Payment</span> <input value="0" name="mode" {{$res->pay_by_credit=="0" ? "checked" : ""}}  type="radio" />
                            <div class="clearfix"></div>
                            <div class="select-wrapper">
                                <i class="fa fa-angle-down"></i>
                                <select name="pay_method">
                                    <option value="">Choose Method</option>
                                    @foreach($paymodes as $mode)
                                    <option {{$res->prefered_pay_mode==$mode->idpay_method ? "selected" : ""}}  value="{{$mode->idpay_method}}">
                                        {{$mode->method_name}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </fieldset>
                    </div>
                </form>

                <div class="col-xs-4">
                    <div style="background:#f3ffe9;padding:6px;margin-top: -6px;border:1px solid rgb(230, 239, 222);border-top:none">

                        <p class="section-title">
                            <span style="background:rgb(243, 255, 233)">Room Charges</span>
                        </p>
                        <div style="margin-left:-6px;margin-right:-6px;">
                            <div class="charges-table-wrapper" style="height:182px;">
                                <table class="table charges-sm-table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Charge</th>
                                            <th>Amount</th>
                                           
                                        </tr>
                                    </thead>
                                    <?php $min = count($res->charge) > 8 ? count($res->charge) : 8; ?>
                                    @for($i=0;$i<$min;$i++)
                                <tr>
                                    <td>{{isset($res->charge{$i}) ?  $res->charge{$i}->motif : ""}}</td>
                                    <td>{{isset($res->charge{$i}) ? number_format($res->charge{$i}->amount):""}}</td>
                                 
                                </tr>
                                @endfor
                                </table>
                            </div>
                        </div>
                        <div style="font-size:12px; border-bottom:1px solid rgb(209, 237, 203);border-top:1px solid rgb(209, 237, 203); background:#fff;padding:8px 18px;margin-left:-6px;margin-right:-6px">
                            <p style="color:#a5a5a5">Summary</p>
                           <div class="text-right">
                               Amount due : {{number_format($res->due_amount)}} <br />
                               Amount Paid : {{number_format($res->paid_amount)}}
                               <hr />
                               Balance : {{number_format($res->due_amount-$res->paid_amount)}}
                           </div>
                        </div>

                        <div class="clearfix"></div>

                        <a style="font-size:12px;margin-top:10px;border-bottom:1px dotted; text-decoration:dotted;display:table;float:right" class="text-right" href="">Full Statement</a>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane" id="2">
            <div class="new-pay-panel">
                <div class="row light-fieldsets" style="border-left:none;border-right:none;margin-top:-5px;border-radius:0">
                    <div class="col-xs-12 inline-fieldsets">
                        <label>New Payment</label>
                       
                        <br />
                        <form method="post" action="{{action('\Kris\Frontdesk\Controllers\ReservationsController@addPayment',$res->idreservation)}}">
                            <input type="hidden" name="_token" value="{{csrf_token()}}" />

                            <fieldset style="width:20.4%;">
                                <label>Mode Of Payment</label>
                                <div class="clearfix"></div>

                                <div class="select-wrapper">
                                    <i class="fa fa-angle-down"></i>
                                    <select name="pay_method">
                                        <option value="">Choose Method</option>
                                        @foreach($paymodes as $mode)
                                        <option value="{{$mode->idpay_method}}">
                                            {{$mode->method_name}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </fieldset>

                            <fieldset style="width:18%;">
                                <label>Currency <i class="fa fa-usd"></i></label>
                                <div class="clearfix"></div>

                                <div class="select-wrapper">
                                    <i class="fa fa-angle-down"></i>
                                    <select required name="currency">
                                        <option value="">Currency </option>
                                        @foreach($currencies as $currency)
                                        <option value="{{$currency->idcurrency}}">
                                            {{$currency->alias}}
                                        </option>
                                        @endforeach
                                    
                                    </select>
                                </div>
                            </fieldset>

                            <fieldset class="bordered" style="width:16%">
                                <label>Amount #</label>
                                <input required value="" type="text" name="amount" placeholder="#" />
                            </fieldset>

                            <fieldset style="width:41.5%;">
                                <label>Description</label>
                                <input style="margin-top:6px;padding:4px 5px !important;border-top:1px dashed rgb(215, 215, 215) !important" value="" type="text" name="motif" placeholder="Specify reason for the payment" />
                            </fieldset>
<br />
                            <div class="row">
                                <div class="col-xs-6">

                                    <button style="padding: 3px 13px;font-size: 11px;text-transform: uppercase;font-weight: bold" class="btn btn-success">
                                        <i class="fa fa-plus"></i> Add Payment
                                    </button>

                                </div>

                                <div class="col-xs-6 text-right">
                                    <button type="button" onclick="window.open('{{action("\Kris\Frontdesk\Controllers\OperationsController@forms","makeRefund")}}?id={{$res->idreservation}}','','width=400,height=280')" style="margin-right: 12px;border: none;background: none;color: #d43f3a;text-decoration: underline;text-transform: uppercase;font-size: 11px;" class="btn-danger btn-xs"><i class="fa fa-ban"></i> Refund</button>
                                </div>
                            </div>
                        </form>

                    </div>

             
                </div>

                <div style="max-height:250px;overflow-y:auto">
                    <table class="table table-condensed table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Description</th>
                                <th>Mode</th>
                                <th>User</th>
                                <th><i class="fa fa-trash"></i></th>
                            </tr>
                        </thead>
                       
                        @if($res->payments == null)
                        <tr>
                            <td colspan="5">No Payments made yet !</td>
                        </tr>
                        @else
                        <?php $i =1; ?>
                        @foreach($res->payments as $payment)
                        @if($payment->void ==0)
                        <tr>
                            <td>{{ (new \Carbon\Carbon($payment->date))->format("d/m/Y H:i:s") }}</td>
                            <td>
                               @if($payment->debit > 0)
                                <em class="text-danger">-{{$payment->debit}}</em>
                                @endif

                                @if($payment->credit > 0 && $payment->debit==0)
                                {{number_format($payment->credit)}} <span style="font-size:11px">{{$payment->currency->rate != 1 ? "({$payment->original_amount} {$payment->currency->alias})" :""}} </span>
                                @endif

                                @if($payment->credit < 0 && $payment->debit==0)
                                {{$payment->credit}}
                                @endif

                            </td>
                            <td width="45%">{{$payment->motif}}</td>
                            <td>{{$payment->debit > 0 ? "-" : $payment->mode->method_name}}</td>
                            <td>{{$payment->user->username}}</td>
                            <td style="font-size:14px">
                                <a title="Delete Payment" class="confirm-btn" data-toggle="tooltip" style="color:#d43f3a;margin-right:6px" href="{{action("\Kris\Frontdesk\Controllers\OperationsController@removePayment",$payment->id_folio)}}"><i class="fa fa-trash"></i></a>
                                <a title="Print" data-toggle="tooltip" onclick="openWindow('printReceipt/{{$payment->id_folio}}',this,'Print Receipt',970,580);" href="#"><i class="fa fa-print"></i></a>
                            </td>

                        </tr>
                        <?php $i++; ?>
                        @endif
                        @endforeach
                        <?php
                            if($i<8)
                            {
                                echo str_repeat("<tr><td>.</td> <td></td> <td></td> <td></td> <td></td> <td></td></tr>",8-$i);
                            }
                        ?>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane" id="3">
            
           <div class="row">
               <div class="col-xs-7">
                   <p class="section-title">
                       <span>Room information</span>
                   </p>
                   <div style="background: rgb(245, 255, 255) none repeat scroll 0% 0%;padding: 12px;border: 1px solid rgb(219, 242, 242)">
                       <!--<h4 style="margin-top:0">Room : {{$res->room->room_number}}</h4>
                       <h5>Room Type: {{$res->room->room_number}}</h5>
                       <p>Current Rate : {{$res->night_rate}}</p>
                       <p>Rent ID # : {{$res->idreservation}}</p>
                       <hr />

                       <p>Checked in by : {{$res->checkedin_by}}</p>
                       <p>Checked out by : {{$res->checkedout_by}}</p>-->
                       <p><b>Guests in Room</b> <button onclick="toggleRoomSharingForm();" type="button" class="btn btn-xs btn-default pull-right">Add Guest</button> </p>
                       <div class="room-sharer-form">
                           <form method="post">
                           <label>Names</label>
                           <input type="text" class="form-control" required placeholder="Firstname Lastname" />
                           <label>ID/Passport</label>
                           <input type="text" class="form-control" placeholder="#" required />

                           <label>Checkin & Checkout</label>
                           <div class="container-flui">
                               <div class="pull-left input-group col-xs-5">
                                   <input value="{{date("Y-m-d")}}" class="form-control datepicker input-sm" placeholder="Checkin" />
                                   <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                               </div>

                               <div class="pull-right input-group col-xs-5">
                                   <input class="form-control datepicker input-sm" placeholder="Checkout" />
                                   <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                               </div>
                           </div>
                               <p class="clearfix"></p>
                               <br />
                               <input class="btn btn-sm btn-danger" type="submit" value="Add Guest" />
                               </form>
                       </div>
                       <table style="background:#fff" class="table table-striped table-condensed table-bordered">
                           <thead>
                               <tr>
                                   <th>Guest Names</th>
                                   <th>Checkin</th>
                                   <th>Checkout</th>
                                   <th>#</th>
                               </tr>
                           </thead>
                           <tr>
                               <td>{{$res->guest !=null  ? $res->guest->firstname : ""}} {{$res->guest !=null  ? $res->guest->lastname : ""}}</td>
                               <td>{{$checkin}}</td>
                               <td>{{$checkout}}</td>
                               <td>-</td>
                           </tr>

                           @for($i=0;$i<4;$i++)
                            <tr>
                               <td>.</td>
                               <td></td>
                               <td></td>
                               <td></td>
                           </tr>
                           @endfor
                       </table>
                   </div>
               </div>

               <div style="background:none;border:none" class="col-xs-5 light-fieldsets">
                   <p class="section-title">
                       <span>Room Operations</span>
                   </p>
                   <form method="post" action="{{action('\Kris\Frontdesk\Controllers\ReservationsController@shiftRoom',$res->idreservation)}}">
                       <h6>Room shift</h6>
                       <fieldset class="bordered">
                           <label>To Room</label>
                           <input name="new_room" type="text" placeholder="#" />
                       </fieldset>
                       <input type="hidden" name="_token" value="{{csrf_token()}}" />
                       <input type="hidden" name="prev_room_number" value="{{$res->room->room_number}}" />
                       <fieldset class="bordered">
                           <label>New Rate</label>
                           <input name="new_rate" value="{{$res->night_rate}}" type="text" placeholder="Rate for the new room" />
                       </fieldset>

                       <fieldset class="bordered">
                           <label>Reason</label>
                           <input name="motif" type="text" placeholder="Enter Reason" />
                       </fieldset>
                       <hr />
                       <button type="submit" class="btn btn-warning">Shift Guest</button>
                   </form>
               </div>
           </div>

        </div>

        <div class="tab-pane" id="4">
            <h5>Charges</h5>
        </div>
    </div>
</div>

<footer>
    <div class="row">
        <div class="col-xs-7">
            @if(\Kris\Frontdesk\User::session()->group_id !=\Kris\Frontdesk\UserGroup::HOUSEKEEPER)
            <form class="form-btn" method="get" action="{{action("\Kris\Frontdesk\Controllers\ReservationsController@checkin",[$res->idreservation,$res->room_id])}}">
                <button type="submit" {{$res->status==\Kris\Frontdesk\Reservation::ACTIVE ? "" : "disabled"}} class="btn btn-success">Checkin</button>
            </form>
            <form class="form-btn confirm-form-submit" data-msg="Are you sure you want to check out this guest ?"  method="get" action="{{action("\Kris\Frontdesk\Controllers\ReservationsController@checkout",[$res->idreservation,$res->room_id])}}">
                <button {{$res->status==\Kris\Frontdesk\Reservation::CHECKEDIN ? "" : "disabled"}} class="btn btn-danger">Checkout</button>
             </form>
            @endif
                @if($res->status==\Kris\Frontdesk\Reservation::ACTIVE  || $res->status==\Kris\Frontdesk\Reservation::CHECKEDIN)
                <button type="button" onclick="window.open('{{action("\Kris\Frontdesk\Controllers\OperationsController@forms","addCharge")}}?id={{$res->idreservation}}','','width=400,height=320')" style="font-size:12px" class="btn btn-default"><i class="fa fa-plus"></i> Add Charge</button>
                <button  style="font-size:12px" class="btn btn-default update-btn"><i class="fa fa-save"></i> Update</button>
                @endif
</div>

        <div class="col-xs-5">
            <div style="font-size:12px !important" class="btn-group">
                <button onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\OperationsController@_print",'bill')}}?id={{$res->idreservation}}&type=standard','','width=920,height=620',this)" type="button" style="font-size:12px" class="btn btn-default"><i class="fa fa-print"></i> Print</button>
                <button type="button" style="font-size:12px" data-placement="top" class="pop-toggle btn btn-default" aria-haspopup="true" aria-expanded="false">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu">
                    <li><a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\OperationsController@_print",'bill')}}?id={{$res->idreservation}}&type=standard','','width=920,height=620',this)" href="#">Standard Bill</a></li>

                    <li><a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\OperationsController@_print",'bill')}}?id={{$res->idreservation}}&type=payments','','width=920,height=620',this)" href="#">With Payments</a></li>
                    <li><a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\OperationsController@_print",'bill')}}?id={{$res->idreservation}}&type=accomodation','','width=920,height=620',this)" href="#">Accomodation</a></li>
                    <li><a onclick="openDialog('{{action("\Kris\Frontdesk\Controllers\OperationsController@_print",'bill')}}?id={{$res->idreservation}}&type=services','','width=920,height=620',this)" href="#">Services</a></li>
                </ul>
            </div>

            
            <div class="btn-group">
                <button style="font-size:12px" type="button" class="btn btn-default"><i class="fa fa-sliders"></i> Options</button>
                <button {{$res->status!="1"? "disabled" :""}} style="font-size:12px" data-placement="top" type="button" class="pop-toggle btn btn-default" aria-haspopup="true" aria-expanded="false">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                @if(\Kris\Frontdesk\User::session()->group_id !=\Kris\Frontdesk\UserGroup::HOUSEKEEPER)
                <ul class="dropdown-menu">
                    <li><a href="{{action("\Kris\Frontdesk\Controllers\ReservationsController@cancel",$res->idreservation)}}">Cancel Reservation</a></li>
                    <li><a href="{{action("\Kris\Frontdesk\Controllers\ReservationsController@noshow",$res->idreservation)}}">Mark as no show</a></li>
                </ul>
                @endif
            </div>
        </div>
    </div>

</footer>

<script>
    $(document).ready(function(){
    @if(session('msg'))
        alert("{{session('msg')}}");
    @elseif(session('errors'))
        error('{{session("errors")->first()}}');
    @endif

    });

    function toggleRoomSharingForm()
    {
        $(".room-sharer-form").toggle();
    }
</script>
@stop