@extends("Backoffice.Master")

@section("contents")
<div class="page-contents">

<div class="report-filter">
<table style="width:100%">
    <tr>
        <td><h3>Stock Sales </h3> </td>
        <td>
         <form style="float:right" action="" class="form-inline" method="get">
                <label>Date</label> 
                <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control"> - 
                <input name="enddate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control">
        
                <input type="submit" class="btn btn-success btn-sm" value="Go">
                 <button type="button" data-dates="{{ isset($_GET['date']) ? $_GET['date'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Stock Sales Report" class="btn btn-default report-print-btn">Print</button>
           </form>  
        </td>
    </tr> 
</table>

</div>

@if(isset($data) && count($data) > 0)
    <table class="table table-bordered table-stripped">
        <thead>
            <tr>
                <th>Reference No.</th>
                <th>Biller</th>
                <th>Done By</th>
                <th>Amount</th>
                <th>Note</th>
                <th>Date</th>
                <th>Open</th>
            </tr>
        </thead>
        @foreach($data as $item)
            <tr>
                <td>{{$item->reference_no}}</td>
                <td>{{$item->biller_name }}</td>
                <td>{{$item->user }}</td>
                <td>{{number_format($item->total) }}</td>
                 <td>{!! html_entity_decode($item->note) !!}</td>
                <td>{{$item->date }}</td>
                <td><a class="modal-btn" data-width="620" href="{{ action("BackofficeReportController@index","saleItems")}}?id={{$item->id}}"><i class="fa fa-expand"></i></a></td>
            </tr>
        @endforeach
    </table>
@endif


</div>

@stop