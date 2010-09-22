<?php

//Include the Simple HTML DOM Parser
include('build/scripts/simplehtmldom/simple_html_dom.php');

//MySQL Connection Information
require_once('/home/phikai/boxee.thinkonezero.com/mysql_connect.inc');

//Get the HTML Source of ESPN3 Index
$html = file_get_html('http://espn.go.com/espn3/index');

//Sport Codes Array
$sp_code_array = array('BASKETBALL' => 'bk', 'SOCCER' => 'so', 'FOOTBALL' => 'fb', 'GOLF' => 'go', 'BOXING' => 'bo', 'RUGBY' => 'rg', 'TENNIS' => 'tn', 'BASEBALL' => 'bb', 'CRICKET' => 'cc', 'FISHING' => 'fi');

//Live Event Handling
foreach($html->find('div.e3p-live table') as $live) {
	foreach($live->find('td.sub') as $event_link){
		$event_id = $event_link->innertext;
		$event_idnum = explode("(", $event_id);
		$event_idnum = explode(",", $event_idnum[1]);
		$idnum = $event_idnum[0];
	}
	foreach($live->find('td.sub a') as $desc){
		$event = $desc->plaintext;
		$t_event = trim($event);
	}
	foreach($live->find('td.mod-cat') as $modcat){
		$sport = $modcat->plaintext;
		$t_sport = trim($sport);
	}
	foreach($live->find('td.title') as $title){
		$league = $title->plaintext;
		$t_league = trim($league);
	}
	foreach($live->find('td.time') as $times){
		$time = $times->plaintext;
		$t_time = trim($time);
	}
	$fulldate = date("D, d M Y H:i:s");
	$date = date("M d, Y");
	//MySQL Connection for each Item
	mysql_connect($server,$username,$password);
	@mysql_select_db($database) or die("Unable to select database");
	
	$query = "INSERT INTO e3_live VALUES('', '$fulldate', '$date', '$idnum', '$t_event', '$t_sport', '$sp_code_array[$t_sport]', '$t_league', '$t_time')";
	mysql_query($query);
	
	//MySQL Connection Close
	mysql_close();
}

?>