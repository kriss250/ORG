<?php $prop = \App\Resto::get()->first(); ?>
<table style="width:100%">
<tr>
    <td style="width:85px;padding-right:15px">
        <img src="/uploads/images/{{$prop->logo_image}}" width="100" />
    </td>
    <td>
        <h4 style="margin-bottom:0">Report</h4>
        {{$prop->resto_name}}<br />
        {{$prop->website}}
    </td>

    <td class="text-right">
    <b>Date ...../...../.......</b><br>
       Report Date : <b>
                         <?php


                            $dates = explode(" - ",$_GET['date_range']);
                            
                            if(count($dates)>1){
                                if($dates[0]==$dates[1])
                                {
                                    echo $dates[0];
                                }else {
                                    echo $_GET['date_range'];
                                }
                            }else {
                                echo $_GET['date_range'];
                            }

                         ?>
        </b> <br />
       User :   {{  \Auth::user()->username }}

    </td>

</tr>
</table>

<div style="border:2px dashed #000;padding:5px;text-align:center;margin-top:6px;margin-bottom: 10px">
    <h4 style="margin:0;font-size:16px;font-weight:bold;padding:0;text-transform:uppercase">{{ $_GET['title'] }}</h4>
</div>