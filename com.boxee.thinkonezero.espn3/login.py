import mc
 
def login():
   uname = mc.ShowDialogKeyboard("Enter Username", "")
   pword = mc.ShowDialogKeyboard("Enter Password", "", True)
   if uname and pword:
      http = mc.Http()
      params = "user=%s&pass=%s" % (uname, pword)
      http.Post("http://mysite.com/login/", params)
      responseCookie = str(http.GetHttpHeader("Set-cookie"))
      if 'PHPSESSID=' in responseCookie:
         return True
   return False