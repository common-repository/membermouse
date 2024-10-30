/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_InstantNotificationViewJS = MM_Core.extend({
    
	validateForm: function(){
		var url = mmJQuery("#mm-script-url").val();
		if(url.length>0){
			if(!this.isValidURL(url)){
				alert('Invalid script URL : '+url);
				return false;
			}
		}
		return true;
	},
	
	checkDefaultURL: function(){
		var url = mmJQuery("#mm_default_url").val();
		
		if(url.length>0 && !this.isValidURL(url)){
			alert('Invalid script URL : '+url);
			return false;
		}
		return true;
	},
	
	sendTestNotify: function(id){
		var values = {
            id:id,
            mm_action: "sendTestNotification"
        };

        var ajax = new MM_Ajax(false, this.module, this.action, this.method);
        ajax.send(values, false, 'mmjs','testNofityCallback'); 
	},
	
	testNofityCallback: function(data){
		if(data.type == 'error'){
			alert(data.message);
		}
		else{
			alert("Test executed successfully");
		}	
	},
	
	setStatusField: function(){
		var status = mmJQuery("#mm-status-container input:radio:checked").val();
		var statusVal = '0';
		if(status=='active'){
			statusVal = '1';
		}
		mmJQuery("#mm-status").val(statusVal);
	},
});

var mmjs = new MM_InstantNotificationViewJS("MM_InstantNotificationView", "Event Notification");