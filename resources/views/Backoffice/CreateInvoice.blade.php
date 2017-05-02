@extends('Backoffice.Master')

@section("contents")
<style>
    .invoice-items-table .form-control {
        border-color: transparent;
        height: 28px;
        font-size: 13px;
    }

     .invoice-table-wrapper  {
         padding:20px;
     }
    .invoice-items-table textarea.form-control {
        height:27px;
        resize:none;
        font-size: 13px;
    }

    .invoice-items-table td {
        position:relative
    }

    .row-del-btn {
        position:absolute;
        left:-23px;
        transition:all .6s;
        display:none
    }
    .invoice-items-table tr:hover .row-del-btn {
        display:block;
    }

    .row-del-btn::after {
        content: " ";
        display: block;
        position: absolute;
        right: -8px;
        top: 10%;
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 8px 0 8px 8px;
        border-color: transparent transparent transparent #d43f3a;
    }

    .date-input {
        border: 1px solid #ccc !important;
        font-size: 12px;
    }
</style>


<script>

    $(document).ready(function () {
        $(".page-contents").on("click",".date-picker-toggle",function () {
            $(this).parent().children("input.form-control").datepicker({ autoclose: true, forceParse: false }).focus();
        });

        $(".page-contents").on("click",".row-del-btn",function (e) {
            e.preventDefault();
            delRow($(this).parent().parent(), typeof $(this).attr("has-data")!="undefined");
        });
    });

    var delRow = function (rowSelectorObj, delData) {
        if (delData && !confirm("Are you sure you want to remove this row ?")) return;

        var rowsInTable = $(".invoice-items-table tr").length-1; //remove the heade tr
        var isLastRow = (rowsInTable == $(rowSelectorObj).index() + 1);

        //update all rows below it
        if (!isLastRow)
        {
            startRowIndex = $(rowSelectorObj).index()+1; // the next row after the one to be deleted
            rowsToUpdate = rowsInTable - startRowIndex ;
            x = startRowIndex;
            

            for (var i = 1; i <= rowsToUpdate;i++)
            {
                currentClass = "row_" + (x+1);
                currentNode = $(".invoice-items-table ." + currentClass);
                
               
                $(currentNode).find(".date-input").attr("name", "date_" + x);

                $(currentNode).find(".form-control.desc-input").attr({
                    "placeholder": "Description Item " + x,
                    "name": "desc_" + x,
                });

                $(currentNode).find(".qty-input").attr("name", "qty_" + x);
                $(currentNode).find(".price-input").attr("name", "price_" + x);
                $(currentNode).find(".days-input").attr("name", "days_" + x);
              
                currentNode.addClass("row_" + x);
                currentNode.removeClass(currentClass)
                x++;
            }

            

            
        }

        $(rowSelectorObj).remove();

       
    };

  var addRow = function (ev)
  {
    ev.preventDefault();
    var table = $(".invoice-items-table");
    var currentRows  = $(".invoice-items-table tbody").children("tr").length;
    var nextNo = currentRows+1;

    var row = $("<tr>");
    var col = $("<td>");

    var dateGroup = $('<div class="input-group">');
    var dateGroupAddon = $('<span style="font-size:13px;cursor:pointer;padding:5px" class="date-picker-toggle input-group-addon"><i class="fa fa-calendar"></i></span>');
    var dateInput = $('<input name="date_'+nextNo+'" class="form-control date-input" type="text" placeholder="Y-m-d" />');
    var descInput = $('<input type="text" data-table="org_backoffice.invoice_items" data-field="description" name="desc_'+nextNo+'" rows="1" class="desc-inputdesc-input desc-input form-control suggest-input" placeholder="Description Item '+nextNo+'" />');
    var qtyInput = $('<input required min="1" name="qty_'+nextNo+'" type="number" class="form-control qty-input" />');
    var upInput = $('<input required name="price_' + nextNo + '" class="form-control price-input" type="text" placeholder="Price #" />');
    var daysInput = $('<input required min="1" value="1" name="days_' + nextNo + '" type="number" class="form-control days-input" />')
    var delBtn = $(' <button type="button" class="row-del-btn btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>');

    $(dateGroup).html(dateInput).append(dateGroupAddon);

    $(row).addClass("row_"+nextNo)
    .append($("<td>").html(dateGroup).prepend(delBtn))
    .append($("<td>").html(descInput))
    .append($("<td>").html(daysInput))
    .append($("<td>").html(qtyInput))
    .append($("<td>").html(upInput));

  $(table).append(row);

  }
</script>
<div class="page-contents">
    <h2>Create Invoice</h2>

    <form method="post" action="{{action("InvoiceController@store")}}">
    <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="row">
            <div class="col-xs-5">
                <label>Institution</label>
                <input data-table="org_backoffice.debtors" autocomplete="off" data-field="name" required class="form-control suggest-input" type="text" name="company" placeholder="Company Name / Individual" />
                <label>
                    Address / Contacts
                </label>
                <textarea name="address" required class="form-control"></textarea>
            </div>

            <div class="col-xs-5">
                <label>Code</label>
                <div style="width:150px" class="input-group">
                    <input class="form-control" value="{{((1)+\App\Invoice::LastInvoiceID())}}" name="code" />
                    <span class="input-group-addon">{{date("Y")}}</span>
                </div>
                <label>Due Date</label>
                <input autocomplete="off" required name="due_date" type="text" style="display:block;max-width:105% !important;width:41% !important" class="form-control date-picker" />

                <label>Prestation / Description</label>
                <input autocomplete="off" required name="description" type="text" style="display:block;max-width:none;width:100% !important" class="form-control" />

            </div>
        </div>
        <hr />
        <label>Invoice Items</label>
        <div class="invoice-table-wrapper">
            <table class="invoice-items-table table-bordered table table-condensed table-striped">
                <thead>
                    <tr>
                        <th width="15%">Date</th>
                        <th style="width:50%">Description</th>
                        <th width="8%">
                            Days
                        </th>
                        <th width="8%">Qty</th>
                        <th>Unit Price</th>
                    </tr>
                </thead>

                @for($i=1;$i<2;$i++)
                <tr class="row_{{$i}}">

                    <td>
                        <button type="button" class="row-del-btn btn btn-danger btn-xs">
                            <i class="fa fa-trash"></i>
                        </button>
                        <div class="input-group">
                            <input style="border:1px solid #ccc;font-size:12px;" name="date_{{$i}}" class="form-control date-input" type="text" placeholder="Y-m-d" />
                            <span style="font-size:13px;cursor:pointer;padding:5px" class="date-picker-toggle input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                        </div>
                    </td>

                    <td>
                        <input autocomplete="off" required type="text" data-table="org_backoffice.invoice_items" data-field="description" name="desc_{{$i}}" rows="1" class="form-control suggest-input esc-input" placeholder="Description Item {{$i}}" />
                    </td>

                    <td>
                        <input required min="1" value="1" name="days_{{$i}}" type="number" class="form-control days-input" />
                    </td>

                    <td>
                        <input required min="1" name="qty_{{$i}}" type="number" class="form-control qty-input" />
                    </td>

                    <td>
                        <input required name="price_{{$i}}" class="form-control price-input" type="text" placeholder="Price #" />
                    </td>
                </tr>
                @endfor
            </table>
        </div>
        <button onclick="addRow(event)" style="margin-top:-10px;display:block;margin-bottom:20px;" class="btn btn-success btn-xs"><i class="fa fa-plus"></i> Add row</button>
        <input type="submit" value="Save Invoice" class="btn btn-primary" />
    </form>
</div>

@stop
