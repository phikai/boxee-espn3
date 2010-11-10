import mc
 
def login(uname, pword):
	http = mc.Http()
	params = "user=%s&pass=%s" % (uname, pword)
	http.Post("https://boxee.thinkonezero.com/espn3/login.php", params)
