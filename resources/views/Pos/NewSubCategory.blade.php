@extends("Pos.master")

@section("contents")



	{!! Form::Open(['action' => 'ProductsSubCategoryController@index','class'=>'single_form',"id"=>"ajaxsave"]) !!}
	<h2 class="text-center"> <i class="fa fa-cart-plus"></i> New Category </h2>

    {!! Form::label('product_label', 'Sub Category Name') !!}
    {!! Form::text('subcategory_name', '',["class"=>"form-control","required"=>"required"]) !!}
    {!! Form::label('product_label', 'Category') !!} <br/>

<select class="thechosen" name="category">

<option value="0">Choose Category</option>
	@foreach($cats as $cat)
<option value="{{$cat->id}}"> {{$cat->category_name}} </option>
	@endforeach

</select>
   
   <br/>


    {!! Form::label('product_label', 'Description') !!}
    {!! Form::textarea('description', '',["class"=>"form-control"]) !!}



<br/>
{!!Form::submit('Save',['class'=>'btn btn-success'])!!}

{!! Form::Close() !!}


@stop