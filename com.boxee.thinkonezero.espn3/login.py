import mc
import urllib2
import re

# http request header
usagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.7) Gecko/2007091417 Firefox/2.0.0.7"
headers = {'User-agent' : usagent}

def doLogout():
	config = mc.GetApp().GetLocalConfig()
	config.ResetAll()
	mc.ShowDialogNotification("Resetting Data...")
	

def doLogin():
	#reset old data
	doLogout()
	
	#prompt for username and password
	prmt_user = mc.ShowDialogKeyboard("Email Address", "", False)
	prmt_pass = mc.ShowDialogKeyboard("Email Password", "", True)
	
	#get user domain for login
	user_provider = re.split('@', prmt_user)
	
	#do Comcast Login
	doComcast(prmt_user, prmt_pass)
	

def doESPN():
	# login url and parameters
	url_lin   = "https://r.espn.go.com/members/login"
	url_lin_p = "count=0&SUBMIT=1&language=en&affiliateName=espn&regFormId=espn&username=" + prmt_user + "&password=" + prmt_pass + "&appRedirect=https://r.espn.go.com/members/index"
	

def doComcast(prmt_user, prmt_pass):
	#comcast login	
	url_lin = "https://login.comcast.net/login"
	url_lin_p = "&user=" + prmt_user + "&passwd=" + prmt_pass
	
	#do Authentication
	doAuthenticate(url_lin, url_lin_p)	


def doAuthenticate(url_lin, url_lin_p):
	# authenticate
	mc.ShowDialogNotification("authenticating...")
	http = mc.Http()
	http.SetHttpHeader("User-Agent", usagent)
	http.Post(url_lin, url_lin_p)
	
	# user information
	mc.ShowDialogNotification("retrieving user information...")
	response = http.Get("http://broadband.espn.go.com/espn360/util/userData?format=tea&store=true&referrer=")
	rc = re.compile('"a":"(.*)","an"(.*)"isLoggedIn":"(.*)","ra"(.*)"username":"(.*)","sso"', re.MULTILINE)
	rs = rc.search(response)
	
	# are they a valid user?
	if (rs.group(1) != "invalid") and (rs.group(3) == "true"):
		mc.ShowDialogNotification(rs.group(5) + " is authenticated!")
	elif rs.group(1) == "invalid":
		mc.ShowDialogNotification("error: not a valid network affiliate")
	else:
		mc.ShowDialogNotification("error: not able to authenticate user")

	# try again to get an id
	setSession()
	
	# set user data info
	config = mc.GetApp().GetLocalConfig()
	config.SetValue("affiliate", rs.group(1))
	config.SetValue("loggedin", rs.group(3))
	config.SetValue("username", rs.group(5))
	
	
def setSession():
	# grab the cookie jar
	cj = mc.GetCookieJar()
	cf = open(cj)
	cl = cf.read();
	cf.close()
	
	#get config
	config = mc.GetApp().GetLocalConfig()
	
	# check if there's anything good in there
	for c in cl.split("\n"):
		if (c.find("go.com") > 0) and (c.find("SWID") > 0):
			# delicious cookie!
			cs = c.split('\t')[6].strip()
			
			if (cs != "") and (config.GetValue("username") != ""):
				if cs == config.GetValue("session"):
					return True
				elif config.GetValue("session") == "":
					config.SetValue("session", cs)
					return True
	
	# no good cookies in the jar
	return False
	