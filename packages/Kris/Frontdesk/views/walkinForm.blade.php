@extends("Frontdesk::MasterIframe")

@section("contents")

<div class="panel-desc">
    <p class="title">Guest Walkin</p>
    <p class="desc">Guest without reservation</p>
</div>
<script type="text/javascript">
    initSelectBoxes();

    $(document).ready(function () {

        $("#walkin-form").submit(function (e) {
            e.preventDefault();
            var form = $(this);
           $.ajax({
                url : $(form).attr("action"),
                type : $(form).attr("method"),
                data:$(form).serialize(),
                success: function(data)
                {
                    jsData  = JSON.parse(data);
                    if(jsData.errors.length>0)
                    {
                        alert(jsData.errors[0]);
                    }else if(jsData.msg.length > 1) {

                          alert(jsData.msg);
                         window.opener.autoRefresh = true;
                          window.close();
                    }
                },
                error:function()
                {
                    alert("An error occured");
                }
            })
        });

        $("input[name=room]").change(function () {
            var room = $(this).parent().parent();

            $("input[name=rate]").val($(room).attr("data-rate"));
        })

        $(".rooms-table-wrapper").slimscroll({
            height: "156px",
            wheelStep: 5,
            distance: '2px',
            railVisible: true
        });

        $("body").on("blur", "input[name=company]", function () {

            $(this).parent().find(".suggestions").remove();
        })

        $("body").on("keyup", "input[name=company]", function () {
            if($(this).val().length < 2)
            {
                return;
            }


            var _suggestionList = $(this).parent().find(".suggestions");
            if (_suggestionList.length == 0)
            {
                var suggestionList = $("<ul class='suggestions'>");
                $(this).parent().append(suggestionList);
            } else {
                var suggestionList = _suggestionList;
                $(suggestionList).html("");
            }

            SearchCompany($(this).val(), '{{action("\Kris\Frontdesk\Controllers\OperationsController@findCompany")}}', $(suggestionList));


        })
    })
</script>

<div style="padding:6px 10px;padding-bottom:2px" class="row">

    <form id="walkin-form" action="{{action("\Kris\Frontdesk\Controllers\ReservationsController@walkin")}}" method="post">
        <div class="col-xs-4">
            <p class="section-title">
                <span>Stay information</span>
            </p>
            <input type="hidden" name="_token" value="{{csrf_token()}}" />
            <fieldset>
                <label>Checkin</label>
                <input name="checkin" autocomplete="off" readonly type="text" value="{{\Kris\Frontdesk\Env::WD()->format("Y-m-d")}}" placeholder="YYYY-MM-DD" />
            </fieldset>

            <fieldset>
                <label>Checkout</label>
                <input name="checkout" autocomplete="off" class="datepicker" type="text" placeholder="YYYY-MM-DD" />
            </fieldset>


            <fieldset style="width:60px;display:table;float:left">
                <label>Adults</label>
                <input name="adults" type="number" min="1" value="1" placeholder="#" />
            </fieldset>

            <fieldset style="width:60px;display:table;float:right">
                <label>Children</label>
                <input name="children" type="number" value="0" min="0" max="20" placeholder="#" />
            </fieldset>

            <div class="clearfix"></div>

            <fieldset>
                <label>Package</label>
                <span>BB</span> <input name="package" value="BB" checked type="radio" />
                <span>HB</span> <input name="package" value="HB" type="radio" />
                <span>FB</span> <input name="package" value="FB" type="radio" />
                <div class="clearfix"></div>
            </fieldset>

            <p class="section-title">
                <span>Company Information</span>
            </p>

            <fieldset>
                <label>Company</label>
                <input type="text" autocomplete="off" name="company" placeholder="Company / Organisation" />
            </fieldset>

            <fieldset>
                <label>Business Source</label>
                <input autocomplete="off" name="business_source" type="text" placeholder="Business Agency.." />
            </fieldset>

        </div>

        <div class="col-xs-4" style="padding:0">

            <p class="section-title">
                <span>Choose a room</span>
            </p>
            <div class="rooms-table-wrapper">
                <table class="walkin-room-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Room</th>
                            <th>Room Type</th>
                            <th>Floor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(Kris\Frontdesk\Room::vacant() as $room)
                        <tr data-rate="{{$room->rate_amount}}">
                            <td><input value="{{$room->idrooms}}" type="radio" name="room" /> </td>
                            <td>
                                {{$room->room_number}}
                            </td>

                            <td>
                                {{$room->type_name}}
                            </td>

                            <td>
                                {{    $room->floor_name}}
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
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
                            {{    $rtype->name}}
                        </option>
                        @endforeach
                    </select>
                </div>
                <input autocomplete="off" type="text" name="rate" placeholder="#" />
            </fieldset>

           
            <div class="clearfix"></div>
           
            <fieldset>
                <label>Mode Of Payment</label>
                <span>Credit</span> <input name="mode" value="1" type="radio" /> <span>Payment</span> <input value="0" name="mode" checked type="radio" />
                <div class="clearfix"></div>
                <div class="select-wrapper">
                    <i class="fa fa-angle-down"></i>
                    <select name="pay_method">
                        <option value="">Choose Method</option>
                        @foreach(Kris\Frontdesk\PayMethod::all() as $mode)
                        <option value="{{$mode->idpay_method}}">
                            {{    $mode->method_name}}
                        </option>
                        @endforeach
                    </select>
                </div>
            </fieldset>

<p class="text-center">
                <img src="/images/frontdesk/card-visa.svg" width="24" />
            <img src="/images/frontdesk/card-mastercard.svg" width="24" />
            <img src="/images/frontdesk/card-front.svg" width="24" />
           </p>

        </div>

        <div class="col-xs-4">
            <p class="section-title">
                <span>Guest information</span>
            </p>
            <p style="margin-bottom:0;margin-top:-6px;" class="row">
                <span class="col-xs-6">
                    <button class="swipe-btn"><i class="fa fa-credit-card"></i> Swipe</button>
                </span>
                <span class="col-xs-6">
                    <button style="float:right" class="existing-guest-btn"><i class="fa fa-user"></i> Existing</button>
                </span>
            </p>

            <input type="text" class="card-info" value="" />
            <fieldset style="margin-top:-12px;">
                <label>Firstname</label>
                <input autocomplete="off" type="text" name="firstname" placeholder="Given name" />
            </fieldset>

            <fieldset>
                <label>Lastname</label>
                <input autocomplete="off" type="text" name="lastname" placeholder="Family name" />
            </fieldset>

            <fieldset>
                <label>Birthdate</label>
                <input type="text" name="birthdate" placeholder="YYYY-MM-DD" />
            </fieldset>

            <fieldset>
                <label>Phone</label>
                <input type="text" name="phone" placeholder="Phone #" />
            </fieldset>

            <fieldset>
                <label>Email</label>
                <input type="text" name="email" placeholder="Email @" />
            </fieldset>
            <fieldset>
                <label>Country</label>
                <div class="select-wrapper">
                    <i class="fa fa-angle-down"></i>
                    <select name="country">
                        <option value="">Choose Country</option>
                        @foreach(Kris\Frontdesk\Countries::$list as $country)
                        <option>
                            {{$country}}
                        </option>
                        @endforeach
                    </select>
                </div>
            </fieldset>

             <fieldset>
                <label>ID / Passport</label>
                <input type="text" name="passport" placeholder="#Pass" />
            </fieldset>



        </div>


        <div class="clearfix"></div>
        <footer class="m-footer">
            <button class="btn btn-success">Checkin</button>
        </footer>

    </form>
</div>

@stop