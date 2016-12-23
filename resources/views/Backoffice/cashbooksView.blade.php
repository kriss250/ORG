@extends('Backoffice.Master')

@section("contents")

@if (session('status'))
    <div class="alert alert-success">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ session('status') }}
    </div>
@endif


@if (session('error'))
    <div class="alert alert-danger">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{session('error') }}
    </div>
@endif



<div class="col-md-12" style="padding:28px;background:#Fff">

        <h3 class="text-center" style="margin-bottom:2px;">Cash Books</h3> 

        <?php
        $_cashbooks =  DB::connection("mysql_backoffice")->select("SELECT * FROM cash_book order by cashbookid desc");
        ?>

        <p class="text-center">Cashbooks Balance</p>

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Cashbook</th>
                <th>Balance</th>
                <th>Action</th>
            </tr>
        </thead>

        @foreach($_cashbooks as $book)
            <tr>
                <td>
                    {{ $book->cashbook_name }}
                </td>
                <td class="text-right">
                    {{ number_format($book->balance) }}
                </td>
                		 	<td class="text-right"><a class="btn btn-xs btn-success" href="{{ action("CashbookController@show",$book->cashbookid) }}" style="font-size:12px;">Open</a>

            </tr>
        @endforeach
    </table>

    <div class="clearfix"></div>

    

</div>



<div class="clearfix"></div>
@stop