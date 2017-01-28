@extends("Frontdesk::MasterIframe")

@section("contents")

<script type="text/javascript">
    initSelectBoxes();
</script>
<style>
    .inline-fieldsets fieldset {
        display: block !important;
        width: 100%;
        background: rgb(253,253,253);
        border-radius: 0;
    }
</style>
<div style="padding:10px 10px;background:#fff" class="inline-fieldsets modal-header">

    <h4>Create A New Room</h4>
    <p class="subtitle" style="margin-top:-5px;">Create a new room and assign it to a floor</p>
  
</div>

<div class="modal-content">
    <form action=""  method="post">
        <p>&nbsp;</p>
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <fieldset>
            <label>Choose Type</label>
            <div class="select-wrapper">
                
                <i class="fa fa-angle-down"></i>
                <select required name="type">
                    <option value="">Choose</option>
                    @foreach(\Kris\Frontdesk\RoomType::all() as  $type)
                    <option value="{{$type->idroom_types}}">{{    $type->type_name}}</option>
                    @endforeach
                </select>
            </div>
        </fieldset>
       
        <fieldset>
            <label>Night Rate</label>
            <div class="select-wrapper">
                <i class="fa fa-angle-down"></i>
                <select name="rate_id">
                    @foreach(Kris\Frontdesk\RateType::all() as $rtype)
                    <option value="{{$rtype->idrate_types}}">
                        {{    $rtype->name}}
                    </option>
                    @endforeach
                </select>
            </div>
            <input autocomplete="off" type="text" name="rate" placeholder="0.0" />
        </fieldset>
      
        <div class="clearfix"></div>
        <br />
       
        <input type="submit" value="Save" class="btn btn-success" />
        <p>&nbsp;</p>

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