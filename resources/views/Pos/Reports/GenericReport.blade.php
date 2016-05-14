@extends("Pos.master")

@section("printHeader")

@include("layouts/ReportHeader")

@stop

@section("contents")

<script type="text/javascript">
$(document).ready(function(){
    var jData = JSON.parse('{!! $data !!}');
    var colNames = Object.keys(jData[0]);
    var colString = "";
    for(i=0;i<colNames.length;i++){
        end = (i==colNames.length-1) ? '' : ',';
        colString +='{"data":"'+colNames[i]+'"}'+end;
    }

colString = "["+colString+"]";

 $('#table').dataTable({
        "language": {
            "decimal": ".",
            "thousands": ","
        },
        "iDisplayLength":25,
         "data": jData,
        "columns": JSON.parse(colString),
		"sDom": 'T<"clear">lfrtip',
		 "tableTools": {
            "sSwfPath": "/assets/js/vendor/datatables/extensions/tableTools/swf/copy_csv_xls_pdf.swf",
            "aButtons": [
                    "copy",
                    "csv",
                    "xls",
                    {
                        "sExtends": "pdf",
                        "sTitle": "{{ $title }}",
                        "sPdfMessage": "Summary Info",
                        "sPdfOrientation": "landscape"                        
                    },
                    "print"
                ]
        },
        "order": [[ 0, "desc" ]]
        {!!$footer!!}
	});


})


	
</script>

<h2>{{$title}}</h2>
<p class="page_info"><i class="fa fa-info-circle"></i> Please use the table below to navigate or filter the results. You can download the table as csv, excel and pdf.</p>

<button class="filter_btn">Show Filter</button>
<div class="clearfix"></div>
<table id="table" class="display" cellspacing="0" width="100%">
	<thead>
		<tr style="font-size:12px !important">
			@foreach($columns as $column)

            <th>{{$column}}</th>

            @endforeach
		</tr>
	</thead>
<tfoot>
<?php echo str_repeat("<th></th>", count($columns)); ?>
</tfoot>
</table>

<style>
    .DTTT_Print .footer-table {
        display:block !important
    }
    .footer-table {
        display:block;
       width:100%;
    }
    .footer-table td {
        padding:10px 15px;
        border-right:1px solid #ccc;
    }

    .footer-table td strong {
        font-size:15px;
        padding-bottom:10px;
        display:block
    }
</style>
<br />
<table class="footer-table" style="width:100%">
    <tr>
        <td valign="top">
            <strong>Room POST</strong>
<?php 

if(isset($more) && count($more['Rooms']) > 0){
    foreach($more['Rooms'] as $room){
?>

<p>{{ $room->room }} : {{ number_format($room->bill_total) }} Rwf &nbsp; [ID #{{ $room->idbills }}]</p>

<?php }
} ?>
            </td>

        <td valign="top">
            <strong>Credits</strong>
            <?php if(isset($more) && count($more['Credits']) > 0){
    foreach($more['Credits'] as $credit){ ?>
            <p>{{ $credit->customer }} : {{ number_format($credit->bill_total) }} Rwf &nbsp; [ID #{{ $credit->idbills }}]</p>
        
        <?php }} ?>
        </td>
        </tr>
    </table>

@stop