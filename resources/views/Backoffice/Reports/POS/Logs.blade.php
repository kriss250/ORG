@extends("Backoffice.Master")

@section("contents")
<div class="page-contents">
    <div class="report-filter">
        <table style="width:100%">
            <tr>
                <td><h3>POS Logs</h3> </td>
                <td>
                    <form style="float:right" action="" class="form-inline" method="get">
                        <label>Date</label>
                        <input name="startdate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control"> -
                        <input name="enddate" type="text" value="{{ \ORG\Dates::$RESTODATE }}" class="date-picker form-control">
                        <label>Cashier</label>
                        <select name="cashier" class="form-control">
                            <option value="0">All</option>
                            <?php
                             $cashiers = \App\FX::GetCashiers();
                            ?>

                            @if(isset($cashiers))
                            @foreach($cashiers as $cashier)
                            <option value="{{ $cashier->id }}">{{$cashier->username}}</option>
                            @endforeach
                            @endif
                        </select>
                        <input type="submit" class="btn btn-success btn-sm" value="Go">
                        <button type="button" data-dates="{{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }} - {{ isset($_GET['startdate']) ? $_GET['startdate'] : date('d/m/Y',strtotime(\ORG\Dates::$RESTODT)) }}" data-title="POS Logs" class="btn btn-default report-print-btn">Print</button>
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
    <table class="table table-condensed table-striped table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>User</th>
                <th style="min-width:350px">Action</th>
            </tr>
        </thead>

        @if(isset($logs))

            @foreach($logs as $log) 
                <tr{!! ($log->type=='danger') ? " class='text-danger' " : "" !!}>
                    <td>{{$log->date}}</td>
                    <td>{{$log->user}}</td>
                    <td>{{$log->action}}</td>
                </tr>
            @endforeach
        @endif
    </table>
   
</div>

@stop
