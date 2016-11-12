@extends("Frontdesk::MasterIframe")

@section("contents")

<div class="panel-desc">
    <p class="title">Group Booking</p>
    <p class="desc"></p>
</div>
<script type="text/javascript">
    initSelectBoxes();

    $(document).ready(function () {

        $(".walkin-room-table").on("change",".room_tick",function(e){
            $(this).parent().parent().toggleClass("selected_room");
        });
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
                          location.reload();
                    }
                },
                error:function()
                {
                    alert("An error occured");
                }
            })
        });

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

<style>
    .panel-desc {
        display: block;
        background: rgb(137, 222, 135);
        padding: 8px 18px;
        font-size: 11px;
        position: relative;
        border-bottom: 1px solid rgb(122, 206, 109);
        color: rgb(78, 78, 78);
    }
</style>

<script>
    function getAvailableRooms() {

        var checkin = $("input[name=checkin]").val();
        var checkout = $("input[name=checkout]").val();

        var roomType = $("select[name=room_type]").val();
        var floor = $("select[name=floor]").val();

        if (checkin.length < 3 || checkout.length < 3) {
            return;
        }
        $(".walkin-room-table > tbody").html("Loading....");
        $.ajax({
            url: '{{action("\Kris\Frontdesk\Controllers\ReservationsController@getAvailableRooms")}}?checkin=' + checkin + '&checkout=' + checkout+"&floor="+floor+"&type="+roomType,
            type: "get",
            success: function (resp) {
                //data = JSON.parse(data);
                $(".walkin-room-table > tbody").html("");
                $.each(resp, function (key, value) {
                    var row = $("<tr data-rate='"+value.rate_amount+"'>").append('<td><input class="room_tick" type="checkbox" value="' + value.idrooms + '" name="room_'+value.idrooms+'" /></td><td>'+value.room_number+'</td><td>'+value.type_name+'</td><td>'+value.floor_name+'</td><td><input size="10" name="rate_'+value.idrooms+'" value="'+value.rate_amount+'" class="room_rate_input" type="text"></td>');
                    $(".walkin-room-table > tbody").append(row);
                })
            }
        });
    }
</script>

<div style="padding:6px 10px" class="row walkin-form-body">

    <form id="walkin-form" action="{{action("\Kris\Frontdesk\Controllers\ReservationsController@reserveGroup")}}" method="post">
        <div class="col-xs-5">

            <p class="section-title">
                <span>Group information</span>
            </p>
            <fieldset>
                <label>Group Name</label>
                <input type="text" autocomplete="off" name="group_name" placeholder="Name of the group" />
            </fieldset>

            <p class="section-title">
                <span>Stay information</span>
            </p>
            <input type="hidden" name="_token" value="{{csrf_token()}}" />
            <fieldset>
                <label>Checkin</label>
                <input onchange="getAvailableRooms();" name="checkin" autocomplete="off" class="datepicker" type="text" value="" placeholder="YYYY-MM-DD" />
            </fieldset>

            <fieldset>
                <label>Checkout</label>
                <input onchange="getAvailableRooms();" name="checkout" autocomplete="off" class="datepicker" type="text" placeholder="YYYY-MM-DD" />
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
                <span title="Complimentary Room">CR</span> <input name="package" value="CR" type="radio" />
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

        <div class="col-xs-7" style="">

            <p class="section-title">
                <span>Room configuration</span>
            </p>
            <div class="room-filter">
                Room Type
                <select onchange="getAvailableRooms()" name="room_type">
                    <option value="0">All</option>
                    @foreach(\Kris\Frontdesk\RoomType::all() as  $type)
                    <option value="{{$type->idroom_types}}">{{$type->type_name}}</option>
                    @endforeach
                </select>
                Floor
                <select onchange="getAvailableRooms()" name="floor">
                    <option value="0">All</option>
                    @foreach(\Kris\Frontdesk\Floor::all() as $floor)
                    <option value="{{$floor->idfloors}}">{{$floor->floor_name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="rooms-table-wrapper">
                <table class="walkin-room-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Room</th>
                            <th>Room Type</th>
                            <th>Floor</th>
                            <th>Rate</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
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

            </fieldset>

            <p class="text-center">
                <img src="/images/frontdesk/card-visa.svg" width="34" />
                <img src="/images/frontdesk/card-mastercard.svg" width="34" />
                <img src="/images/frontdesk/card-front.svg" width="34" />
            </p>
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
                            {{$mode->method_name}}
                        </option>
                        @endforeach
                    </select>
                </div>
            </fieldset>



        </div>


        <div class="clearfix"></div>
        <footer>
            <button type="submit" class="btn btn-primary"><i class="fa fa-calendar"></i> Reserve</button>
        </footer>

    </form>
</div>

@stop