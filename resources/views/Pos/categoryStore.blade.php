@extends("Pos.master")

@section("contents")



<h2>Category to Store</h2>
<p>Assign categories to store</p>
<form method="post" action="{{action("ProductsController@setCategoryStore")}}"><input type="hidden" name="_token" value="{{csrf_token()}}" />
    <table class="table">

        <tr>
            <td>
                <label>Category</label>
                <select name="category">
                    @foreach(\App\Category::all() as $cat)
                    <option value="{{$cat->id}}">{{$cat->category_name}}</option>
                    @endforeach
                </select>
            </td>

            <td>
                <label>Store</label>
                <select name="store">
                    @foreach(\App\Store::all() as $store)
                    <option value="{{$store->idstore}}">{{$store->store_name}}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="submit" value="Add to Store" class="btn btn-success" />
            </td>
        </tr>
    </table>
</form>
Category to Store
<hr />

<table class="table table-bordered table-condensed table-striped">
    <thead>
        <tr>
            <th>
                Category
            </th>
            <th>
                Store
            </th>
            <th>Remove</th>
        </tr>
    </thead>

    @foreach(\App\CategoryStore::all() as $catSt)
        <tr>
            <td>
                {{$catSt->category->category_name}}
            </td>
            <td>
                {{$catSt->store->store_name}}
            </td>
            <td>
                <a style="border-radius:8px" class="btn btn-danger" href="{{action("ProductsController@removeCatStore",$catSt->category->id)}}/{{$catSt->store->idstore}}"><i class="fa fa-trash"></i></a>
            </td>
        </tr>
    @endforeach
</table>

@stop