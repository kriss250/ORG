@extends("Pos.master")

@section("contents")

@if(strlen(session('success'))>0)
     {!! "<div class='alert alert-success'>".session('success')."</div>" !!}
 @endif

     {{session('errors')}}

{!! Form::Open(['action' => 'SettingsController@store','class'=>'']) !!}
<h2> <i class="fa fa-cogs"></i> Settings </h2>


    {!! Form::label('product_label', 'Restaurant Name') !!}
    {!! Form::text('resto_name', \ORG\Settings::$RESTONAME,["class"=>"form-control"]) !!}

    {!! Form::label('product_label', 'Logo') !!}
    {!! Form::text('logo', \ORG\Settings::$LOGO,["class"=>"form-control"]) !!}

    {!! Form::label('product_label', 'Currency') !!}
    {!! Form::text('currency', \ORG\Settings::$CURRENCY,["class"=>"form-control"]) !!}

    {!! Form::label('product_label', 'Tax(%)') !!}
    {!! Form::text('tax', \ORG\Settings::$TAXRATE,["class"=>"form-control"]) !!}

<p>Contacts</p>
    
    {!! Form::label('product_label', 'Phone') !!}
    {!! Form::text('phones', \ORG\Settings::$PHONES,["class"=>"form-control"]) !!}

    {!! Form::label('product_label', 'Email') !!}
    {!! Form::text('email', \ORG\Settings::$EMAIL,["class"=>"form-control"]) !!}


    {!! Form::label('product_label', 'Website') !!}
    {!! Form::text('website', \ORG\Settings::$WEBSITE,["class"=>"form-control"]) !!}


    {!! Form::label('product_label', 'Bill Footer Text') !!}
    {!! Form::text('bill_footer', \ORG\Bill::$FOOTER,["class"=>"form-control"]) !!}


<br/>

{!!Form::submit('Save',['class'=>'btn btn-success'])!!}

{!! Form::Close() !!}

@stop