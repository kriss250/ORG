@extends("Frontdesk::Master")

@section("contents")
<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
<?php
$all_floors ="";
$current_floor = "";
$next_floor = "";
$floor = "";
$floors_li = "";

$rooms_no = count($data);

//print floor when all rooms are appended
$space_init = 1;
$newln = 1;
for($i=0;$i<$rooms_no;$i++)
{
    $current_floor= $data[$i]->floors_id;
    $next_floor = isset($data[$i+1]->floors_id) ?  $data[$i+1]->floors_id : 0;
    $url = action("\Kris\Frontdesk\Controllers\OperationsController@roomView",$data[$i]->idreservation);

   $show_names = strtolower($data[$i]->status_name) =='occupied' || strtolower($data[$i]->status_name) =='reserved';

   $floor .= "<div data-placement='top' data-toggle='tooltip' ".($show_names ? "title='GUEST : {$data[$i]->guest}'" : "")."".($show_names ?  "onclick=\"window.openDialog('{$url}','Room','width=800,height=590,resizable=no',this)" :  '')."\" class='black-tooltip room_item ".strtolower($data[$i]->status_name).($space_init == 5 ?  ' room_space' : '' )."'>
                         <p>".$data[$i]->room_number."</p>
                         <span> ".substr($data[$i]->type_name,0,9)." </span>
                     </div>";

    if($next_floor!=$current_floor){
        $floors_li .="<li><a data-floor='".$data[$i]->floors_id."' class='floor-nav-item' href='#'>".$data[$i]->floor_name."</a></li>";
        $all_floors .="<li class='floor-li floor_".$data[$i]->floors_id."'>".$floor."</li>";
        $floor = "";
        $next_floor =-1;
    }

    if($space_init==5 || ($newln%14)==0)
    {
        $space_init = 1;
    }else {
        $space_init++;
    }

    $newln++;
}

?>
<script>
    $(document).ready(function () {
        $(".floor-nav-item").click(function (e) {
            e.preventDefault();
            var floor = parseInt($(this).attr("data-floor"));
            $(".active-floor").removeClass("active-floor");
            $(this).parent("li").addClass("active-floor");
            if(floor > 0) {
                $(".floor-li").hide();
                $(".floor_" + floor).show();
            }else {
                $(".floor-li").show();
            }
        })

        $(".status_filter").click(function (e) {
            e.preventDefault();

            var className = $(this).attr("data-target");
            $(".room_item").hide();
            $("." + className).show();

            if (className == "all") $(".room_item").show();
        })
    })
</script>

<div class="room-view-wrapper">

    <div style="margin-top:-20px;" class="row">
        <div class="col-sm-7">
            <h3 style="font-family:'Eras ITC';font-weight:600;color:#3E3E3E">
                 Floors View
                <b style="font-size:12px;color:#858585;font-weight:normal;display:block">Rooms arranged by floors</b>
            </h3>
        </div>

        <div class="col-sm-5">

            <div class="room_status_list">

                <div  class="col-md-4 text-right">
                    <ul style="background:none !important;box-shadow:none!important;border:none">
                        <li style="color: #191919;">
                            <a href="#" data-target="all" class="status_filter">
                                <i class="fa fa-heart"></i>All
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="col-md-4">
                    <ul class="">
                        <li style="color: #2cae00">
                            <a href="#" data-target="vacant" class="status_filter">
                                <i class="fa fa-circle"></i>Vacant
                            </a>
                        </li>
                        <li style="color: #b72626">
                            <a href="#" data-target="occupied" class="status_filter">
                                <i class="fa fa-circle"></i>Occupied
                            </a>
                        </li>
                        <li style="color: #9f9f9f">
                            <a href="#" data-target="dirty" class="status_filter">
                                <i class="fa fa-circle"></i>Dirty
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="col-md-4">
                    <ul>
                        <li style="color: #24acd8">
                            <a href="#" data-target="reserved" class="status_filter">
                                <i class="fa fa-circle"></i>Reserved
                            </a>
                        </li>
                        <li style="color: #191919">
                            <a href="#" data-target="blocked" class="status_filter">
                                <i class="fa fa-circle"></i>Blocked
                            </a>
                        </li>
                        <li style="color: #e46121">
                            <a href="#" data-target="house" class="status_filter">
                                <i class="fa fa-circle"></i>House Use
                            </a>
                        </li>

                    </ul>


                </div>



                <div class="clearfix"></div>
            </div>
        </div>

    </div>
    <ul class="floors-nav">
        <li class="active-floor">
            <a class="floor-nav-item" data-floor="0" href="#">All</a>
        </li>
        {!! $floors_li !!}
    </ul>
    <div class="inner-room-view">
        <ul class="all-floors">
            {!! $all_floors !!}
        </ul>
    </div>
</div>
@stop