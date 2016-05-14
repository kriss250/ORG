@extends("Pos.master")

@section("contents")



	{!! Form::Open(['action' => 'ProductsCategoryController@index','class'=>'single_form',"id"=>"ajaxsave"]) !!}
	<h2 class="text-center"> <i class="fa fa-cart-plus"></i> New Category </h2>

    {!! Form::label('product_label', 'Category Name') !!}
    {!! Form::text('category_name', '',["class"=>"form-control","required"=>"required"]) !!}

    {!! Form::label('product_label', 'Store') !!}
    <br>
    <select name="store" required="required">
    	<option value="0">Choose Store</option>
    	@foreach($stores as $store)
        <option value="{{ $store->idstore}}">{{ $store->store_name }}</option>
    	@endforeach
    </select>
<br/>
    {!! Form::label('product_label', 'Description') !!}
    {!! Form::textarea('description', '',["class"=>"form-control"]) !!}



<br/>

{!!Form::submit('Save',['class'=>'btn btn-success'])!!}
{!! Form::Close() !!}

@stop