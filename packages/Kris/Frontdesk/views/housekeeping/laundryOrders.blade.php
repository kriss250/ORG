
@extends("Frontdesk::MasterIframe")

@section("contents")
<div class="panel-desc">
    <p class="title">Add Sold Service / Item</p>
    <p class="desc"></p>
</div>

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
<div style="padding:6px 10px" class="inline-fieldsets">
    <form action="" method="post">
        <input type="hidden" name="_token" value="{{csrf_token()}}" />
        
        <fieldset>
            <label>Guest Names</label>
            <input required style="margin-top:6px;padding:4px 5px !important;border-top:1px dashed rgb(215, 215, 215) !important" value="{{old("names")}}" type="text" name="names" placeholder="Firstname & Lastname" />
        </fieldset>

        <fieldset>
            <label>Phone</label>
            <input style="margin-top:6px;padding:4px 5px !important;border-top:1px dashed rgb(215, 215, 215) !important" value="{{old("phone")}}" type="text" name="phone" placeholder="#" />
        </fieldset>

        <div style="padding:0" class="container-fluid">
            <fieldset style="width:37%;box-sizing:padding-box">
                <label>Receipt No.</label>
                <input required style="margin-top:6px;padding:4px 5px !important;border-top:1px dashed rgb(215, 215, 215) !important" value="{{old("receipt")}}" type="text" name="receipt" placeholder="#" />
            </fieldset>

            <fieldset style="width:60%;box-sizing:padding-box">
                <label>Description</label>
                <input required style="margin-top:6px;padding:4px 5px !important;border-top:1px dashed rgb(215, 215, 215) !important" value="{{old("desc")}}" type="text" name="desc" placeholder="Specify..." />
            </fieldset>
        </div>
     

        <fieldset>
            <label>Service</label>
            <div class="select-wrapper">
                <i class="fa fa-angle-down"></i>
                <select required name="service">
                    <option value="">Choose Service</option>
                    <option value="Halls">Hall</option>
                </select>
            </div>
        </fieldset>

       

        <p class="section-title">
            <span>Payment info</span>
        </p>

        <div style="padding:0" class="container-fluid">
            <fieldset style="width:40%">
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

            <fieldset style="width:55%">
                <label>Amount</label>
                <input required style="margin-top:6px;padding:4px 5px !important;border-top:1px dashed rgb(215, 215, 215) !important" value="{{old("amount")}}" type="text" name="amount" placeholder="#" />
            </fieldset>
        </div>
        <br />
        <button type="submit" value="Save" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
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