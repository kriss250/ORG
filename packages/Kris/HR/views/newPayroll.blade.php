@extends("HR::Master")

@section("contents")


<div class="col-md-10 main-contents">
    <div class="page-title">
        <h3>New Payroll</h3>
        <p style="font-size:12px;opacity:.6;margin-top:-5px;">
            Use this form to generate a new payroll list
        </p>
    </div>
    <div class="row col-md-6" style="padding:10px 35px;">

        <form  action="{{action("\Kris\HR\Controllers\PayrollController@store")}}" method="post">
            <input type="hidden" name="_token" value="{{csrf_token()}}" />
            <label>Choose Month & Year</label>
            <div class="row">
                <div class="col-md-6">
                    <label>Month</label>
                    <select required name="month" class="form-control">
                        <option value="">Month</option>
                        @for($i=1;$i<13;$i++)
                                <option>{{$i}}</option>         
                        @endfor
                    </select>
                </div>

                <div class="col-md-6">
                    <label>Year</label>
                    <input name="year" readonly class="form-control" value="{{\Carbon\Carbon::now()->format("Y")}}" />
                </div>
            </div>

            <label>Taxes</label>
            <div class="form-inline">
                @foreach(\Kris\HR\Models\Tax::all() as $tax)
                <div class="input-group">

                    <label for="tax_{{$tax->idtaxes}}" class="input-group-addon">{{$tax->tax_name}}</label>
                    <div class="form-control">
                        <input name="tax_{{$tax->idtaxes}}" type="checkbox" checked class="frm-control" value="{{$tax->idtaxes}}" />
                    </div>
                </div>
                @endforeach
            </div>


            <label>Charges</label>
            <div class="form-inline">
                @foreach(\Kris\HR\Models\Charge::all() as $charge)
                <div class="input-group">

                    <label for="charge_{{$charge->idtaxes}}" class="input-group-addon">{{$charge->charge_name}}</label>
                    <div class="form-control">
                        <input name="charge_{{$charge->idcharges}}" type="checkbox" checked class="frm-control" value="{{$charge->idcharges}}" />
                    </div>
                </div>
                @endforeach
            </div>
            <label>Description</label>
            <textarea name="description" class="form-control"></textarea>
            <p>&nbsp;</p>
            <input type="submit" class="btn btn-success" value="Commit" />
        </form>

    </div>
</div>
@stop