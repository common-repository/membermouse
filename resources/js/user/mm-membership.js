/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

var MM_MembershipView = MM_Core.extend({

	
	createDiv: function(id)
	{
		mmJQuery("<div id='"+id+"'></div>").hide().appendTo("body").fadeIn();
	},
	
	freeToPaid: function(url){
		document.location.href=  url;
	},
	
	fastForwardPrompt: function(price, days){
			var dialogId = 'mm-ff-membership';
			this.createDiv(dialogId);
			mmJQuery("#"+dialogId).dialog({autoOpen: false, buttons: {
				"Fast Forward Membership": function() { mmMembershipJs.fastForwardMembership(); },
				"Nevermind": function() { mmMembershipJs.closeDialog(); }}});
			var values =  {};
			values.mm_action = "ffDialog";
			values.mm_module = this.module;
			values.mm_days = days;
			values.mm_price = price;
	  
			mmdialog_js.showDialog(dialogId, this.module, 400, 132, "Fast Forward "+this.entityName, values);
	},
	
	fastForwardMembership: function(){
		var values = {};
		values.mm_action = "ffMembership";
		values.price = mmJQuery("#mm-price").val();
		values.days = mmJQuery("#mm-days").val();

		var module = "MM_MembershipView";
		var method = "performAction";
		var action = 'module-handle';
      
		var ajax = new MM_Ajax(false, module, action, method);
		ajax.send(values, false, 'mmMembershipJs','handleFFCallback');
	},
	
	handleFFCallback: function(data){

		this.closeDialog();
		
		if(data.type=='error')
		{
			alert(data.message);
		}
		if(data.message.url != undefined){
			if(this.shouldRedirectExternal(data.message)){
				if(data.message.is_standard !=undefined){
					this.createFormSubmit(data.message);
				}
				else{
					document.location.href= data.message.url;
				}
			}
		}
		else{
			alert(data.message);
		}
	},
	
	confirmReactivate: function(url){
		var ret = confirm("Are you sure you want to reactivate your membership?");
		if(ret){
			document.location.href=url;
		}
	},
	
	pauseMembershipConfirm: function(url)
	{
		var dialogId = 'mm-pause-membership';
		this.createDiv(dialogId);
		mmJQuery("#"+dialogId).dialog({autoOpen: false, buttons: {
			"Cancel Membership": function() { mmMembershipJs.pauseMembership(); },
			"Nevermind": function() { mmMembershipJs.closeDialog(); }}});
		var values =  {};
		values.mm_action = "pauseDialog";
		values.mm_module = this.module;
		values.redirect_url = url;
  
		mmdialog_js.showDialog(dialogId, this.module, 400, 132, "Cancel "+this.entityName, values);
	},
	
	cancelMembershipConfirm: function(url)
	{
		var dialogId = 'mm-cancel-membership';
		this.createDiv(dialogId);
		mmJQuery("#"+dialogId).dialog({autoOpen: false, buttons: {
			"Cancel Membership": function() { mmMembershipJs.cancelMembership(); },
			"Nevermind": function() { mmMembershipJs.closeDialog(); }}});
		var values =  {};
		values.mm_action = "cancelDialog";
		values.mm_module = this.module;
		values.redirect_url = url;
  
		mmdialog_js.showDialog(dialogId, this.module, 400, 132, "Cancel "+this.entityName, values);
	},

	changeMembershipConfirm: function(userId, memberTypeId, errors)
	{
		if(errors.length>0){
			mmdialog_js.displayMessage(errors);
		}
		else{
			var dialogId = 'mm-change-membership';
			this.createDiv(dialogId);
			
			mmJQuery("#"+dialogId).dialog({autoOpen: false, buttons: {
				"Change Membership": function() { mmMembershipJs.changeMembership(userId,dialogId); },
				"Cancel": function() { mmMembershipJs.closeDialog(); }}});
			var values =  {};
			values.mm_action = "changeMembershipDialog";
			values.mm_module = this.module;
			values.member_type_id = memberTypeId;
	  
			mmdialog_js.showDialog(dialogId, this.module, 500, 212, "Change "+this.entityName, values);
		}
	},
	
	changeMembership: function(userId,dialogId){
		mmdialog_js.close(dialogId);
		
	    var values = {};
		values.mm_id = userId;
		values.mm_action = "changeMembership";
	    values.mm_new_membership_selection = mmJQuery("#mm-member-type-id").val();
	    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
	    ajax.send(values, false, 'mmMembershipJs', "handleMemberTypeChange"); 
	},
	
	handleMemberTypeChange: function(data){
		if(data.type == 'error'){
			mmdialog_js.displayMessage(data.message);
		}
		else
		{
			 if(data.message.url != undefined){
				if(this.shouldRedirectExternal(data.message)){
					this.createFormSubmit(data.message);
				}
			 }
			 else{
				alert("Could not change membership, please notify the administration"); 
			 }
			//document.location.href = data.message.url;
		}
	},
	
	pauseMembership: function()
	{
		var url = mmJQuery("#mm-membership-pause-redirect").val();

		var values = {};
		values.mm_should_show = '0';
		values.mm_action = "pauseMembership";
		values.redirect_url = url;

		var module = "MM_MembershipView";
		var method = "performAction";
		var action = 'module-handle';
      
		var ajax = new MM_Ajax(false, module, action, method);
		ajax.send(values, false, 'mmMembershipJs','handleCancellationCallback');
	},
	
	cancelMembership: function()
	{
		var url = mmJQuery("#mm-membership-cancellation-redirect").val();

		var values = {};
		values.mm_should_show = '0';
		values.mm_action = "cancelMembership";
		values.redirect_url = url;

		var module = "MM_MembershipView";
		var method = "performAction";
		var action = 'module-handle';
      
		var ajax = new MM_Ajax(false, module, action, method);
		ajax.send(values, false, 'mmMembershipJs','handleCancellationCallback');
	},
	
	handleCancellationCallback: function(data)
	{
		this.closeDialog();
		
		if(data.type=='error')
		{
			mmdialog_js.displayMessage(data.message);
		}
		else
		{
			if(data.message.indexOf("http")>=0)
			{
				if(this.shouldRedirectExternal(data.message)){
					document.location.href=data.message;
				}
				return true;
			}
		}
	},
	
});

var mmMembershipJs = new MM_MembershipView("MM_MembershipView", "Membership");