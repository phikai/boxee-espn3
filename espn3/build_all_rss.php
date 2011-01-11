<?php
	
$builddate = date("D, d M Y H:i:s O");

//MySQL Connection Information
include('/home/phikai/boxee.thinkonezero.com/mysql_connect.inc.php');

//Other Fucntions
include('/home/phikai/boxee.thinkonezero.com/espn3/build/scripts/functions.inc.php');

//MySQL Connection for each Item
mysql_connect($server,$username,$password);
@mysql_select_db($database) or die( "Unable to select database");
mysql_query("SET time_zone = '-5:00';");

//JavaScript Control Overlay URL for Boxee
$js_control = rawurlencode('http://boxee.thinkonezero.com/espn3/build/scripts/js/control.js');

//Content URL Encoding
$content_url = rawurlencode('http://espn.go.com/espn3/player');			

/**************** LIVE EVENTS RSS ********************/
	
//MySQL Query to only return Live events instead of the last 25 events from live table.
//$data = mysql_query("SELECT * FROM e3_live WHERE event_id NOT IN (SELECT event_id FROM e3_replay) AND fulldate > NOW() - INTERVAL 1 day ORDER BY id DESC LIMIT 0, 25");
$data = mysql_query("SELECT * FROM e3_live WHERE event_id NOT IN (SELECT event_id FROM e3_replay) AND fulldate > NOW() - INTERVAL 1 day UNION SELECT * FROM e3_upcoming WHERE event_id NOT IN (SELECT event_id FROM e3_replay) AND event_id NOT IN (SELECT event_id FROM e3_live) AND time < CURTIME() + INTERVAL 5 MINUTE AND time > CURTIME() + INTERVAL 5 MINUTE ORDER BY time DESC");

if(mysql_num_rows($data)==0){
	$rss .= '<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/" xmlns:boxee="http://boxee.tv/spec/rss/" xmlns:dcterms="http://purl.org/dc/terms/">'."\n";
	$rss .= '<channel>'."\n";
		$rss .= '<title>ESPN 3 Live Events</title>'."\n";
		$rss .= '<description>ESPN 3 provides live streaming sports online.</description>'."\n";
		$rss .= '<lastBuildDate>'.$builddate.'</lastBuildDate>'."\n";
		$rss .= '<ttl>15</ttl>'."\n";
			$rss .= '<guid>http://boxee.thinkonezero.com</guid>'."\n";
			$rss .= '<title>No Live Events</title>'."\n";
			$rss .= '<description>There are currently no live events ongoing.</description>'."\n";
			$rss .= '<boxee:property name="custom:sport"></boxee:property>'."\n";
			$rss .= '<boxee:property name="custom:league"></boxee:property>'."\n";
			$rss .= '<boxee:property name="custom:event">No Live Events Scheduled at this Time.</boxee:property>'."\n";
			$rss .= '<boxee:property name="custom:date"></boxee:property>'."\n";
			$rss .= '<boxee:property name="custom:time"></boxee:property>'."\n";
			$rss .= '<media:content url="" />'."\n";
			$rss .= '<media:thumbnail url="http://boxee.thinkonezero.com/espn3/build/thumbs/default.png" />'."\n";
			$rss .= '<boxee:media-type expression="full" type="show" name="Live Sports"/>'."\n";			
			$rss .= '<media:category scheme="urn:boxee:genre">sport</media:category>'."\n";
			$rss .= '<boxee:release-date></boxee:release-date>'."\n";
		$rss .= '</item>'."\n";
	$rss .= '</channel>'."\n";
	$rss .= '</rss>'."\n";
	} else {	
		$rss .= '<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/" xmlns:boxee="http://boxee.tv/spec/rss/" xmlns:dcterms="http://purl.org/dc/terms/">'."\n";
		$rss .= '<channel>'."\n";
			$rss .= '<title>ESPN 3 Live Events</title>'."\n";
			$rss .= '<description>ESPN 3 provides live streaming sports online.</description>'."\n";
			$rss .= '<lastBuildDate>'.$builddate.'</lastBuildDate>'."\n";
			$rss .= '<ttl>15</ttl>'."\n";
				while($results = mysql_fetch_array($data)){
				
					$league_url = strtolower(str_replace(" ", "%20", $results['league']));
					$time = date("g:i A", strtotime($results['time']));
					
					$rss .= '<item>'."\n";
					$rss .= '<guid>http://espn.go.com/espn3/player?id='.$results['event_id'].'</guid>'."\n";
					$rss .= '<title>'.$results['sport'].' - '.$results['event'].'</title>'."\n";
					$rss .= '<description>'.$results['sport'].' - '.$results['league'].' - '.$results['event'].' - '.$results['date'].' - '.$results['time'].' EST</description>'."\n";
					$rss .= '<boxee:property name="custom:sport">'.$results['sport'].'</boxee:property>'."\n";
					$rss .= '<boxee:property name="custom:league">'.$results['league'].'</boxee:property>'."\n";
					$rss .= '<boxee:property name="custom:event">'.$results['event'].'</boxee:property>'."\n";
					$rss .= '<boxee:property name="custom:date">'.$results['date'].'</boxee:property>'."\n";
					$rss .= '<boxee:property name="custom:time">'.$time.' EST</boxee:property>'."\n";
					$rss .= '<media:content url="flash://espn.go.com/src='.$content_url.'%3Fid%3D'.$results['event_id'].'%26league%3D'.$league_url.'&bx-jsactions='.$js_control.'" type="application/x-shockwave-flash" />'."\n";
					if (url_exists($results['thumb'])) {
						$rss .= '<media:thumbnail url="'.$results['thumb'].'" />'."\n";
					} else {
						$rss .= '<media:thumbnail url="http://boxee.thinkonezero.com/espn3/build/thumbs/default.png" />'."\n";
					}
					$rss .= '<boxee:media-type expression="full" type="show" name="Live Sports"/>'."\n";			
					$rss .= '<media:category scheme="urn:boxee:genre">sport</media:category>'."\n";
				$rss .= '<boxee:release-date>'.$results['fulldate'].'</boxee:release-date>'."\n";
			$rss .= '</item>'."\n";
			}			
		$rss .= '</channel>'."\n";
		$rss .= '</rss>'."\n";
	}

//Write the NEW LOCATION Live Events XML File
$xmlfile = '/home/phikai/boxee.thinkonezero.com/espn3/feeds/live.xml';
$fh = fopen($xmlfile, 'w+') or die('Unable to Open File');
fwrite($fh, $rss);
fclose($fh);

//Unset Variables
unset($data);
unset($rss);
unset($xmlfile);
unset($fh);


/***************** UPCOMING EVENTS RSS ******************/

//MySQL Query to only return Upcoming events instead of the last 25 events from upcoming table.
$data = mysql_query("SELECT * FROM e3_upcoming WHERE event_id NOT IN (SELECT event_id FROM e3_live) AND date < NOW() + INTERVAL 7 day AND date > NOW() - INTERVAL 1 day ORDER BY id ASC LIMIT 0, 25");

$rss .= '<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/" xmlns:boxee="http://boxee.tv/spec/rss/" xmlns:dcterms="http://purl.org/dc/terms/">'."\n";
$rss .= '<channel>'."\n";
	$rss .= '<title>ESPN 3 Upcoming Events</title>'."\n";
	$rss .= '<description>ESPN 3 provides live streaming sports online.</description>'."\n";
	$rss .= '<lastBuildDate>'.$builddate.'</lastBuildDate>'."\n";
	$rss .= '<ttl>15</ttl>'."\n";
	while($results = mysql_fetch_array($data)){
	
		$time = date("g:i A", strtotime($results['time']));
		
		$rss .= '<item>'."\n";
			$rss .= '<guid>http://espn.go.com/espn3/player?id='.$results['event_id'].'</guid>'."\n";
			$rss .= '<title>'.$results['sport'].' - '.$results['event'].'</title>'."\n";
			$rss .= '<description>'.$results['sport'].' - '.$results['league'].' - '.$results['event'].' - '.$results['date'].' - '.$results['time'].' EST</description>'."\n";
			$rss .= '<boxee:property name="custom:sport">'.$results['sport'].'</boxee:property>'."\n";
			$rss .= '<boxee:property name="custom:league">'.$results['league'].'</boxee:property>'."\n";
			$rss .= '<boxee:property name="custom:event">'.$results['event'].'</boxee:property>'."\n";
			$rss .= '<boxee:property name="custom:date">'.$results['date'].'</boxee:property>'."\n";
			$rss .= '<boxee:property name="custom:time">'.$time.' EST</boxee:property>'."\n";
			if (url_exists($results['thumb'])) {
				$rss .= '<media:thumbnail url="'.$results['thumb'].'" />'."\n";
			} else {
				$rss .= '<media:thumbnail url="http://boxee.thinkonezero.com/espn3/build/thumbs/default.png" />'."\n";
			}
			$rss .= '<boxee:media-type expression="full" type="show" name="Live Sports"/>'."\n";			
			$rss .= '<media:category scheme="urn:boxee:genre">sport</media:category>'."\n";
			$rss .= '<boxee:release-date>'.$results['fulldate'].'</boxee:release-date>'."\n";
		$rss .= '</item>'."\n";
	}
			
$rss .= '</channel>'."\n";
$rss .= '</rss>'."\n";

//Write the NEW LOCATION Upcoming Events XML File
$xmlfile = '/home/phikai/boxee.thinkonezero.com/espn3/feeds/upcoming.xml';
$fh = fopen($xmlfile, 'w+') or die('Unable to Open File');
fwrite($fh, $rss);
fclose($fh);

//Unset Variables
unset($data);
unset($rss);
unset($xmlfile);
unset($fh);

//MySQL Connection Close
mysql_close();

?>