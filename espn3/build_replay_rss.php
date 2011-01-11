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

/**************** REPLAY SPORTS ********************/	

//Query for each sport from the Replay Table	
$query1 = mysql_query("SELECT distinct sport FROM e3_replay WHERE date > NOW() - INTERVAL 30 day ORDER BY sport") or die(mysql_error());

//Start of Sport RSS Feed of RSS Feeds based on each sport
$sportrss .= '<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/" xmlns:boxee="http://boxee.tv/spec/rss/" xmlns:dcterms="http://purl.org/dc/terms/">'."\n";
$sportrss .= '<channel>'."\n";
	$sportrss .= '<title>ESPN 3 Replay Events by Sport.</title>'."\n";
	$sportrss .= '<description>ESPN 3 provides live streaming sports online.</description>'."\n";
	$sportrss .= '<lastBuildDate>'.$builddate.'</lastBuildDate>'."\n";
	$sportrss .= '<ttl>15</ttl>'."\n";

//Execute while loop to get each sport	and add to sport rss feed
while($sport = mysql_fetch_array($query1)){
	
	$sportfile = strtolower(str_replace(' ', '_', $sport[0]));
	
	$sportrss .= '<item>'."\n";
	$sportrss .= '<title>'.$sport[0].'</title>'."\n";
	$sportrss .= '<description>Replay events for '.$sport[0].'</description>'."\n";
	$sportrss .= '<link>rss://boxee.thinkonezero.com/espn3/feeds/replay/'.$sportfile.'.xml</link>'."\n";
	$sportrss .= '<media:thumbnail url="http://boxee.thinkonezero.com/espn3/build/thumbs/default.png" />'."\n";
	$sportrss .= '</item>'."\n";
	
	//Make Sport safe for Query
	$safesport = mysql_real_escape_string($sport[0]);
	
	//Query for each league of each sport from the Replay Table
	$query2 = mysql_query("SELECT distinct league FROM e3_replay WHERE sport='$safesport' AND date > NOW() - INTERVAL 30 day ORDER BY league") or die(mysql_error());
	
	//Start of League RSS Feed of RSS Feeds based on each league
	$leaguerss .= '<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/" xmlns:boxee="http://boxee.tv/spec/rss/" xmlns:dcterms="http://purl.org/dc/terms/">'."\n";
	$leaguerss .= '<channel>'."\n";
		$leaguerss .= '<title>ESPN 3 Replay Events by Sport.</title>'."\n";
		$leaguerss .= '<description>ESPN 3 provides live streaming sports online.</description>'."\n";
		$leaguerss .= '<lastBuildDate>'.$builddate.'</lastBuildDate>'."\n";
		$leaguerss .= '<ttl>15</ttl>'."\n";
	
	//Execute while lopp to get each league and add to the league rss feed
	while($league = mysql_fetch_array($query2)){
	
		$sportfile = strtolower(str_replace(' ', '_', $sport[0]));
		$leaguefile = strtolower(str_replace(' ', '_', $league[0]));
	
		$leaguerss .= '<item>'."\n";
		$leaguerss .= '<title>'.$league[0].'</title>'."\n";
		$leaguerss .= '<description>Replay events for '.$league[0].'</description>'."\n";
		$leaguerss .= '<link>rss://boxee.thinkonezero.com/espn3/feeds/replay/'.$sportfile.'-'.$leaguefile.'.xml</link>'."\n";
		$leaguerss .= '<media:thumbnail url="http://boxee.thinkonezero.com/espn3/build/thumbs/default.png" />'."\n";
		$leaguerss .= '</item>'."\n";
		
		//Make Sport and League safe for Query
		$safesport = mysql_real_escape_string($sport[0]);
		$safeleague = mysql_real_escape_string($league[0]);
		
		//Query for each event of each league in each sport from the Replay Table
		$query3 = mysql_query("SELECT * FROM e3_replay WHERE sport='$safesport' AND league='$safeleague' AND date > NOW() - INTERVAL 30 day ORDER BY id DESC LIMIT 0, 25") or die(mysql_error());
		
		//Start of Replay Events RSS Feed based on League and Sport
		$eventrss .= '<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/" xmlns:boxee="http://boxee.tv/spec/rss/" xmlns:dcterms="http://purl.org/dc/terms/">'."\n";
		$eventrss .= '<channel>'."\n";
			$eventrss .= '<title>ESPN 3 Replay Events for the '.$sport[0].' - '.$league[0].'</title>'."\n";
			$eventrss .= '<description>ESPN 3 provides live streaming sports online.</description>'."\n";
			$eventrss .= '<lastBuildDate>'.$builddate.'</lastBuildDate>'."\n";
			$eventrss .= '<ttl>15</ttl>'."\n";
			
			while($results = mysql_fetch_array($query3)){
			
				$league_url = strtolower(str_replace(" ", "%20", $results['league']));
				$time = date("g:i A", strtotime($results['time']));
				
				$eventrss .= '<item>'."\n";
				$eventrss .= '<guid>http://espn.go.com/espn3/player?id='.$results['event_id'].'</guid>'."\n";
				$eventrss .= '<title>'.$results['sport'].' - '.$results['event'].'</title>'."\n";
				$eventrss .= '<description>'.$results['sport'].' - '.$results['league'].' - '.$results['event'].' - '.$results['date'].' - '.$results['time'].' EST</description>'."\n";
				$eventrss .= '<boxee:property name="custom:sport">'.$results['sport'].'</boxee:property>'."\n";
				$eventrss .= '<boxee:property name="custom:league">'.$results['league'].'</boxee:property>'."\n";
				$eventrss .= '<boxee:property name="custom:event">'.$results['event'].'</boxee:property>'."\n";
				$eventrss .= '<boxee:property name="custom:date">'.$results['date'].'</boxee:property>'."\n";
				$eventrss .= '<boxee:property name="custom:time">'.$time.' EST</boxee:property>'."\n";
				$eventrss .= '<media:content url="flash://espn.go.com/src='.$content_url.'%3Fid%3D'.$results['event_id'].'%26league%3D'.$league_url.'&bx-jsactions='.$js_control.'" type="application/x-shockwave-flash" />'."\n";
				if (url_exists($results['thumb'])) {
					$eventrss .= '<media:thumbnail url="'.$results['thumb'].'" />'."\n";
				} else {
					$eventrss .= '<media:thumbnail url="http://boxee.thinkonezero.com/espn3/build/thumbs/default.png" />'."\n";
				}
				$eventrss .= '<boxee:media-type expression="full" type="show" name="Live Sports"/>'."\n";			
				$eventrss .= '<media:category scheme="urn:boxee:genre">sport</media:category>'."\n";
				$eventrss .= '<boxee:release-date>'.$results['fulldate'].'</boxee:release-date>'."\n";
				$eventrss .= '</item>'."\n";
			}
			
		$eventrss .= '</channel>'."\n";
		$eventrss .= '</rss>'."\n";

		$sportfile = strtolower(str_replace(' ', '_', $sport[0]));
		$leaguefile = strtolower(str_replace(' ', '_', $league[0]));
		
		//Write the Replay Events XML File
		$xmlfile = '/home/phikai/boxee.thinkonezero.com/espn3/feeds/replay/'.$sportfile.'-'.$leaguefile.'.xml';
		$fh = fopen($xmlfile, 'w+') or die('Unable to Open File');
		fwrite($fh, $eventrss);
		fclose($fh);

		//Unset Variables
		unset($eventrss);
		unset($xmlfile);
		unset($fh);
	}
	
	$leaguerss .= '</channel>'."\n";
	$leaguerss .= '</rss>'."\n";
	
	$sportfile = strtolower(str_replace(' ', '_', $sport[0]));
	
	//Write the Replay Events XML File
	$xmlfile = '/home/phikai/boxee.thinkonezero.com/espn3/feeds/replay/'.$sportfile.'.xml';
	$fh = fopen($xmlfile, 'w+') or die('Unable to Open File');
	fwrite($fh, $leaguerss);
	fclose($fh);

	//Unset Variables
	unset($leaguerss);
	unset($xmlfile);
	unset($fh);
	
}

$sportrss .= '</channel>'."\n";
$sportrss .= '</rss>'."\n";

//Write the Replay Events XML File
$xmlfile = '/home/phikai/boxee.thinkonezero.com/espn3/feeds/replay.xml';
$fh = fopen($xmlfile, 'w+') or die('Unable to Open File');
fwrite($fh, $sportrss);
fclose($fh);

//Unset Variables
unset($sportrss);
unset($xmlfile);
unset($fh);

//MySQL Connection Close
mysql_close();

?>