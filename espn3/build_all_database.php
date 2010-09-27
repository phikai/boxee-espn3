<?php

//MySQL Connection Information
include('/home/phikai/boxee.thinkonezero.com/mysql_connect.inc.php');

//MySQL Connection
mysql_connect($server,$username,$password);
@mysql_select_db($database) or die("Unable to select database");

//Set XML Errors Off
libxml_use_internal_errors(true);

//Get the XML Source of ESPN3 Live Feed
$xml = file_get_contents('http://sports-ak.espn.go.com/espn3/feeds/live');
$enc = mb_detect_encoding($xml);
$xml = mb_convert_encoding($xml, 'UTF-8', $enc);
$e3_live_xml = new SimpleXMLElement($xml);
unset($xml);
unset($enc);

//Live Event Handling
foreach($e3_live_xml->event as $event) {
	foreach($event as $event_id) {
		$event_id_num = $event['id'];
		$raw['idnum'] = trim($event_id_num);
	}
	unset($event_id);
	foreach($event->name as $event_name) {
		$raw['event'] = trim($event_name);
	}
	unset($event_name);
	foreach($event->league as $event_league) {
		$raw['league'] = trim($event_league);
	}
	unset($event_league);
	foreach($event->sport as $event_sport) {
		$raw['sport'] = trim($event_sport);
	}
	unset($event_sport);
	foreach($event->startTime as $event_time) {
		$time = trim($event_time);
		$raw['date'] = date('Y-m-d', strtotime($time));
		$raw['time'] = date('g:i A', strtotime($time));
	}
	unset($event_time);
	foreach($event->thumbnail->large as $event_thumb) {
		$raw['thumb'] = trim($event_thumb);
	}
	unset($event_thumb);
   	
  //Sanitize the Variables before MySQL Query
	foreach($raw as $key => $val){
		$safe[$key] = mysql_real_escape_string($val);
	}
   	
	//MySQL Query for Each Item
	$query = "INSERT INTO e3_live VALUES('', NOW(), DATE('{$safe['date']}'), '{$safe['idnum']}', '{$safe['event']}', '{$safe['league']}', '{$safe['sport']}', '{$safe['time']}', '{$safe['thumb']}')";
	mysql_query($query);

}

//Unset XML to Free Memory
unset($e3_live_xml);

//Get the XML Source of ESPN3 Replay Feed
$xml = file_get_contents('http://sports-ak.espn.go.com/espn3/feeds/replay');
$enc = mb_detect_encoding($xml);
$xml = mb_convert_encoding($xml, 'UTF-8', $enc);
$e3_replay_xml = new SimpleXMLElement($xml);
unset($xml);
unset($enc);

//Replay Event Handling
foreach($e3_replay_xml->event as $event) {
	foreach($event as $event_id) {
		$event_id_num = $event['id'];
		$raw['idnum'] = trim($event_id_num);
	}
	unset($event_id);
	foreach($event->name as $event_name) {
		$raw['event'] = trim($event_name);
	}
	unset($event_name);
	foreach($event->league as $event_league) {
		$raw['league'] = trim($event_league);
	}
	unset($event_league);
	foreach($event->sport as $event_sport) {
		$raw['sport'] = trim($event_sport);
	}
	unset($event_sport);
	foreach($event->startTime as $event_time) {
		$time = trim($event_time);
		$raw['date'] = date('Y-m-d', strtotime($time));
		$raw['time'] = date('g:i A', strtotime($time));
	}
	unset($event_time);
	foreach($event->thumbnail->large as $event_thumb) {
		$raw['thumb'] = trim($event_thumb);
	}
	unset($event_thumb);
   	
  //Sanitize the Variables before MySQL Query
	foreach($raw as $key => $val){
		$safe[$key] = mysql_real_escape_string($val);
	}
   	
	//MySQL Query for Each Item
	$query = "INSERT INTO e3_replay VALUES('', NOW(), DATE('{$safe['date']}'), '{$safe['idnum']}', '{$safe['event']}', '{$safe['league']}', '{$safe['sport']}', '{$safe['time']}', '{$safe['thumb']}')";
	mysql_query($query);

}

//Unset XML to Free Memory
unset($e3_replay_xml);

//Get the XML Source of ESPN3 Upcoming Feed
$xml = file_get_contents('http://sports-ak.espn.go.com/espn3/feeds/upcoming');
$enc = mb_detect_encoding($xml);
$xml = mb_convert_encoding($xml, 'UTF-8', $enc);
$e3_upcoming_xml = new SimpleXMLElement($xml);
unset($xml);
unset($enc);

//Upcoming Event Handling
foreach($e3_upcoming_xml->event as $event) {
	foreach($event as $event_id) {
		$event_id_num = $event['id'];
		$raw['idnum'] = trim($event_id_num);
	}
	unset($event_id);
	foreach($event->name as $event_name) {
		$raw['event'] = trim($event_name);
	}
	unset($event_name);
	foreach($event->league as $event_league) {
		$raw['league'] = trim($event_league);
	}
	unset($event_league);
	foreach($event->sport as $event_sport) {
		$raw['sport'] = trim($event_sport);
	}
	unset($event_sport);
	foreach($event->startTime as $event_time) {
		$time = trim($event_time);
		$raw['date'] = date('Y-m-d', strtotime($time));
		$raw['time'] = date('g:i A', strtotime($time));
	}
	unset($event_time);
	foreach($event->thumbnail->large as $event_thumb) {
		$raw['thumb'] = trim($event_thumb);
	}
	unset($event_thumb);
   	
  //Sanitize the Variables before MySQL Query
	foreach($raw as $key => $val){
		$safe[$key] = mysql_real_escape_string($val);
	}
   	
	//MySQL Query for Each Item
	$query = "INSERT INTO e3_upcoming VALUES('', NOW(), DATE('{$safe['date']}'), '{$safe['idnum']}', '{$safe['event']}', '{$safe['league']}', '{$safe['sport']}', '{$safe['time']}', '{$safe['thumb']}')";
	mysql_query($query);

}

//Unset XML to Free Memory
unset($e3_upcoming_xml);

//MySQL Connection Close
mysql_close();

?>