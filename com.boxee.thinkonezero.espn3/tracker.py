# 
# Tracker Client - A Google Analytics Integration
# A Python module for interacting with the Tracker web service.
# 
# Written by /rob, 29 July 2010
#
# Usage:
# 
# Tracker Client uses the Tracker web service to allow Python apps like those found on Boxee to store view and event
# tracking data on Google Analytics.  To track a page view, simply call the method with the option of passing a 
# parameter for the window's ID or canonical name for easy reporting (e.g. "home").  To track an event, call the
# method with event argument with the category as the value (e.g. "Content", "Video"), action argument with an
# appropriate value (e.g. "Play", "Pause") and label argument (e.g. The title of the content being played).  Value
# is optional.
#
# Returns false if fail.  Returns transparent GIF if success.
#
# Examples:
# 
# Instantiate object:
# myTracker = tracker.Tracker()
#
# Instantiate object with your own Google Analytics code.
# myTracker = tracker.Tracker("UA-xxxxxxx-x")
#
# Track page view:
# tracker.Tracker.trackView()
# 
# Track page view with non-default window:
# tracker.Tracker.trackView("browse")
# 
# Track Play event:
# myTracker.trackEvent("Video", "Play", "Foo Bar")

import mc
import urllib

class Tracker:
    def __init__(self, uacode = False, debug = False):
        self.uacode = uacode
        self.version = "alpha"
        self.path = "http://apps.gonzee.tv/tracker.php"
        self.application = mc.GetApp().GetId()
        self.debug = debug
            
    def trackView(self, window = False):
        mc.LogDebug("%%%%%%%%%%%%%%%%%%% Tracker: Tracking view. %%%%%%%%%%%%%%%%%%%")
        if not window:
            window = "14000"
        
        mc.LogDebug("%%%%%%%%%%%%%%%%%%% Tracker: Requesting tracker image path. %%%%%%%%%%%%%%%%%%%%%%%%%")
        params = {
                  'application': self.application,
                  'window': window
        }
        
        if self.uacode:
            params['uacode'] = self.uacode
        if self.debug:
            params['debug'] = self.debug
        
        tracker = self.request(self.path + "?" + urllib.urlencode(params))       

        if self.debug:
            mc.LogDebug("%%%%%%%%%%%%%%%%%%% Tracker: Path is " + str(tracker) + " %%%%%%%%%%%%%%%%%%%%%%%%%")
            tracker = self.request(str(tracker))
            mc.LogDebug("%%%%%%%%%%%%%%%%%%% GA Debug: " + str(tracker) + " %%%%%%%%%%%%%%%%%%%%%%%%%")
        elif tracker:
            mc.LogDebug("%%%%%%%%%%%%%%%%%%% Tracker: Path is " + str(tracker) + " %%%%%%%%%%%%%%%%%%%%%%%%%")
            return self.request(str(tracker))
        else:
            mc.LogDebug("%%%%%%%%%%%%%%%%%%% Tracker: tracking request failed. %%%%%%%%%%%%%%%%%%%")
            return False
    
    def trackEvent(self, event, action, label, value = False):
        mc.LogDebug("%%%%%%%%%%%%%%%%%%% Tracker: Tracking event. %%%%%%%%%%%%%%%%%%%")
        params = {
                  'application': self.application,
                  'event': event,
                  'action': action,
                  'label': label
        }
        
        if value:
            eventPath = eventPath + "&value=" + value
        if self.uacode:
            params['uacode'] = self.uacode
        if self.debug:
            params['debug'] = self.debug
        
        eventPath = self.path + "?" + urllib.urlencode(params)
        
        mc.LogDebug("%%%%%%%%%%%%%%%%%%% Tracker: Requesting tracker image path. %%%%%%%%%%%%%%%%%%%%%%%%%")
        tracker = self.request(eventPath)
        
        if self.debug:
            mc.LogDebug("%%%%%%%%%%%%%%%%%%% Tracker: Path is " + str(tracker) + " %%%%%%%%%%%%%%%%%%%%%%%%%")
            tracker = self.request(str(tracker))
            mc.LogDebug("%%%%%%%%%%%%%%%%%%% GA Debug: " + str(tracker) + " %%%%%%%%%%%%%%%%%%%%%%%%%")
        elif tracker:
            mc.LogDebug("%%%%%%%%%%%%%%%%%%% Tracker: Path is " + str(tracker) + " %%%%%%%%%%%%%%%%%%%%%%%%%")
            return self.request(str(tracker))
        else:
            mc.LogDebug("%%%%%%%%%%%%%%%%%%% Tracker: tracking request failed. %%%%%%%%%%%%%%%%%%%")
            return False
        
    def request(self, path):
        # Instatiate HTTP object
        myHttp = mc.Http()
        
        # Set User Agent
        myHttp.SetUserAgent("Boxee App (boxee/beta " + self.version + " tracker)")
        
        # Get data
        data = myHttp.Get(path)
        
        # Make request
        return data