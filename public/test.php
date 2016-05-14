<?php
    
$link = new mysqli("localhost","root","klaxycomApps2014","orgdb2");
$s ="select idrooms,room_number from rooms where room_number='{$_GET['q']}' and room_number <>''";
$res  = $link->query($s);
echo $s;
echo $link->error;
if($d = $res->fetch_assoc())
{
    print_r($d);
}

"' or 1=1 ) union select 'TEXT INTO ','','','','','','','','','','','','';--"
?>