@extends("Frontdesk::MasterIframe")

@section("contents")
<div class="panel-desc">
    <p class="title">Add New Order</p>
    <p class="desc"></p>
</div>

<script type="text/javascript">
    initSelectBoxes();
    window.opener.autoRefresh = true;
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
    <form action="{{action("\Kris\Frontdesk\Controllers\OperationsController@addBanquetEvent")}}" method="post">
        <input type="hidden" name="_token" value="{{csrf_token()}}" />


        <div style="padding:0" class="container-fluid">
            <fieldset style="width:48%" class="bordered col-xs-6">
                <label>From Date</label>
                <input required class="datepicker" value="" type="text" name="startdate" placeholder="#" />
            </fieldset>

            <fieldset style="width:48%" class="bordered col-xs-6">
                <label>To Date</label>
                <input required value=""  class="datepicker" type="text" name="enddate" placeholder="#" />
            </fieldset>
        </div>

        <fieldset>
            <label>Banquet</label>
            <div class="clearfix"></div>

            <div class="select-wrapper">
                <i class="fa fa-angle-down"></i>
                <select required name="banquet">
                    <option value="">Choose Banquet</option>
                    @foreach(\Kris\Frontdesk\Banquet::all() as $ban)
                    <option value="{{$ban->idbanquet}}">
                        {{$ban->banquet_name}}
                    </option>
                    @endforeach
                </select>
            </div>
        </fieldset>

        <fieldset>
            <label>Event</label>
            <input required style="margin-top:6px;padding:4px 5px !important;border-top:1px dashed rgb(215, 215, 215) !important" value="" type="text" name="desc" placeholder="Specify..." />
        </fieldset>

        <input type="submit" value="Save" class="btn btn-success" />
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