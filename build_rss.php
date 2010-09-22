<?php
	
	$builddate = date("D, d M Y H:i:s O");

	//MySQL Connection Information
	require_once('/home/phikai/boxee.thinkonezero.com/mysql_connect.inc');

	//MySQL Connection for each Item
	mysql_connect($server,$username,$password);
	@mysql_select_db($database) or die( "Unable to select database");
	
	$data = mysql_query("SELECT * FROM e3_live ORDER BY id DESC LIMIT 0, 25");
	
	//JavaScript Control Overlay URL for Boxee
	$js_control = rawurlencode('http://boxee.thinkonezero.com/espn3/build/scripts/js/control.js');	

$rss .= '<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/" xmlns:boxee="http://boxee.tv/spec/rss/" xmlns:dcterms="http://purl.org/dc/terms/">'."\n";

$rss .= '<channel>'."\n";
	$rss .= '<title>ESPN 3 Live Events</title>'."\n";
	$rss .= '<description>ESPN 3 provides live streaming sports online.</description>'."\n";
	$rss .= '<lastBuildDate>'.$builddate.'</lastBuildDate>'."\n";
	$rss .= '<ttl>15</ttl>'."\n";
	while($results = mysql_fetch_array($data)){
		$league = strtolower(str_replace(" ", "_", $results['league']));
		$league_url = strtolower(str_replace(" ", "%20", $results['league']));
		$rss .= '<item>'."\n";
			$rss .= '<guid>http://espn.go.com/espn3/player?id='.$results['event_id'].'</guid>'."\n";
			$rss .= '<title>'.$results['sport'].' - '.$results['event'].'</title>'."\n";
			$rss .= '<description>'.$results['sport'].' - '.$results['league'].' - '.$results['event'].' - '.$results['date'].' - '.$results['time'].' EST</description>'."\n";
			$content_url = rawurlencode('http://espn.go.com/espn3/player');			
			$rss .= '<media:content url="flash://espn.go.com/src='.$content_url.'%3Fid%3D'.$results['event_id'].'%26league%3D'.$league_url.'&bx-jsactions='.$js_control.'" type="application/x-shockwave-flash" />'."\n";
			if(empty($league) || empty($results['sport_code'])){
				$rss .= '<media:thumbnail url="http://boxee.thinkonezero.com/espn3/build/thumbs/default.png" />'."\n";
			}	else {
				$rss .= '<media:thumbnail url="http://a.espncdn.com/espn360/images/'.$results['sport_code'].'/'.$league.'/'.$results['event_id'].'.jpg" />'."\n";
			}
			$rss .= '<boxee:media-type expression="full" type="show" name="Live Sports"/>'."\n";			
			$rss .= '<media:category scheme="urn:boxee:genre">sport</media:category>'."\n";
			$rss .= '<boxee:release-date>'.$results['fulldate'].'</boxee:release-date>'."\n";
		$rss .= '</item>'."\n";
		unset($league);
		unset($content_url);
	}

	//MySQL Connection Close
	mysql_close();
			
$rss .= '</channel>'."\n";
$rss .= '</rss>'."\n";

//Write the XML File
$xmlfile = '/home/phikai/boxee.thinkonezero.com/espn3/rss.xml';
$fh = fopen($xmlfile, 'w+') or die('Unable to Open File');
fwrite($fh, $rss);
fclose($fh);

?>