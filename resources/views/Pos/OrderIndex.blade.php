@extends('/Pos/OrderMaster')

@section("contents")

<style>
    .header {
        display: none;
    }
    body {
        background: #21303F;
    }
    .main-container {
        background: linear-gradient(to top,#21303F,#111c27);
        color: #fff;
        position: absolute;
        bottom: 0;
        left: 0;
        top: 0;
        right: 0;
    }

    .order-menu .norder-btn {
        background: #13c63b;
        border-color: transparent;
        border-radius: 19px;
        box-shadow: 0 0 6px #043b10;
    }

    .order-menu .lorder-btn {
        background: #13a6c6;
        border-color: transparent;
        border-radius: 19px;
        box-shadow: 0 0 6px #041c3b;
    }
    </style>

<script>
    $(document).ready(function () {
        $(".change_pin_btn").click(function (e) {
            e.preventDefault();
            if ($("#waiter-new-pin-input").val().length < 4) {
                alert("PIN Must be atleast 4 characters long !");
                return;
            }
            var waiter = $("[name=waiterid]:checked").val();
            if (waiter < 1) {
                alert("Please choose waiter");
                return;
            }
            $.ajax({
                url: '{{action("WaiterController@changePIN")}}',
                type: 'post',
                data: { waiterid: waiter, old_pin: $("#waiter-old-pin-input").val(), new_pin: $("#waiter-new-pin-input").val(), _token: '{{csrf_token()}}' },
                success: function (data) {
                    data = JSON.parse(data);

                    if (typeof data.error !== "undefined" && data.error.length > 0) {
                        //There was an error
                        alert(data.error);
                    } else if (typeof data.success !== "undefined") {
                        $("#waiter-old-pin-input").val("");
                        $("#waiter-new-pin-input").val("");
                        alert("PIN changed");
                    }
                }
            });
        });

        $(".cancel_change_pin_btn").click(function (e) {
            e.preventDefault();
            $(".waiter-pin-change").toggleClass("hidden");
            $("#waiter-old-pin-input").val("");
            $("#waiter-new-pin-input").val("");
        });

        $(".pin-change-btn").click(function (e) {
            e.preventDefault();
            $(".waiter-pin-change").toggleClass("hidden");
            $("#waiter-old-pin-input").val("");
            $("#waiter-new-pin-input").val("");
        });
    })
</script>
<h1 style="font-size:50px;" class="text-center">Kitchen/Bar Order</h1>
<p class="text-center" style="color:#8e8e8e">ORG Restaurant Ordering System</p>

<div class="clock text-center">
    <p>
        <i class="fa fa-clock-o"></i>&nbsp;{{date('l d, m Y',strtotime(\ORG\Dates::$RESTODT)) }}
    </p>
    <i style="font-size:70px;" class="time">{{date('H:i') }}</i>
</div>
<ul class="order-menu">

    <li>
        <a class="norder-btn" href="{{route('newOrder')}}">
            <i class="fa fa-plus-circle"></i>
            New Order
        </a>
    </li>

    <li>
        <a class="lorder-btn" href="#">
            <i class="fa fa-list-ol"></i>
            My Orders
        </a>
    </li>

    <li>
        <a class="pin-change-btn" href="#">
            <i class="fa fa-key"></i>
            Change PIN
        </a>
    </li>
</ul>

<div class="waiter-login-wrapper waiter-pin-change hidden">
    <div class="waiter-login">

        <ul class="waiter-login-list pull-left btn-group col-md-4" data-toggle="buttons">
            @foreach(\App\Waiter::all() as $waiter)
            <li style="font-size:14px !important">
                <label class="btn btn-default">
                    <i class="fa fa-user"></i>
                    <input type="radio" name="waiterid" value="{{$waiter->idwaiter}}" id="option{{$waiter->idwaiter}}" autocomplete="off" />{{$waiter->waiter_name}}
                </label>
            </li>
            @endforeach
        </ul>
        <div style="width:29.1%;color:#000" class="text-left pull-right">
            <p>Enter your New PIN</p>
            <input id="waiter-new-pin-input" autocomplete="off" style="width:90%" readonly type="password" />
            <p style="margin-bottom:-5px">&nbsp;</p>
            <ul class="row pin-keyboard">
                @for($x=1;$x<=9;$x++)
                <li style="color:#000" data-field="#waiter-new-pin-input" data-key="{{$x}}" {{$x==3 || $x==6 || $x==9 ? 'style="margin-right:0" ': ''}}>
                    {{$x}}
                </li>
                @endfor
                <li style="color:#000;" data-field="#waiter-nepin-w-input" data-key="0">0</li>
                <li style="color:#000;width:130px" data-field="#waiter-new-pin-input" class="pin-delete-btn">
                     DEL.
                </li>
            </ul>
            <hr class="clearfix" />
            
        </div>

        <div style="width:29.1%;color:#000" class="text-left pull-right">
            <p>Enter your Old PIN</p>
            <input id="waiter-old-pin-input" style="width:90%;font-size:25px" autocomplete="off" readonly type="password" />
            <p style="margin-bottom:-5px">&nbsp;</p>
            <ul class="row pin-keyboard">
                @for($x=1;$x<=9;$x++)
                <li data-field="#waiter-old-pin-input" style="color:#000" data-key="{{$x}}" {{$x==3 || $x==6 || $x==9 ? 'style="margin-right:0" ': ''}}>
                    {{$x}}
                </li>
                @endfor
                <li data-field="#waiter-old-pin-input" style="color:#000" data-key="0">0</li>
                <li data-field="#waiter-old-pin-input" style="color:#000;width:130px" class="pin-delete-btn" >
                     DEL.
                </li>
            </ul>
            <hr class="clearfix" />
            
        </div>


        <div class="row text-center waiter-login-btns">
            <div class="col-xs-3 pull-right">
                <button class="cancel_change_pin_btn push-btn btn btn-danger">CANCEL</button>
            </div>
            <div class="col-xs-3 pull-right">
                <button class="push-btn btn btn-success change_pin_btn">CHANGE PIN</button>
            </div>
           
        </div>
    </div>
</div>
<br />
<br />
@stop