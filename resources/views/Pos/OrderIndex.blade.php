@extends('/Pos/OrderMaster')

@section("contents")

            <style>
                .header {
            display:none;
        }
                body {
                    background: #21303F;
                }
                .main-container {
                    background: linear-gradient(to top,#21303F,#111c27);
                    color: #fff;
                    position:absolute;
                    bottom:0;
                    left:0;
                    top:0;
                    right:0
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
        <a class="lorder-btn" href="{{route('newOrder')}}">
            <i class="fa fa-list-ol"></i>
            My Orders
        </a>
    </li>

    <li>
        <a class="pin-change-btn" href="{{route('newOrder')}}">
            <i class="fa fa-key"></i>
            Change PIN
        </a>
    </li>
</ul>

<div class="waiter-login-wrapper hidden">
    <div class="waiter-login">

        <ul class="waiter-login-list pull-left btn-group" data-toggle="buttons">
            @foreach(\App\Waiter::all() as $waiter)
            <li>
                <label class="btn btn-default">
                    <i class="fa fa-user"></i>
                    <input type="radio" name="waiterid" value="{{$waiter->idwaiter}}" id="option{{$waiter->idwaiter}}" autocomplete="off" />{{$waiter->waiter_name}}
                </label>

            </li>
            @endforeach
        </ul>

        <div style="width:27.1%" class="text-left pull-right">
            <p>Enter your PIN</p>
            <input id="waiter-pin-input" autocomplete="off" readonly type="password" />
            <p style="margin-bottom:-5px">&nbsp;</p>
            <ul class="row pin-keyboard">
                @for($x=1;$x<=9;$x++)
                <li data-key="{{$x}}" {{$x==3 || $x==6 || $x==9 ? 'style="margin-right:0" ': ''}}>
                    {{$x}}
                </li>
                @endfor
                <li data-key="0">0</li>
                <li class="pin-delete-btn" style="width:130px">
                    < />
                </li>
            </ul>
            <hr class="clearfix" />
            <div class="row text-center waiter-login-btns">
                <div class="col-xs-7 pull-left">
                    <button class="push-btn btn btn-success save_print_order">CHANGE PIN</button>
                </div>

                <div class="col-xs-5">
                    <button class="cancel_login_btn push-btn btn btn-danger">CANCEL</button>
                </div>
            </div>
        </div>
    </div>
</div>
<br />
<br />
@stop