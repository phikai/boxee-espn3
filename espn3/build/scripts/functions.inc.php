<?php
function url_exists($url) {
    // Version 4.x supported
    $handle = curl_init($url);
    if (false === $handle)
    {
        return false;
    }
    curl_setopt($handle, CURLOPT_HEADER, false);
    curl_setopt($handle, CURLOPT_FAILONERROR, true);  // this works
    curl_setopt($handle, CURLOPT_HTTPHEADER, Array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15") ); // request as if Firefox   
    curl_setopt($handle, CURLOPT_NOBODY, true);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
    $connectable = curl_exec($handle);
    curl_close($handle);  
    return $connectable;
}

function time_diff($eventtime){
//This will probably fail from 11:45 PM until sometime the next morning...but it shouldn't really matter cause live event table should be correct...
	$curtime = date('H:i');
	$curtime = explode(":", $curtime);
	$curtime_hours = $curtime[0] * 3600;
	$curtime_mins = $curtime[1] * 60;
	$curtimeSeconds = $curtime_hours + $curtime_mins + 600;
	
	$eventtime = explode(":", $eventtime);
	$eventtime_hours = $eventtime[0] * 3600;
	$eventtime_mins = $eventtime[1] * 60;
	$eventtimeSeconds = $eventtime_hours + $eventtime_mins;
	
	if($eventtimeSeconds <= $curtimeSeconds) {
		return "true";
	}
	else {
		return "false";
	}
}

?>