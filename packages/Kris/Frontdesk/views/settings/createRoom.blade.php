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
        <input type="hidden" name="_token" value="{{csrf_token()}}" />
        <p>&nbsp;</p>
        <p>Room Name</p>
        <input type="text" name="name" class="form-control" placeholder="E.g 402" />

        <p>Phone Ext.</p>
        <input type="text" name="phone" class="form-control" placeholder="E.g 102" />
        <br />
        <fieldset class="col-xs-6">
            <label>Choose Type</label>
            <div class="select-wrapper">
                
                <i class="fa fa-angle-down"></i>
                <select required name="type">
                    <option value="">Choose</option>
                    @foreach(\Kris\Frontdesk\RoomType::all() as  $type)
                    <option value="{{$type->idroom_types}}">{{$type->type_name}}</option>
                    @endforeach
                </select>
            </div>
        </fieldset>

        <div class="col-xs-6 pull-right" style="padding-right:0">
            <fieldset>
                <label>Choose Floor</label>
                <div class="select-wrapper">

                    <i class="fa fa-angle-down"></i>
                    <select required name="floor">
                        <option value="">Choose</option>
                        @foreach(\Kris\Frontdesk\Floor::all() as $floor)
                        <option value="{{$floor->idfloors}}">{{    $floor->floor_name}}</option>
                        @endforeach
                    </select>
                </div>
            </fieldset>
        </div>
        <div class="clearfix"></div>
        <br />
        <p>Description</p>
        <textarea class="form-control"></textarea>
        <p>&nbsp;</p>
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