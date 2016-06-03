<div class="print-footer">
    Printed by {{ \Session::has("fo_user") ? Session::get("fo_user") :  \Auth::user()->username }} , on {{ date('d/m/Y H:i:s') }}
</div>