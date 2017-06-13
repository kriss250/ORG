<style>
    .modal-window {
        height:auto !important
    }
</style>
<script>
    function Objproto () { };
    var fbitems = [];
    var foitems = [];

    Objproto.prototype.code = null;
    Objproto.prototype.date = null;
    Objproto.prototype.customer = null;
    Objproto.prototype.checkin = "";
    Objproto.prototype.checkout = "";
    Objproto.prototype.type = "";
    Objproto.prototype.amount = 0;
    
    function showData() {
        $("#dbTtable tbody").html("");
        $.get('{{action("InvoiceController@getDebts")}}', { 'startdate': $("#stdate").val(), 'enddate': $("#endate").val() }, function (data) {

            if (typeof data !== "object") data = JSON.parse(data);
            var tr = null;
            if (typeof data.data !== "undefined") {
                var x = 0;
                $.each(data.data, function (fbk, fbv) {
                    objproto = new Objproto();
                    objproto.code = fbv.idbills;
                    objproto.date = fbv.date;
                    objproto.customer = fbv.customer;
                    objproto.checkin = "";
                    objproto.checkout = "";
                    objproto.type = "fb";
                    objproto.amount = fbv.bill_total;

                    tr = $("<tr>");
                    $(tr).html("<td><input class='cr_check' type='checkbox' value='fb_" + x + "' /></td>");
                    $(tr).append("<td>" + objproto.code + "</td>");
                    $(tr).append("<td>" + objproto.date + "</td>");
                    $(tr).append("<td>" + objproto.customer + "</td>");
                    $(tr).append("<td>" + objproto.checkin + "</td>");
                    $(tr).append("<td>" + objproto.checkout + "</td>");
                    $(tr).append("<td>" + objproto.type + "</td>");
                    $(tr).append("<td>" + objproto.amount + "</td>");
                    $("#dbTtable").append($(tr));

                    fbitems.push(objproto);
                    x++;
                });


            }

            if (typeof data.fo_data !== "undefined") {
                var x = 0;
                $.each(data.fo_data, function (fok, fov) {
                    objproto = new Objproto();
                    objproto.code = fov.idreservation;
                    objproto.date = fov.checkin;
                    objproto.customer = fov.payer;
                    objproto.checkin = fov.checkin;
                    objproto.checkout = fov.checkout;
                    objproto.type = "fo";
                    objproto.amount = fov.dues;

                    foitems.push(objproto);

                    tr = $("<tr>");
                    $(tr).html("<td><input class='cr_check' type='checkbox' value='fb_" + x + "' /></td>");
                    $(tr).append("<td>" + objproto.code + "</td>");
                    $(tr).append("<td>" + objproto.date + "</td>");
                    $(tr).append("<td>" + objproto.customer + "</td>");
                    $(tr).append("<td>" + objproto.checkin + "</td>");
                    $(tr).append("<td>" + objproto.checkout + "</td>");
                    $(tr).append("<td>" + objproto.type + "</td>");
                    $(tr).append("<td>" + objproto.amount + "</td>");
                    $("#dbTtable").append($(tr));

                    fbitems.push(objproto);
                    x++;
                });

            }


        });
    }

    function addToInvoice() {
        items = $(".cr_check:checked");
        if (items.length == 0) { alert("Please select atleast one item"); return; }
    }
</script>
<div class="form-inline">
    <input type="text" class="form-control date-picker" value="{{date("Y-m-d")}}" id="stdate" />
    <input type="text" class="form-control date-picker" value="{{date("Y-m-d")}}" id="endate" />
    <button type="button" class="btn btn-primary" onclick="showData();">Fetch</button>
</div>
<br />
<button onclick="addToInvoice()" class="btn btn-success">Add To Invoice</button>

<hr />
<table id="dbTtable" class="table table-condensed table-striped">
   <thead>
       <tr>
           <th></th>
           <th>Code</th>
           <th>Date</th>
           <th>Customer</th>
           <th>Checkin</th>
           <th>Checkout</th>
           <th>Type</th>
           <th>Amount</th>
       </tr>
   </thead>
</table>