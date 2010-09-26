<?php
	
$builddate = date("D, d M Y H:i:s O");

//MySQL Connection Information
include('/home/phikai/boxee.thinkonezero.com/mysql_connect.inc.php');

//MySQL Connection for each Item
mysql_connect($server,$username,$password);
@mysql_select_db($database) or die( "Unable to select database");

//JavaScript Control Overlay URL for Boxee
$js_control = rawurlencode('http://boxee.thinkonezero.com/espn3/build/scripts/js/control.js');

//Content URL Encoding
$content_url = rawurlencode('http://espn.go.com/espn3/player');			

/**************** LIVE EVENTS RSS ********************/
	
//MySQL Query to only return Live events instead of the last 25 events from live table.
$data = mysql_query("SELECT * FROM e3_live WHERE event_id NOT IN (SELECT event_id FROM e3_replay) AND fulldate > NOW() - INTERVAL 1 day ORDER BY id DESC LIMIT 0, 25");
	
$rss .= '<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/" xmlns:boxee="http://boxee.tv/spec/rss/" xmlns:dcterms="http://purl.org/dc/terms/">'."\n";
$rss .= '<channel>'."\n";
	$rss .= '<title>ESPN 3 Live Events</title>'."\n";
	$rss .= '<description>ESPN 3 provides live streaming sports online.</description>'."\n";
	$rss .= '<lastBuildDate>'.$builddate.'</lastBuildDate>'."\n";
	$rss .= '<ttl>15</ttl>'."\n";
	while($results = mysql_fetch_array($data)){
		$league_url = strtolower(str_replace(" ", "%20", $results['league']));
		$rss .= '<item>'."\n";
			$rss .= '<guid>http://espn.go.com/espn3/player?id='.$results['event_id'].'</guid>'."\n";
			$rss .= '<title>'.$results['sport'].' - '.$results['event'].'</title>'."\n";
			$rss .= '<description>'.$results['sport'].' - '.$results['league'].' - '.$results['event'].' - '.$results['date'].' - '.$results['time'].' EST</description>'."\n";
			$rss .= '<media:content url="flash://espn.go.com/src='.$content_url.'%3Fid%3D'.$results['event_id'].'%26league%3D'.$league_url.'&bx-jsactions='.$js_control.'" type="application/x-shockwave-flash" />'."\n";
			$rss .= '<media:thumbnail url="'.$results['thumb'].'" />'."\n";
			$rss .= '<boxee:media-type expression="full" type="show" name="Live Sports"/>'."\n";			
			$rss .= '<media:category scheme="urn:boxee:genre">sport</media:category>'."\n";
			$rss .= '<boxee:release-date>'.$results['fulldate'].'</boxee:release-date>'."\n";
		$rss .= '</item>'."\n";
	}
			
$rss .= '</channel>'."\n";
$rss .= '</rss>'."\n";

//Write the Live Events XML File
$xmlfile = '/home/phikai/boxee.thinkonezero.com/espn3/rss_live.xml';
$fh = fopen($xmlfile, 'w+') or die('Unable to Open File');
fwrite($fh, $rss);
fclose($fh);

//Unset Variables
unset($rss);
unset($xmlfile);
unset($fh);

/*************** REPLAY EVENTS RSS ********************/

//MySQL Query to only return Live events instead of the last 25 events from live table.
$data = mysql_query("SELECT * FROM e3_replay ORDER BY id DESC LIMIT 0, 25");
	
$rss .= '<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/" xmlns:boxee="http://boxee.tv/spec/rss/" xmlns:dcterms="http://purl.org/dc/terms/">'."\n";
$rss .= '<channel>'."\n";
	$rss .= '<title>ESPN 3 Replaye Events</title>'."\n";
	$rss .= '<description>ESPN 3 provides live streaming sports online.</description>'."\n";
	$rss .= '<lastBuildDate>'.$builddate.'</lastBuildDate>'."\n";
	$rss .= '<ttl>15</ttl>'."\n";
	while($results = mysql_fetch_array($data)){
		$league_url = strtolower(str_replace(" ", "%20", $results['league']));
		$rss .= '<item>'."\n";
			$rss .= '<guid>http://espn.go.com/espn3/player?id='.$results['event_id'].'</guid>'."\n";
			$rss .= '<title>'.$results['sport'].' - '.$results['event'].'</title>'."\n";
			$rss .= '<description>'.$results['sport'].' - '.$results['league'].' - '.$results['event'].' - '.$results['date'].' - '.$results['time'].' EST</description>'."\n";
			$rss .= '<media:content url="flash://espn.go.com/src='.$content_url.'%3Fid%3D'.$results['event_id'].'%26league%3D'.$league_url.'&bx-jsactions='.$js_control.'" type="application/x-shockwave-flash" />'."\n";
			$rss .= '<media:thumbnail url="'.$results['thumb'].'" />'."\n";
			$rss .= '<boxee:media-type expression="full" type="show" name="Live Sports"/>'."\n";			
			$rss .= '<media:category scheme="urn:boxee:genre">sport</media:category>'."\n";
			$rss .= '<boxee:release-date>'.$results['fulldate'].'</boxee:release-date>'."\n";
		$rss .= '</item>'."\n";
	}
			
$rss .= '</channel>'."\n";
$rss .= '</rss>'."\n";

//Write the Live Events XML File
$xmlfile = '/home/phikai/boxee.thinkonezero.com/espn3/rss_replay.xml';
$fh = fopen($xmlfile, 'w+') or die('Unable to Open File');
fwrite($fh, $rss);
fclose($fh);

//Unset Variables
unset($rss);
unset($xmlfile);
unset($fh);

/***************** UPCOMING EVENTS RSS ******************/

//MySQL Query to only return Live events instead of the last 25 events from live table.
$data = mysql_query("SELECT * FROM e3_upcoming ORDER BY id DESC LIMIT 0, 25");
	
$rss .= '<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/" xmlns:boxee="http://boxee.tv/spec/rss/" xmlns:dcterms="http://purl.org/dc/terms/">'."\n";
$rss .= '<channel>'."\n";
	$rss .= '<title>ESPN 3 Upcoming Events</title>'."\n";
	$rss .= '<description>ESPN 3 provides live streaming sports online.</description>'."\n";
	$rss .= '<lastBuildDate>'.$builddate.'</lastBuildDate>'."\n";
	$rss .= '<ttl>15</ttl>'."\n";
	while($results = mysql_fetch_array($data)){
		$rss .= '<item>'."\n";
			$rss .= '<guid>http://espn.go.com/espn3/player?id='.$results['event_id'].'</guid>'."\n";
			$rss .= '<title>'.$results['sport'].' - '.$results['event'].'</title>'."\n";
			$rss .= '<description>'.$results['sport'].' - '.$results['league'].' - '.$results['event'].' - '.$results['date'].' - '.$results['time'].' EST</description>'."\n";
			$rss .= '<media:thumbnail url="'.$results['thumb'].'" />'."\n";
			$rss .= '<boxee:media-type expression="full" type="show" name="Live Sports"/>'."\n";			
			$rss .= '<media:category scheme="urn:boxee:genre">sport</media:category>'."\n";
			$rss .= '<boxee:release-date>'.$results['fulldate'].'</boxee:release-date>'."\n";
		$rss .= '</item>'."\n";
	}
			
$rss .= '</channel>'."\n";
$rss .= '</rss>'."\n";

//Write the Live Events XML File
$xmlfile = '/home/phikai/boxee.thinkonezero.com/espn3/rss_upcoming.xml';
$fh = fopen($xmlfile, 'w+') or die('Unable to Open File');
fwrite($fh, $rss);
fclose($fh);

//Unset Variables
unset($rss);
unset($xmlfile);
unset($fh);

//MySQL Connection Close
mysql_close();

?>