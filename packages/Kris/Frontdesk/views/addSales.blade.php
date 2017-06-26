
@extends("Frontdesk::MasterIframe")

@section("contents")

<script type="text/javascript">
    initSelectBoxes();
</script>
<style>
    .inline-fieldsets fieldset{
        display:block !important;
        width:100%;
        background:rgb(253,253,253);
        border-radius:0
    }
</style>
<div style="padding:0" class="inline-fieldsets list-wrapper">
    <p class="list-wrapper-title">Extra Sales</p>
    <br />
    <form style="padding:12px" action="" method="post">
        <input type="hidden" name="_token" value="{{csrf_token()}}" />
        
        <fieldset>
            <label>Guest Names</label>
            <input required style="margin-top:6px;padding:4px 5px !important;border-top:1px dashed rgb(215, 215, 215) !important" value="{{old("names")}}" type="text" name="names" placeholder="Firstname & Lastname" />
        </fieldset>

        <fieldset>
            <label>Address / Phone</label>
            <input style="margin-top:6px;padding:4px 5px !important;border-top:1px dashed rgb(215, 215, 215) !important" value="{{old("phone")}}" type="text" name="phone" placeholder="#" />
        </fieldset>

        <div style="padding:0" class="container-fluid">
         

            <fieldset style="width:60%;box-sizing:padding-box">
                <label>Description</label>
                <input required style="margin-top:6px;padding:4px 5px !important;border-top:1px dashed rgb(215, 215, 215) !important" value="{{old("desc")}}" type="text" name="desc" placeholder="Specify..." />
            </fieldset>

            <fieldset style="width:32.5%;float:left;box-sizing:padding-box">
                <label>Service</label>
                <div class="select-wrapper">
                    <i class="fa fa-angle-down"></i>
                    <select required name="service">
                        <option value="">Choose Service</option>
                        <option value="Halls">Hall</option>
                        <option value="Gym">Gym</option>
                        <option value="Sauna/Massage">Sauna/Massage</option>
                        <option value="Stationary">Stationary</option>
                        <option value="Resto/Bar">Resto/Bar</option>
                       <!-- <option value="Other">Other</option>-->
                    </select>
                </div>
            </fieldset>
        </div>
     

        

       

        <p class="section-title">
            <span>Payment info</span>
        </p>

        <div style="padding:0" class="container-fluid">
            <fieldset style="width:40%">
                <label>Mode Of Payment</label>
                <!--<span>Credit</span> <input name="mode" value="1" type="radio" />--> <span>Payment</span> <input value="0" name="mode" checked type="radio" />
                <div class="clearfix"></div>
                <br />
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

            <fieldset style="width:55%">
                <label>Amount</label>
                <input required style="margin-top:6px;padding:4px 5px !important;border-top:1px dashed rgb(215, 215, 215) !important" value="{{old("amount")}}" type="text" name="amount" placeholder="#" />
                <div class="select-wrapper">
                    <i class="fa fa-angle-down"></i>
                    <select required name="currency">
                        <option value="">Currency </option>
                        @foreach(Kris\Frontdesk\Currency::all() as $currency)
                        <option value="{{$currency->idcurrency}}">
                            {{$currency->alias}}
                        </option>
                        @endforeach

                    </select>
                </div>
            </fieldset>
        </div>
        <br />
        <br />
        <br />
        <button style="margin:auto;display:block" type="submit" value="Save" class="btn btn-primary"><i class="fa fa-save"></i> Save & Print</button>
    </form>
</div>

<script>
    $(document).ready(function(){
    @if(session('msg'))
        alert("{{session('msg')}}");
    @elseif(session('errors'))
        error('{{session("errors")->first()}}');
    @endif
    });

</script>
@stop