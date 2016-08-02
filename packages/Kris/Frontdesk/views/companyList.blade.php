@extends("Frontdesk::MasterIframe")
@section("contents")

<div class="list-filter">
    <?php $wd = \Kris\Frontdesk\Env::WD(); ?>
    <form action="{{action("\Kris\Frontdesk\Controllers\OperationsController@frame","companies")}}" method="get" class="form-inline">
    
        <fieldset class="bordered" style="width:280px;">
            <label>Company Name</label>
            <input type="text" name="company_name" value="" class="form-control" placeholder="Name of the company" />
        </fieldset>


        <input type="submit" value="Find" class="btn btn-success btn-xs" />
    </form>
   
    <div class="clearfix"></div>
</div>

<div class="clearfix"></div>

<?php

$companies =  !isset($companies)  || is_null($companies) ? \Kris\Frontdesk\Company::orderBy("idcompanies","desc")->limit("20")->get() : $companies;
?>

<div class="list-wrapper">
    <p class="list-wrapper-title">
        <span>Company Database</span>
    </p>

    <table class="table table-bordered table-condensed data-table table-striped text-left">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Company Name</th>
                <th>Contact Person</th>
                <th>Phone</th>
                <th>Email</th>
                <th>
                    <i class="fa fa-eye"></i>
                </th>
            </tr>
        </thead>

        @if(!empty($companies))
    @foreach($companies as $company )
        <tr>
            <td>{{$company->idcompanies}}</td>
            <td>{{$company->name}}</td>
            <td>{{$company->contact_person}}</td>
            <td>{{$company->phone}}</td>
            <td>{{$company->email}}</td>
            <td>
                <a class="btn btn-xs btn-success" href="{{action('\Kris\Frontdesk\Controllers\StatementsController@company',$company->idcompanies)}}">
                    <i class="fa fa-file-o"></i>
                </a>
            </td>
        </tr>
    @endforeach
    @else
        <tr>
            <td colspan="9">
                No data
            </td>
        </tr>
        @endif
    </table>
</div>
@stop