<?php

if(isset($_POST['sql'])){

    $data = DB::select("select room_number,type_name, concat_ws(' ',firstname,lastname) As Guest,checkin,checkout,concat_ws(',',adults,children) as PAX,rate_types.name,night_rate, balance_amount,due_amount,reservations.date from reservations
join reserved_rooms on reservation_id = idreservation
join rooms on room_id = idrooms
join room_types  on rooms.type_id = idroom_types
join guest on guest.id_guest = guest_in
join accounts on accounts.reservation_id = idreservation
join rate_types on rate_id = idrate_types
where reservations.status=1");

    $i = 1;
    $size = count($data);


    $columns = new stdClass();

    $columns->data="sdfsd";

    $obj = array("columns"=>array($columns),"data"=>$data);

    $v = get_object_vars($data);
    echo json_encode($obj);
}

?>


