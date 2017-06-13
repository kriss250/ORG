@extends('Backoffice.Master')

@section("contents")

<div class="page-contents">
    <div class="report-filter">
        <table style="width:100%">
            <tr>
                <td><h3>Bill Finder</h3> </td>
                <td>

                    <form style="float:right" action="" class="form-inline" method="get">
                        <label>Firstname</label>
                        <input size="10" name="firstname" type="text" placeholder="Firstname" value="{{isset($_GET['firstname']) ?$_GET['firstname']:""}}" class="form-control" />

                        <label>Lastname</label>
                        <input size="10" name="lastname" type="text" placeholder="Lastname" value="{{isset($_GET['lastname'])?$_GET['lastname']:""}}" class="form-control" />

                        <label>Date</label>
                        <input name="startdate" type="text" value="{{ isset($_GET['startdate']) ? $_GET['startdate'] : \ORG\Dates::$RESTODATE }}" class="date-picker form-control" />-
                        <input name="enddate" type="text" value="{{isset($_GET['enddate']) ? $_GET['enddate'] :  \ORG\Dates::$RESTODATE }}" class="date-picker form-control" />
                        <input type="submit" name="find" class="btn btn-success btn-sm" value="Find" />
                       
                    </form>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="text-danger"><b>Date : {{isset($_GET['startdate']) && isset($_GET['enddate'])  ?  \App\FX::Date($_GET['startdate'])." - ".\App\FX::Date($_GET['enddate']) : \App\FX::Date(\ORG\Dates::$RESTODATE) }}</b></p>
                </td>
            </tr>
        </table>
    </div>
  
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Bill ID</th>
                <th>Guest Names</th>
                <th>Company</th>
                <th>Checkin</th>
                <th>Checkout</th>
                <th>Due Amount</th>
                <th>Balance</th>
                <th>Open</th>
            </tr>
        </thead>
        <?php $dues = 0 ; $balance=0; ?>
        @foreach($bills as $bill)
        <?php $dues += $bill->due_amount;$balance += $bill->due_amount-$bill->paid_amount; ?>
        <tr>
            <td>{{$bill->idreservation}}</td>
            <td>
                {{$bill->guest != null ? $bill->guest->firstname." ".$bill->guest->lastname : ""}}
            </td>
            <td>
               
            </td>
            <td>{{$bill->checkin}}</td>
            <td>{{$bill->checkout}}</td>
            <td>{{number_format($bill->due_amount)}}</td>
            <td>{{number_format($bill->due_amount-$bill->paid_amount)}}</td>
            <td>
                <a class="btn btn-xs btn-success" onclick="return window.open('{{action("CustomersController@printBill",$bill->idreservation)}}?type=standard','','width=920,height=620',this)" href="#"><i class="fa fa-eye"></i></a>
            </td>
        </tr>
        @endforeach
<tfoot>
    <tr>
        <th colspan="5">TOTAL</th>
        <th>{{number_format($dues)}}</th>
        <th>{{number_format($balance)}}</th>
        <th></th>
    </tr>
</tfoot>
    </table>
</div>

@stop