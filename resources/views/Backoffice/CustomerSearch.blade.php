@extends('Backoffice.Master')

@section("contents")

<div class="page-contents">
    <div class="report-filter">
    <table style="width:100%">
        <tr>
            <td><h3>Customer Database / POS & F.O</h3> </td>
            <td>

               <form style="float:right" action="" class="form-inline" method="get">
                    <label>Company : </label>
                   <input style="background:#fff" class='form-control' type="text" name="company" placeholder="Company Name" />
                    
                    <label>Guest : </label>
                   <input style="background:#fff" class='form-control' placeholder="Firstname and Lastname" type="text" name="name" />

                   <input type="submit" class="btn btn-success btn-sm" value="Search" />
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
    
    @if($foc==null && $fog==null && $pos==null)
        <h3>Please enter customer name or company name</h3>
    @else 

    <h4>FO Customers</h4>
    <table class="table table-condensed table-bordered">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Email</th>
                <th>Phone</th>
                <th>ID / DOC</th>
                <th>Action</th>
            </tr>
        </thead>
        @if($fog !=null )
        @foreach($fog as $focustomer)
            <tr>
                <td>{{$focustomer->id_guest}}</td>
                <td>{{$focustomer->firstname}}</td>
                <td>{{$focustomer->lastname}}</td>
                <td>{{$focustomer->email}}</td>
                <td>{{$focustomer->phone}}</td>
                <td>{{$focustomer->id_doc}}</td>
                <td class="text-right"><a href="{{action("StatementController@ShowStatement",["fo",$focustomer->id_guest,"null",$focustomer->firstname])}}" class="btn btn-default btn-xs">Statement</a></td>
            </tr>
        @endforeach
       @endif
        <thead>
            <tr>
                <th class="text-center" colspan="7">Companies</th>
            </tr>
            <tr>
                <th>ID#</th><th colspan="2">Company</th><th>Email</th><th>Phone</th><th colspan="3">ACTION</th>
            </tr>
        </thead>
        @if($foc !=null )
        @foreach($foc as $company)
            <tr>
                <td>{{$company->idcompanies}}</td>
                <td colspan="2">{{$company->name}}</td>
                <td>{{$company->email}}</td>
                <td>{{$company->phone}}</td>
                
                <td class="text-right" colspan="3"><a href="{{action("StatementController@ShowStatement",["fo",$company->idcompanies,$company->name,"null"])}}" class="btn btn-default btn-xs">
<i class="fa fa-file" aria-hidden="true"></i> Statement</a></td>
            </tr>
        @endforeach
        @endif
    </table>
    @endif

    @if($pos!=null)
    <h4>POS Customers</h4>
    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th>Customer Name</th>
                <th>Action</th>
            </tr>
        </thead>

        @foreach($pos as $poscustomer)
        <tr>
            <td>{{$poscustomer->customer}}</td>
            <td class="text-right">
                <a href="{{action("StatementController@ShowStatement",["pos","0",$poscustomer->customer,"null"])}}" class="btn btn-default btn-xs"><i class="fa fa-file" aria-hidden="true"></i> Statement</a>
            </td>
        </tr>
        @endforeach
    </table>
    @endif
</div>
      
@stop