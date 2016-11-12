
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
<div style="padding:10px 10px;background:#fff" class="inline-fieldsets modal-header">
    

    <h4>Saved Currencies</h4>
    <p class="subtitle" style="margin-top:-5px;">Manage saved currencies and rates</p>

    <ul class="list-inline">
        <li>
            <a onclick="window.open('addCurrency','Create Currency','width=400,height=300')" class="btn btn-success btn-xs" href="#">Create currency</a>
        </li>
    </ul>
</div>

<div class="modal-content">
    <form  action="" method="post"> <input type="hidden" name="_token" value="{{csrf_token()}}" />
        <div style="padding:6px 0;overflow:auto;max-height:175px">
            <table class="table-form table table-condensed table-bordered table-striped" style="background:#f7f7f7;margin-top:-1px">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Alias</th>
                        <th>Rate #</th>
                    </tr>
                </thead>

                @foreach(\Kris\Frontdesk\Currency::all() as $currency)
                <tr class="text-left">
                    <td>{{$currency->name}}</td>
                    <td>
                        <input class="form-control" name="alias_{{$currency->idcurrency}}" required type="text" value="{{$currency->alias}}" placeholder="Alias" />
                    </td>
                    <td>
                        <input class="form-control" name="rate_{{$currency->idcurrency}}" required pattern="[\d\.]+" type="text" value="{{$currency->rate}}" placeholder="Rate" />
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
        <button type="submit" class="btn btn-primary" style="margin:5px auto">Update</button>
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

</script>@stop