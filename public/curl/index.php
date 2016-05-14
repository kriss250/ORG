<style type="text/css">
table {
  display: table;
  width:700px !important;
  box-sizing: padding-box;
  padding: 7px;
  margin-bottom: 10px;
  font-size: 14px;
  border-collapse: collapse;
}

table td:nth-child(4){
  color: blue;
  text-align: center
}

table td {
  padding: 6px;
  border: 1px solid gray;
}
table td:last-child {
  background: #21658A;
  color: #fff;
  font-weight: bold;
  border:none;
}
</style>
<table>
<?php
ini_set('max_execution_time', 1200);
//open connection
	$ch = curl_init();

for($i=300;$i<=410;$i++){

	$url = 'http://traffic.police.gov.rw/check_marks.php';
	$fields = array(
							'code' => "RL00$i",
							'form' => "",
	);

	$fields_string = "";
	//url-ify the data for the POST
	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	rtrim($fields_string, '&');

	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_POST, count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	//execute post
	$result = curl_exec($ch);
	$st =  strpos($result, "<td>Amanota</td></tr>");
	if($st < 1){
		continue;
	}

	$en = strpos($result, "</tr><tbody>");
	$string = substr($result, $st,$en-$st-5);
	echo str_replace("<td>Amanota</td></tr>", "", $string) ;
	flush();

	if($i==600){
		sleep(30);
	}

	if($i==830){
		sleep(35);
	}
	
}

?>

</table>