#!/usr/bin/python
#
# Based on the default.py script from the XBMC ESPN3 Plugin
#		Written by Ksosez, with massive help from Bluecop
#		Released under GPL(v2)

import mc
import random
from BeautifulSoup import BeautifulStoneSoup as Soup

def doStreams(listitem):
	mc.ShowDialogWait()
	print '*** ESPN3 ***'
	url = str(listitem.GetProperty("baseurl"))
	#Make startupevent url from page url
	startUrl = 'http://espn.go.com/espn3/feeds/startupEvent?'+url.split('?')[1]+'&gameId=null&sportCode=null'
	#Get eventid, bamContentId, and bamEventId from starturl data
	html = get_html(startUrl)
	if html == False:
		mc.HideDialogWait()
		mc.ShowDialogOk("Failure to Launch URL", "Unable to launch video feed most likely because you already requested this feed. Please wait a while and try again.")
	else:
		player = mc.GetPlayer()
		event = Soup(html)('event')[1]
		eventid = event['id']
		bamContentId = event['bamcontentid']
		bamEventId = event['bameventid']
		
		#Make identityPointId from userdata
		userdata = 'http://broadband.espn.go.com/espn3/auth/userData'
		html = get_html(userdata)
		soup = Soup(html)
		affiliateid = soup('name')[0].string
		swid = soup('personalization')[0]['swid']
		identityPointId = affiliateid+':'+swid
		
		#Use eventid, bamContentId, bamEventId, and identityPointId to get smil url and auth data
		authurl = 'https://espn-ws.bamnetworks.com/pubajaxws/bamrest/MediaService2_0/op-findUserVerifiedEvent/v-2.1'
		authurl += '?platform=WEB_MEDIAPLAYER'
		authurl += '&playbackScenario=FMS_CLOUD'
		authurl += '&eventId='+bamEventId
		authurl += '&contentId='+bamContentId
		authurl += '&rand='+str(random.random())+'0000'
		authurl += '&cdnName=PRIMARY_AKAMAI'
		authurl += '&partnerContentId='+eventid
		authurl += '&identityPointId='+identityPointId
		authurl += '&playerId=domestic'
		html = get_html(str(authurl))
		smilurl = Soup(html).findAll('url')[0].string
		auth = smilurl.split('?')[1]
		
		#Grab smil url to get rtmp url and playpath
		html = get_html(str(smilurl))
		soup = Soup(html)
		rtmp = soup.findAll('meta')[0]['base']
		
		if 'ondemand' in rtmp:		
			#get replay quality
			config = mc.GetApp().GetLocalConfig()
			confquality = config.GetValue("quality")
			quality = int(confquality)
			
			rtmppath = rtmp+'/?'+auth
			playpath = soup.findAll('video')[quality]['src']
			#finalurl = rtmp+'/?'+auth+' playpath='+playpath
			listitem = mc.ListItem(mc.ListItem.MEDIA_VIDEO_CLIP)
			listitem.SetPath(str(rtmppath))
			listitem.SetProperty("PageURL", str(rtmppath))
			listitem.SetProperty("PlayPath", str(playpath))			
		elif 'live' in rtmp:
			#print 'rtmp'
			#print rtmp
			#playpath = soup.findAll('video')[4]['src']
			#authpath = playpath+'?'+auth
			#finalurl = rtmp+' live=1 playlist=1 subscribe='+playpath+' playpath='+playpath+'?'+auth
			#print 'finalurl'
			#print finalurl
			#rtmppath = rtmp+' live=1 playlist=1'
			#print 'rtmppath'
			#print rtmppath
			#listitem = mc.ListItem(mc.ListItem.MEDIA_VIDEO_CLIP)
			#listitem.SetPath(str(rtmppath))
			#listitem.SetProperty("PageURL", str(rtmppath))
			#listitem.SetProperty("PlayPath", str(authpath))
			#listitem.SetProperty("TcUrl", str(finalurl))
			pass
		else:
			pass
		mc.HideDialogWait()
		player.Play(listitem)
	
def get_html(url):
	http = mc.Http()
	html = http.Get(url)
	if http.GetHttpResponseCode() != 200:
		html = False
		return html
	else:
		return html