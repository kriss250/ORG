<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{csrf_token() }}" >

        {!!HTML::style('assets/css/vendor/bootstrap.min.css') !!}
        {!!HTML::style('assets/css/vendor/font-awesome.min.css') !!}
        {!!HTML::style('assets/js/vendor/chosen/chosen.css') !!}
        {!!HTML::style('assets/js/vendor/datatables/css/jquery.dataTables.min.css') !!}
        {!!HTML::style('assets/js/vendor/datatables/extensions/TableTools/css/dataTables.tableTools.min.css') !!}
        {!!HTML::style('assets/js/vendor/datepicker/css/bootstrap-datepicker3.standalone.min.css') !!}
        {!!HTML::style('assets/css/vendor/jquery-ui.min.css') !!}
        {!!HTML::style('assets/js/vendor/datetimepicker/css/bootstrap-datetimepicker.min.css') !!}
        {!!HTML::style('assets/css/POS.css') !!}

        {!!HTML::style('assets/css/'.(\Session::get("pos.mode")).'.css')!!}
        {!!HTML::style('assets/css/touch.css') !!}
        {!!HTML::style('assets/css/orders.css') !!}
        {!!HTML::style('assets/css/keyboard.css') !!}

    <!-- SCRIPTS -->
        {!!HTML::script('assets/js/vendor/jquery/jquery-1.11.2.min.js') !!}
        {!!HTML::script('assets/js/vendor/bootstrap/bootstrap.min.js') !!}
        {!!HTML::script('assets/js/vendor/jquery-ui/jquery-ui.min.js') !!}
        {!!HTML::script('assets/js/vendor/moment/moment.min.js') !!}
        {!!HTML::script('assets/js/vendor/datatables/js/jquery.dataTables.min.js') !!}
        {!!HTML::script('assets/js/vendor/chosen/chosen.jquery.min.js') !!}
        {!!HTML::script('assets/js/vendor/datatables/extensions/TableTools/js/dataTables.tableTools.min.js') !!}
        {!!HTML::script('assets/js/vendor/accounting/accounting.js') !!}
        {!!HTML::script('assets/js/vendor/datetimepicker/js/bootstrap-datetimepicker.min.js') !!}
        {!!HTML::script('assets/js/vendor/datepicker/js/bootstrap-datepicker.min.js') !!}
        {!!HTML::script('assets/js/pos.jquery.js') !!}
        {!!HTML::script('assets/js/POS.js') !!}
        {!!HTML::script('assets/js/keyboard.js') !!}
    
    <title>ORG POS </title>
</head>
<body class="noselect">
    <div class="print_container"></div>


    @if(isset($errors) && count($errors) > 0)
    <div style="background: rgb(192, 57, 43) none repeat scroll 0% 0%; color: rgb(0, 0, 0);" class="ualert ui-draggable ui-draggable-handle">
        <i class="fa fa-exclamation-triangle"></i><div class="inner_content">

            {{$errors->first() }}
        </div><button class="ok-btn ht_close">OK</button>
    </div>
    @endif

    <div class="header">
        <div class="grid">
            <div class="row">
                <div class="col-md-4 col-xs-3">
                    <h4 style="text-transform:uppercase;font-family:'Open Sans';opacity:.7;padding-left:20px">
                        <img class="col-xs-2" width="40" style="padding:0;filter:sepia(100%);margin-top:-10px;max-height:100%" src="{{\App\Settings::get("logo")[0]}}" />
                        <span class="col-xs-10">
                            Ordering System
                            <span style="display:block;opacity:.5;font-size:11px">ORG Point of sales</span>
                        </span>
                    </h4>
                </div>

                <div class="col-md-8 col-xs-9 header-right">

                    <ul>
                        <li style="position:relative">

                            @if(\App\POSSettings::get("custom_product")=="1")
                            <button class="custom_prod_btn"><i class="fa fa-plus"></i> Custom Product</button>
                            <div class="new_prod">
                                <form id="custom_product_form" action="{{ action('ProductsController@CreateCustomProduct') }}" method="post">
                                    <button class="new_prod_close_btn"><i class='fa fa-times'></i></button>
                                    <label>Name</label> <input name="product_name" type="text">
                                    <label>Price</label> <input name="product_price" type="text">
                                    <label>Category</label><select style="max-width:150px;display:block" required="" name="category">
                                        <option value="">Category</option><?php $cats = \DB::select("SELECT id,category_name,store_name,store_id FROM categories join category_store on category_store.category_id = categories.id join store on store.idstore = store_id"); ?>
                                        @foreach($cats as $cat)
                                        <option value="{{ $cat->id }}-{{$cat->store_id}}">{{$cat->category_name}} ({{ $cat->store_name }})</option>
                                        @endforeach
                                    </select>
                                    <input type="submit" value="Add">
                                    {!! csrf_field() !!}
                                </form>
                            </div>
                            @endif
                        </li>
                        <li>
                            <span class="clock">
                                <i class="fa fa-clock-o"></i>&nbsp;
                                {{date('l d, m Y',strtotime(\ORG\Dates::$RESTODT)) }}
                                <i class="time">{{date('H:i') }}</i>
                            </span>
                        </li>
                        <li>
                            <a style="margin-top:-10px" href="{{route('order') }}" class="btn btn-xs btn-danger">
                                <i class="fa fa-times"></i> EXIT
                            </a>
                        </li>


                    </ul>
                </div>
            </div>

        </div>
    </div>
    <div class="grid main-container">
        <div class="row">
            <div class="mini-submenu">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </div>

            <div class="contents col-md-9 col-lg-9">
                @yield("printHeader")
                @yield("contents")
            </div>
        </div>
    </div>

    <!--ON SCREEN KEYBOARD-->
    <div class="k-keyboard">
        <header>
            <div class="input-preview pull-left">
                <input class="preview-text-box" spellcheck="true" type="text" />
                <button class="erase-key-btn"><i class="fa fa-arrow-left" aria-hidden="true"></i></button>
                <div class="clearfix"></div>
            </div>
            <button class="hide-btn hide-key-btn pull-right">
                <i class="fa fa-keyboard-o" style="font-size:14px;display:block"></i>
                <i class="fa fa-sort-desc" style="margin-top:-9px;display:block" aria-hidden="true"></i>

            </button>
            <div class="clearfix"></div>
        </header>

        <div class="numeric-keys">
            <ul>
                <li>
                    <a key="1" href="">
                        1
                        <span class="hidden-keys">!</span>
                    </a>
                </li>
                <li>
                    <a key="2" href="">
                        <span class="hidden-keys">@</span>2
                    </a>
                </li>
                <li>
                    <a key="3" href="">
                        <span class="hidden-keys">#</span>3
                    </a>
                </li>
                <li>
                    <a key="4" href="">
                        <span class="hidden-keys">$</span>4
                    </a>
                </li>
                <li>
                    <a key="5" href="">
                        <span class="hidden-keys">%</span>5
                    </a>
                </li>
                <li>
                    <a key="6" href="">
                        <span class="hidden-keys">^</span>6
                    </a>
                </li>
                <li>
                    <a key="7" href="">
                        <span class="hidden-keys">&</span>7
                    </a>
                </li>
                <li>
                    <a key="8" href="">
                        <span class="hidden-keys">*</span>8
                    </a>
                </li>
                <li>
                    <a key="9" href="">
                        <span class="hidden-keys">(</span>9
                    </a>
                </li>
                <li>
                    <a key="0" href="">
                        <span class="hidden-keys">)</span>0
                    </a>
                </li>
                <li>
                    <a key="." href="">
                        <span class="hidden-keys">-</span>.
                    </a>
                </li>
                <li>
                    <a key="/" href="">
                        <span class="hidden-keys">,</span>/
                    </a>
                </li>
            </ul>
        </div>

        <div class="alpha-keys">
            <ul class="upper-keys">
                <li>
                    <a key="q" href="">Q</a>
                </li>
                <li>
                    <a key="w" href="">W</a>
                </li>
                <li>
                    <a key="e" href="">E</a>
                </li>
                <li>
                    <a key="r" href="">R</a>
                </li>

                <li>
                    <a key="t" href="">T</a>
                </li>
                <li>
                    <a key="y" href="">Y</a>
                </li>

                <li>
                    <a key="u" href="">U</a>
                </li>

                <li>
                    <a key="i" href="">I</a>
                </li>

                <li>
                    <a key="o" href="">O</a>
                </li>

                <li>
                    <a key="p" href="">P</a>
                </li>
            </ul>

            <ul class="middle-keys">
                <li>
                    <a key="a" href="">A</a>
                </li>
                <li>
                    <a key="s" href="">S</a>
                </li>

                <li>
                    <a key="d" href="">D</a>
                </li>

                <li>
                    <a key="f" href="">F</a>
                </li>

                <li>
                    <a key="g" href="">G</a>
                </li>

                <li>
                    <a key="h" href="">H</a>
                </li>

                <li>
                    <a key="j" href="">J</a>
                </li>

                <li>
                    <a key="k" href="">K</a>
                </li>

                <li>
                    <a key="l" href="">L</a>
                </li>
            </ul>

            <ul class="lower-keys">
                <li>
                    <a key="z" href="">Z</a>
                </li>

                <li>
                    <a key="x" href="">X</a>
                </li>

                <li>
                    <a key="c" href="">C</a>
                </li>

                <li>
                    <a key="v" href="">V</a>
                </li>

                <li>
                    <a key="b" href="">B</a>
                </li>

                <li>
                    <a key="n" href="">N</a>
                </li>

                <li>
                    <a key="m" href="">M</a>
                </li>
            </ul>
        </div>


        <ul class="special-keys">
            <li class="numeric-key">
                <a href="">SHIFT</a>
            </li>
            <li class="space-key" style="width:50%;">
                <a key=" " style="display:block" href="">_____</a>
            </li>
            <li class="enter-key">
                <a href="">OK</a>
            </li>
        </ul>
    </div>
    <!--\ON SCREEN KEYBOARD\-->

    <div class="waiter-login-wrapper hidden">
        <div class="waiter-login">

            <ul class="waiter-login-list pull-left btn-group" data-toggle="buttons">

                @foreach(\App\Waiter::where("is_active","1")->get() as $waiter)
                <li>
                    <label class="btn btn-default">
                        <i class="fa fa-user"></i>
                        <input type="radio" name="waiterid" value="{{$waiter->idwaiter}}" id="option{{$waiter->idwaiter}}" autocomplete="off"> {{$waiter->waiter_name}}
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
                    <li data-key="{{$x}}" {{$x==3 || $x==6 || $x==9 ? 'style="margin-right:0"': ''}}>
                        {{$x}}
                    </li>
                    @endfor
                    <li data-key="0">0</li>
                    <li class="pin-delete-btn" style="width:130px">< DEL.</li>
                </ul>
                <hr class="clearfix" />
                <div class="row text-center waiter-login-btns">
                    <div class="col-xs-7 pull-left">
                        <button class="push-btn btn btn-success save_print_order">ORDER</button>
                    </div>

                    <div class="col-xs-5">
                        <button class="cancel_login_btn push-btn btn btn-danger">CANCEL</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


</body>
</html>
