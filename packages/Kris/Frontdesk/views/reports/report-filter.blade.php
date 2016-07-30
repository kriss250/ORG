
<div class="report-filter">

    <div style="width:100%;max-width:980px;margin:auto" class="row">
        <div class="col-xs-4">
            <h4>Frontdesk Reports</h4>
        </div>

        <div class="col-xs-8">
            <form style="float:right" action="" class="form-inline" method="get">
                <label>Date</label>
                <input name="startdate" type="text" value="{{ \Kris\Frontdesk\Env::WD()->format("Y-m-d")}}" class="date-picker form-control" />-
                <input name="enddate" type="text" value="{{\Kris\Frontdesk\Env::WD()->format("Y-m-d")}}" class="date-picker form-control" />
                <input type="submit" class="btn btn-success btn-sm" value="Go" />
                <button type="button" onclick="window.print()" class="btn btn-default report-print-btn">Print</button>
            </form>
        </div>
    </div>
</div>