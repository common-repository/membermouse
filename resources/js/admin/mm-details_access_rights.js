/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_MemberDetailsViewJS = MM_Core.extend({

	editCalcMethod: function(appliedId){

		var dialogId = 'mm-edit-calc-method-dialog';
		mmJQuery("#"+dialogId).dialog({autoOpen: false, buttons: {
			"Save": function() { mmjs.saveCalcMethod(appliedId); },
			"Cancel": function() { mmjs.closeDialog(); }}});
		var values =  {};
		values.mm_action = "editCalcMethod";
		values.mm_module = 'editCalcMethod';
		values.applied_id = appliedId;
		
		
		mmdialog_js.showDialog(dialogId, this.module, 640, 282, "Update Calc Method", values);
	},

	setCalcMethod: function(method){
		mmJQuery("#mm-calc-method").val(method);
		mmJQuery("#mm-custom-date").val("");
		mmJQuery("#mm-fixed").val("");
	},
	
	saveCalcMethod: function(){
		var form_obj = new MM_Form('mm-calc-method-div');
	    var values = form_obj.getFields();
		values.mm_action = "saveCalcMethod";
	    
	    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
	    ajax.send(values, false, 'mmjs', "saveCalcMethodCallback"); 
	},
	
	saveCalcMethodCallback: function(response){
		alert(response.message);
		mmjs.closeDialog();
	},
	
	lockAccount: function(id, productName, flagNoPay)
	{	
		var msg = "Are you sure you want to lock this account?\n\nWhen a member's account is locked they won't be able to login.";
		
		if(productName != "") {
			msg += "\n\nRebills associated with the product '" + productName + "' won't be effected.";
		}
		
		var doLock = confirm(msg);
		
		if(doLock) {
			this.id = id; 
			
			var form_obj = new MM_Form('mm-form-container');
		    var values = form_obj.getFields();
			values.mm_id = this.id;
			values.mm_action = "lockAccount";
		    
		    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		    ajax.send(values, false, 'mmjs', "detailsUpdateHandler"); 
		}
	},
	
	unlockAccount: function(id)
	{	
		var doUnlock = confirm("Are you sure you want to unlock this account?");
		
		if(doUnlock) {
			this.id = id; 
			
			var form_obj = new MM_Form('mm-form-container');
		    var values = form_obj.getFields();
			values.mm_id = this.id;
			values.mm_action = "unlockAccount";
		    
		    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		    ajax.send(values, false, 'mmjs', "detailsUpdateHandler"); 
		}
	},

	cancelMembership: function(id, productName, hasActiveSubscriptions)
	{	
		if(hasActiveSubscriptions) {
			alert("Please deactivate all access tags with associated orders before cancelling the membership.");
			return false;
		}
		
		var msg = "Are you sure you want to cancel this membership?";
		
		if(productName != "") {
			msg += "\n\nRebills associated with the product '" + productName + "' will be canceled.";
		}
		
		var doCancel = confirm(msg);
		
		if(doCancel) {
			this.id = id; 
			
		    var form_obj = new MM_Form('mm-form-container');
		    var values = form_obj.getFields();
			values.mm_id = this.id;
			values.mm_action = "cancelMembership";
		    
		    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		    ajax.send(values, false, 'mmjs', "detailsUpdateHandler"); 
		}
	},
	
	pauseMembership: function(id, productName, hasActiveSubscriptions)
	{	
		if(hasActiveSubscriptions) {
			alert("Please deactivate all access tags with associated orders before cancelling the membership.");
			return false;
		}
		
		var msg = "Are you sure you want to pause this membership?";
		
		if(productName != "") {
			msg += "\n\nRebills associated with the product '" + productName + "' will be canceled.";
		}
		
		var doPause = confirm(msg);
		
		if(doPause) {
			this.id = id; 
			
		    var form_obj = new MM_Form('mm-form-container');
		    var values = form_obj.getFields();
			values.mm_id = this.id;
			values.mm_action = "pauseMembership";
		    
		    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		    ajax.send(values, false, 'mmjs', "detailsUpdateHandler"); 
		}
	},
	
	activateMembership: function(id, productName, flagNoPay)
	{	
		var msg = "Are you sure you want to activate this membership?";
		
		if(productName != "") {
			msg += "\n\nRebills associated with the product '" + productName + "' will be started.";
		}
		
		if(flagNoPay != undefined){
			if(flagNoPay=="1"){
				msg = "Are you sure you want to continue this membership without collecting payment?";
			}
		}
		
		var doActivate = confirm(msg);
		
		if(doActivate) {
			this.id = id; 
			
			var form_obj = new MM_Form('mm-form-container');
		    var values = form_obj.getFields();
			values.mm_id = this.id;
			values.mm_action = "activateMembership";
		    
		    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		    ajax.send(values, false, 'mmjs', "detailsUpdateHandler"); 
		}
	},
	
	changeMembership: function(id, crntMemberTypeId,flagNoPay)
	{	
		
		if(crntMemberTypeId != mmJQuery("#mm-new-membership-selection").val()) 
		{
			var msg = "Are you sure you want to change this membership?";

			if(flagNoPay != undefined){
				if(flagNoPay=="1"){
					msg = "Are you sure you want to change this membership without changing payment?";
				}
			}
			
			var doChange = confirm(msg);
			
			if(doChange) {
				this.id = id; 
				
			    var form_obj = new MM_Form('mm-form-container');
			    var values = form_obj.getFields();
				values.mm_id = this.id;
				values.mm_action = "changeMembership";
			    
			    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
			    ajax.send(values, false, 'mmjs', "detailsUpdateHandler"); 
			}
		}
		else 
		{
			alert("Please select a different membership to change to");
		}
	},
	
	updatePaidProductSelection: function()
	{
	    var form_obj = new MM_Form('mm-form-container');
	    var values = form_obj.getFields();
		values.mm_action = "getProductName";
	    
	    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
	    ajax.send(values, false, 'mmjs', "paidProductSelectionHandler"); 
	},
	
	paidProductSelectionHandler: function(data)
	{
		if(data.message != undefined && data.message.length > 0) {
			  mmJQuery("#mm-paid-product").html(data.message);
			  this.determineCampaign();
		  }
		  else {
			  alert("No data received");
		  }
	},
	
	determineCampaign: function(){
	    var values = {};
		values.mm_membertype_id = mmJQuery("#mm-new-membership-paid-selection").val();
		values.mm_action = "determineProductCampaign";
	    
	    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
	    ajax.send(values, false, 'mmjs', "campaignCallback"); 
	},
	
	campaignCallback:function(data){
		if(data.type=='error'){
			alert(data.message);
		}
		else{
			mmJQuery("#mm-campaign-name").html(data.message.campaign_name+" ("+data.message.campaign_id+")");
			mmJQuery("#mm-campaign-id").val(data.message.campaign_id);
		}
	},
	
	attachOrder: function(id)
	{
		this.id = id; 
		
		if(this.validateForm())
		{
			var msg = "Are you sure you want to attach the order with ID '" + mmJQuery('#mm-attach-order-id').val() + "' to this member?";
			
			var doAttach = confirm(msg);
			
			if(doAttach) {
			    var form_obj = new MM_Form('mm-form-container');
			    var values = form_obj.getFields();
				values.mm_id = this.id;
				values.mm_campaign_id=mmJQuery("#mm-campaign-id").val();
				values.mm_action = "attachOrder";
			    
			    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
			    ajax.send(values, false, 'mmjs', "detailsUpdateHandler"); 
			}
		}
	},
	
	activateAccessTag: function(userId, accessTagId, isFree, hasCardOnFile, hasMultipleProducts,flagNoPay)
	{	
		if(isFree == false && hasCardOnFile == false && flagNoPay!="1") {
			alert("This access tag can't be activated for this member because there is no card on file");
			return false;
		}
		
		var msg = "Are you sure you want to activate this access tag?";
		
		if(isFree == false) {
			msg += "\n\nRebills associated with this access tag will be started.";
		}

		if(flagNoPay != undefined && !isFree){
			if(flagNoPay=="1"){
				msg = "Are you sure you want attribute this access tag to the user without collecting payment?";
			}
		}
		
		var doActivate = confirm(msg);
		
		if(doActivate) {
			this.id = userId;
			
			if(hasMultipleProducts) {
				this.selectProduct(userId, accessTagId, 400, 230);
			}
			else 
			{
			    var form_obj = new MM_Form('mm-form-container');
			    var values = form_obj.getFields();
				values.mm_id = this.id;
				
				values.mm_access_tag_id = accessTagId;
				values.mm_action = "activateAccessTag";
//				form_obj.dump();
				
			    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
			    ajax.send(values, false, 'mmjs', "detailsUpdateHandler"); 
			}
		}
	},
	
	deactivateAccessTag: function(userId, accessTagId, isFree, hasCardOnFile, flagPause)
	{	
		var msg = "Are you sure you want to deactivate this access tag?";
		
		if(isFree == false) {
			msg += "\n\nRebills associated with this access tag will be stopped.";
		}
		
		var doDeactivate = confirm(msg);
		
		if(doDeactivate) {
			this.id = userId;
			
		    var form_obj = new MM_Form('mm-form-container');
		    var values = form_obj.getFields();
			values.mm_id = this.id;
			values.mm_access_tag_id = accessTagId;
			values.mm_action = "deactivateAccessTag";

			if(flagPause!=undefined && flagPause=="1"){
				values.is_paused = 1;
			}
		    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		    ajax.send(values, false, 'mmjs', "detailsUpdateHandler"); 
		}
	},
	  
	selectProduct: function(userId, accessTagId, width, height)
	{
		var params = {
			userId: userId,
			accessTagId: accessTagId,
	        mm_module: this.getQuerystringParam("module")
		};

		mmdialog_js.showDialog("mm-select-product-dialog", this.module, width, height, "Select Product to Purchase", params);
	},
	  
	  save: function() 
	  {
		  this.processForm();
		  
		  if(this.validateForm() == true) {
		      var form_obj = new MM_Form('mm-select-product-form-container');
		      var values = form_obj.getFields();
		      
		      values.mm_action = "save";
		      
		      // TEST ONLY
		      //form_obj.dump();
		      
		      var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		      ajax.send(values, false, 'mmjs', "detailsUpdateHandler"); 
		      
			  mmdialog_js.close();
		  }
	  },
	
	processForm: function()
	{
		var productSelection = mmJQuery('#mm-products-container input:radio:checked').val();
	 	  
		mmJQuery("#mm-product-id").attr('value', productSelection);
	},
	
	validateForm: function()
	{
		if(mmJQuery('#mm-attach-order-id').val() == "") {
			alert("Order ID is required");
			return false;
		}
		
		return true;
	},
	
	detailsUpdateHandler: function(data)
	  {
		  if(data.type == "error")
		  {
			  if(data.message.length > 0)
			  {  
				  alert(data.message);
				  return false;
			  }
		  }
		  else {
			  
			  if(data.message.indexOf("http")>=0){
				  document.location.href = data.message;
				  return false;
			  }
			  
			  if(data.message != undefined && data.message.length > 0)
			  {
				  alert(data.message);
			  }
			  
			  this.refreshView();
		  }
	  },
	  
	  refreshView: function()
	  {
	    var values = {
	        user_id: this.id,
	        mm_page: this.getQuerystringParam("page"),
	        mm_module: this.getQuerystringParam("module"),
	        mm_action: "refreshView"
	    };
	    
	    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
	    ajax.send(values, false, 'mmjs','refreshViewCallback'); 
	  }
});

var mmjs = new MM_MemberDetailsViewJS("MM_MemberDetailsView", "Member");