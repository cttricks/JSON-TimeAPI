<?php
$response = array("code"=>401,"msg"=>"unknown location");
if(isset($_GET['q']) && $_GET['q'] !==""){
	if(count(explode('/',$_GET['q']))>1){
		$timeZones = json_decode(file_get_contents('timezone.json', true));
		$given_Zone = format_location_name($_GET['q']);
		if(in_array($given_Zone,$timeZones)){
			date_default_timezone_set($given_Zone);
			$time = time();
			$response = array(
				"code" => 200,
				"abbreviation" => "IST",
				"client_ip" => get_client_ip(),
				"timezone" => $given_Zone,
				"day" => gmdate("l"),
				"day_of_year"=> gmdate("z") +1,
				"day_of_week" => gmdate("w") + 0,
				"week_number" => gmdate("W") + 0,
				"date" => date('d/m/Y', $time),
				"time" => date('h:i:s a', $time),
				"unixtime" => gmdate("U") +0
			);
		}
	}else{
		$allTimeZones = json_decode(file_get_contents('timezone.json', true));
		$givenZone = explode('/',$_GET['q'])[0];
		$zone_list = array();
		foreach ($allTimeZones as $zones){
			if(strpos(strtolower($zones), strtolower($givenZone)) !== false){
				array_push($zone_list, $zones);
			}
		}
		
		if(count($zone_list)>0){
			$response = array(
				"code" => 200,
				"time_zones" => $zone_list
			);
		}
		
	}
}else{
	$response = array(
		"code" => 200,
		"time_zones" => json_decode(file_get_contents('timezone.json', true))
	);
}

echo  json_encode($response);

/*Format Location Name*/
function format_location_name($name){
	$data = explode('/',$name);
	$newName = "";
	for($i=0; $i<count($data); $i++){
		if($newName===""){
			$newName = ucfirst(strtolower($data[$i]));
		}else{
			$newName .= "/" . ucfirst(strtolower($data[$i]));
		}
	}
	
	return $newName;
}

/*Function to get the client IP address*/
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
?>
