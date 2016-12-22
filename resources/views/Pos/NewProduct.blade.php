@extends("Pos.master")

@section("contents")


<?php

    $product_name = isset($prod) ? $prod->product_name : ""  ;
    $price= isset($prod) ? $prod->price : "";
    $tax = isset($prod) ? $prod->tax : "";
    $desc = isset($prod) ? $prod->description : "" ;
    $action  = isset($prod) ? 'ProductsController@update' : 'ProductsController@store';
    $sub_cat = (isset($prod) && isset($prod->sub_cat)) ? $prod->sub_cat  : '-1';
    $the_title = isset($prod) ? "Update Product" :"New Product";
    $code = isset($prod) && isset($prod->code) ? $prod->code : "";
?>

@if(isset($prod))

    {!! Form::Open(['action' => [$action,$_GET['id']],'method'=>'PUT','class'=>'single_form',"id"=>"ajaxsave"]) !!}
    {!! Form::hidden('prev_price', $price,["class"=>"form-control"]) !!}
@else 

    {!! Form::Open(['action' => $action,'class'=>'single_form',"id"=>"ajaxsave"]) !!}

@endif

<h2> <i class="fa fa-cart-plus"></i> {{$the_title}} </h2>


    {!! Form::label('product_label', 'Product Name') !!}
    {!! Form::text('product_name', $product_name,["class"=>"form-control"]) !!}

    {!! Form::label('product_label', 'Price (With Tax)') !!}
    {!! Form::text('product_price', $price,["class"=>"form-control"]) !!}

    {!! Form::label('product_label', 'Tax(%)') !!}
    {!!Form::text('product_tax', $tax,["class"=>"form-control"]) !!}

    {!! Form::label('stock_code_label', 'Stock Code') !!}
    {!! Form::text('stock_code', $code,["class"=>"form-control"]) !!}

    {!! Form::label('product_label', 'Category') !!} <br/>

<select class="thechosen" id="category" name="category">

<option value="0">Choose Category</option>
    @foreach($cats as $cat)
<option {{ (isset($prod) && $prod->category_id ==$cat->id) ? ' selected="selected"' : '' }} value="{{$cat->id}}"> {{$cat->category_name}} </option>
    @endforeach

</select>
 


<select class="thechosen"  disabled="disabled" id="sub_category" name="sub_category">
</select>

    
    {!! Form::label('product_label', 'Description') !!}
    {!! Form::textarea('description', $desc,["class"=>"form-control"]) !!}


<br/>

{!!Form::submit('Save',['class'=>'btn btn-success'])!!}

{!! Form::Close() !!}




<script type="text/javascript">
    
    $(document).ready(function(){
        $("#category").change(function(){
            $("#sub_category").html("").attr("disabled","disabled");
            var selected_sub_cat = {{$sub_cat}};
            
            var id = parseInt($(this).val());

            $.ajax({
                url: "{{ action('ProductsSubCategoryController@ajaxGetSubCategories') }}?id="+id,
                method:"get",
                success:function(data){
                    var sub_cats = JSON.parse(data);

                    $.each(sub_cats,function(key,cat){
                       option =  $("<option value='"+cat.id+"'>"+cat.sub_category_name+"</option>");
                       if(cat.id==selected_sub_cat)
                       {
                        $(option).attr("selected","selected");
                       }
                       $("#sub_category").prepend(option);
                    });

                    $("#sub_category").removeAttr("disabled");
                    $("#sub_category").trigger("chosen:updated");
                }
            })
        })
    })

</script>


<?php

if(isset($prod)){

?>

<script type="text/javascript">
    $(document).ready(function(){
        $("#category").trigger('change');
       // $("#category").trigger("chosen:updated");
    })
</script>

<?php } ?>
@stop