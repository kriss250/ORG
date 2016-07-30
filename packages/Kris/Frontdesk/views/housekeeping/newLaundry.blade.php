
@extends("Frontdesk::MasterIframe")

@section("contents")
<div class="panel-desc">
    <p class="title">Add Laundry Order</p>
    <p class="desc"></p>
</div>


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
            <label>Items</label>
            <input required style="margin-top:6px;padding:4px 5px !important;border-top:1px dashed rgb(215, 215, 215) !important" value="{{old("items")}}" type="text" name="items" placeholder="Items separated by commas" />
        </fieldset>

        <p class="section-title">
            Room & Amount
        </p>
        <div style="padding:0" class="container-fluid">
            <fieldset style="width:25%;box-sizing:padding-box">
                <label>Room</label>
                <input required style="margin-top:6px;padding:4px 5px !important;border-top:1px dashed rgb(215, 215, 215) !important" value="{{old("room")}}" type="text" name="room" placeholder="#" />
            </fieldset>

            <fieldset style="width:34%;box-sizing:padding-box">
                <label>Amount</label>
                <input required style="margin-top:6px;padding:4px 5px !important;border-top:1px dashed rgb(215, 215, 215) !important" value="{{old("amount")}}" type="text" name="amount" placeholder="#" />
            </fieldset>

            <fieldset style="width:31%;box-sizing:padding-box">
                <label>Reference</label>
                <input style="margin-top:6px;padding:4px 5px !important;border-top:1px dashed rgb(215, 215, 215) !important" value="{{old("ref")}}" type="text" name="ref" placeholder="#" />
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