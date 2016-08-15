@extends("Backoffice.Master")

@section("contents")
<div class="page-contents">
    <div class="report-filter">
        <table style="width:100%">
            <tr>
                <td><h3>Waiter Product Sales Count</h3> </td>
                <td>
                    <form style="float:right" action="" class="form-inline" method="get">
                        <label>Date</label>
                        <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control"> -
                        <input name="enddate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control">
                        <label>Waiter</label>
                        <select name="waiter" class="form-control">
                            <option value="0">All</option>
                            <?php
                             $waiters = \App\FX::GetWaiters();
                            ?>

                            @if(isset($waiters))
                            @foreach($waiters as $waiter)
                            <option value="{{ $waiter->idwaiter}}">{{$waiter->waiter_name}}</option>
                            @endforeach
                            @endif
                        </select>
                        <input type="submit" class="btn btn-success btn-sm" value="Go">
                        <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="Waiter Product Sales" class="btn btn-default report-print-btn">Print</button>
                    </form>
                </td>
            </tr>

            <tr>
                <td>
                    <p class="text-danger"><b>Date : {{ isset($_GET['startdate']) && isset($_GET['enddate'])  ?  \App\FX::Date($_GET['startdate'])." - ".\App\FX::Date($_GET['enddate']) : \App\FX::Date(\ORG\Dates::$RESTODATE) }}</b></p>
                </td>
            </tr>

        </table>


    </div>

   @foreach($data as $key=>$waiter)
    <h3>{{$key}}</h3>
    <table class="table table-condensed table-striped table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th class="text-right" width="10%">Quantity</th>
            </tr>
        </thead>

            @foreach($waiter as $item) 
                <tr>
                    <td>{{$item["item_name"]}}</td>
                    <td class="text-right">{{$item["qty"]}}</td>
                </tr>
            @endforeach
    </table>
   @endforeach
</div>

@stop
