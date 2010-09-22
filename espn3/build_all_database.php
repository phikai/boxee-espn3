<?php

//Include the Simple HTML DOM Parser
include('build/scripts/simplehtmldom/simple_html_dom.php');

//MySQL Connection Information
include('/home/phikai/boxee.thinkonezero.com/mysql_connect.inc.php');

//MySQL Connection
mysql_connect($server,$username,$password);
@mysql_select_db($database) or die("Unable to select database");

//Get the HTML Source of ESPN3 Index
$html = file_get_html('http://espn.go.com/espn3/index');

//Sport Codes Array
$sp_code_array = array('BASKETBALL' => 'bk', 'SOCCER' => 'so', 'FOOTBALL' => 'fb', 'GOLF' => 'go', 'BOXING' => 'bo', 'RUGBY' => 'rg', 'TENNIS' => 'tn', 'BASEBALL' => 'bb', 'CRICKET' => 'cc', 'FISHING' => 'fi', 'LACROSSE' => 'lc', 'AWARDS' => 'aw', 'TRACK AND FIELD' => 'tf', 'VOLLEYBALL' => 'vb', 'SOFTBALL' => 'sb', 'ACTION SPORTS' => 'et', 'AUTO RACING' => 'ar', 'HOCKEY' => 'ho', 'POKER' => 'gm');

//Live Event Handling
foreach($html->find('div.e3p-live table') as $live) {
	foreach($live->find('td.sub') as $event_link){
		$event_id = $event_link->innertext;
		$event_idnum = explode("(", $event_id);
		$event_idnum = explode(",", $event_idnum[1]);
		$raw['idnum'] = $event_idnum[0];
	}
	unset($event_link);
	foreach($live->find('td.sub a') as $desc){
		$event = $desc->plaintext;
		$raw['t_event'] = trim($event);
	}
	unset($desc);
	foreach($live->find('td.mod-cat') as $modcat){
		$sport = $modcat->plaintext;
		$raw['t_sport'] = trim($sport);
	}
	unset($modcat);
	foreach($live->find('td.title') as $title){
		$league = $title->plaintext;
		$raw['t_league'] = trim($league);
	}
	unset($title);
	foreach($live->find('td.time') as $times){
		$time = $times->plaintext;
		$raw['t_time'] = trim($time);
	}
	unset($times);
	$raw['fulldate'] = date("D, d M Y H:i:s");
	$raw['date'] = date("M d, Y");
	
	//Clear Live for Memory
	unset($live);
	
	//Sanitize the Variables before MySQL Query
	foreach($raw as $key => $val){
		$safe[$key] = mysql_real_escape_string($val);
	}

	//MySQL Query for Each Item
	$query = "INSERT INTO e3_live VALUES('', '{$safe['fulldate']}', '{$safe['date']}', '{$safe['idnum']}', '{$safe['t_event']}', '{$safe['t_sport']}', '{$sp_code_array[$safe['t_sport']]}', '{$safe['t_league']}', '{$safe['t_time']}')";
	mysql_query($query);
}

//Empty Upcoming Event Table to prevent Duplications as there is no Unique Key Defined
$query = "TRUNCATE TABLE e3_upcoming";
mysql_query($query);

//Upcoming Event Handling
foreach($html->find('div.e3p-upcoming table') as $live) {
	foreach($live->find('td.sub') as $desc){
		$event = $desc->plaintext;
		$raw['t_event'] = trim($event);
	}
	unset($desc);
	foreach($live->find('td.mod-cat') as $modcat){
		$sport = $modcat->plaintext;
		$raw['t_sport'] = trim($sport);
	}
	unset($modcat);
	foreach($live->find('td.title') as $title){
		$league = $title->plaintext;
		$raw['t_league'] = trim($league);
	}
	unset($title);
	foreach($live->find('td.time') as $times){
		$time = $times->plaintext;
		$raw['t_time'] = trim($time);
	}
	unset($times);
	$raw['fulldate'] = date("D, d M Y H:i:s");
	$raw['date'] = date("M d, Y");
	
	//Clear Live for Memory
	unset($live);
	
	//Sanitize the Variables before MySQL Query
	foreach($raw as $key => $val){
		$safe[$key] = mysql_real_escape_string($val);
	}
	
	//MySQL Query for Each Item
	$query = "INSERT INTO e3_upcoming VALUES('', '{$safe['fulldate']}', '{$safe['date']}', '', '{$safe['t_event']}', '{$safe['t_sport']}', '{$sp_code_array[$safe['t_sport']]}', '{$safe['t_league']}', '{$safe['t_time']}')";
	mysql_query($query);
}

//Replay Event Handling
foreach($html->find('div.e3p-replay table') as $live) {
	foreach($live->find('td.sub') as $event_link){
		$event_id = $event_link->innertext;
		$event_idnum = explode("(", $event_id);
		$event_idnum = explode(",", $event_idnum[1]);
		$raw['idnum'] = $event_idnum[0];
	}
	unset($event_link);
	foreach($live->find('td.sub a') as $desc){
		$event = $desc->plaintext;
		$raw['t_event'] = trim($event);
	}
	unset($desc);
	foreach($live->find('td.mod-cat') as $modcat){
		$sport = $modcat->plaintext;
		$raw['t_sport'] = trim($sport);
	}
	unset($modcat);
	foreach($live->find('td.title') as $title){
		$league = $title->plaintext;
		$raw['t_league'] = trim($league);
	}
	unset($title);
	foreach($live->find('td.time') as $times){
		$time = $times->plaintext;
		$raw['t_time'] = trim($time);
	}
	unset($times);
	$raw['fulldate'] = date("D, d M Y H:i:s");
	$raw['date'] = date("M d, Y");
	
	//Clear Live for Memory
	unset($live);
	
	//Sanitize the Variables before MySQL Query
	foreach($raw as $key => $val){
		$safe[$key] = mysql_real_escape_string($val);
	}

	//MySQL Query for Each Item
	$query = "INSERT INTO e3_replay VALUES('', '{$safe['fulldate']}', '{$safe['date']}', '{$safe['idnum']}', '{$safe['t_event']}', '{$safe['t_sport']}', '{$sp_code_array[$safe['t_sport']]}', '{$safe['t_league']}', '{$safe['t_time']}')";
	mysql_query($query);
}

//MySQL Connection Close
mysql_close();

?>