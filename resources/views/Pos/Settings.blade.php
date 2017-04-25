@extends("Pos.master")

@section("contents")

@if(strlen(session('success'))>0)
     {!! "<div class='alert alert-success'>".session('success')."</div>" !!}
 @endif

     {{session('errors')}}

{!!Form::Open(['action' => 'SettingsController@store','class'=>'']) !!}
<h2> <i class="fa fa-cogs"></i> Settings </h2>

<div class="row">
    <div class="col-md-4">
        <label>General</label>

        <div class="input-group">
            <span class="input-group-addon">
                Allow Custom products
            </span>

            <div class="form-control">
                <input {{\App\POSSettings::get("custom_product")== "1" ? 'checked' : "" }} name="custom_product" type="checkbox" />
            </div>
        </div>

        <div class="input-group">
            <span class="input-group-addon">
                Allow price changes
            </span>

            <div class="form-control">
                <input {{\App\POSSettings::get("price_change")== "1" ? 'checked' : "" }} name="price_change" type="checkbox" />
            </div>
        </div>
    </div>
</div>
  
<br/>

{!!Form::submit('Save',['class'=>'btn btn-success'])!!}

{!! Form::Close() !!}

@stop