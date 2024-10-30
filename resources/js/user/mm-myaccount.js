/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_MyAccountJs = MM_Core.extend({

	createDiv: function(id)
	{
		mmJQuery("<div id='"+id+"'></div>").hide().appendTo("body").fadeIn();
	},
	
	resizeBoxes: function(){
		var membershipHeight = mmJQuery("#mm-myaccount-membership").height();
		var subscriptionHeight = mmJQuery("#mm-myaccount-subscriptions").height();
		var newHeight = (membershipHeight>subscriptionHeight)?membershipHeight:subscriptionHeight;
		
		var accountHeight = mmJQuery("#mm-myaccount-details").height();
		var billingHeight = mmJQuery("#mm-myaccount-billing").height();
		var newHeight2 = (accountHeight>billingHeight)?accountHeight:billingHeight;
		
		mmJQuery("#mm-myaccount-subscriptions").css("height", newHeight);
		mmJQuery("#mm-myaccount-membership").css("height", newHeight);
		
		mmJQuery("#mm-myaccount-details").css("height", newHeight2);
		mmJQuery("#mm-myaccount-billing").css("height", newHeight2);
	},
	
	confirmCancel: function(userId, accessTagId, isFree, hasCardOnFile)
	{
		var dialogId = 'mm-my-account';
		this.createDiv(dialogId);
		mmJQuery("#"+dialogId).dialog({autoOpen: false, buttons: {
			"Cancel Purchase": function() { myAccountJs.cancelAccessTag(userId, accessTagId, isFree, hasCardOnFile); },
			"Nevermind": function() { myAccountJs.closeDialog(); }}});
		var values =  {};
		values.mm_action = "confirmAccessTagCancel";
		values.access_tag_id = accessTagId;
		
		mmdialog_js.showDialog(dialogId, this.module, 400, 182, "Confirm Cancellation", values);
	},
	
	cancelAccessTag: function(userId, accessTagId, isFree, hasCardOnFile){
		this.id = userId;
		
	    var form_obj = new MM_Form('mm-form-container');
	    var values = form_obj.getFields();
		values.mm_id = this.id;
		values.mm_access_tag_id = accessTagId;
		values.mm_action = "deactivateAccessTag";
	    
	    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
	    ajax.send(values, false, 'myAccountJs', "cancelAccessTagCallback"); 
	},
	
	cancelAccessTagCallback: function(data){
		if(data.type){
			if(data.type=='error' && data.message){
				alert(data.message);
				return false;
			}
			else if(data.type=='error'){
				alert("Unable to cancel subscription, contact the site administrator.");
				return false;
			}
		}
		document.location.reload();
		
	},
});


var myAccountJs = new MM_MyAccountJs("MM_MyAccountView", "Member");