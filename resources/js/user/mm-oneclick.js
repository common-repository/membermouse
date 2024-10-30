/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

var MM_OneClickView = MM_Core.extend({

	createDiv: function(id)
	{
		mmJQuery("<div id='"+id+"'></div>").hide().appendTo("body").fadeIn();
	},
	
	confirmPurchase: function(productId, userId, accessTagId, standardPaymentMethod)
	{
		
		var dialogId = 'mm-one-click';
		this.createDiv(dialogId);
		mmJQuery("#"+dialogId).dialog({autoOpen: false, buttons: {
			"Purchase": function() { mmOneClick.purchaseProduct(userId, accessTagId); },
			"Cancel": function() { mmOneClick.closeDialog(); }}});
		var values =  {};
		values.mm_action = "confirmOneClick";
		values.mm_module = 'one_click';
		values.product_id = productId;
		values.payment_method = "limelight";
		if(standardPaymentMethod != undefined){
			values.payment_method = standardPaymentMethod;
		}	
		
		mmdialog_js.showDialog(dialogId, this.module, 400, 182, "Confirm Purchase", values);
	},

	createFormSubmit: function(params){
		if(params!=null){
			var html  = "<form id='mm-paymentmethod' action='"+params.url+"' method='post'>";
			for(var eachvar in params){
				html+= "<input type='hidden' name='"+eachvar+"' value='"+params[eachvar]+"' />";
			}
			html+="</form>";

			mmJQuery("body").append(html);
			mmJQuery("#mm-paymentmethod").submit();
		}
	},
	
	purchaseProduct: function(userId, accessTagId)
	{
		var form_obj = new MM_Form('mm-form-container');
	    var values = form_obj.getFields();
		values.mm_id = userId;
		values.product_id = mmJQuery("#product_id").val();
		values.mm_access_tag_id = accessTagId;
		values.mm_action = "purchaseAccessTag";
		values.payment_method = mmJQuery("#payment_method").val();
		
		
		mmJQuery("#mm-progressbar-container").show();
	    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
	    ajax.send(values, false, 'mmOneClick', "handleCallback"); 
	},
	
	handleCallback: function(data)
	{
		if(data.message.gateway != undefined){
			if(data.message.gateway.error != undefined){
				mmJQuery("#mm-progressbar-container").hide();
				this.closeDialog();
				var dialogId = 'mm-one-click-response';
				this.createDiv(dialogId);
				mmJQuery("#"+dialogId).dialog({autoOpen: false, buttons: {
					"OK": function() { mmOneClick.closeDialog(); }}});
				var values =  {};
				values.mm_action = "responseDialog";
				values.mm_module = 'one_click_response';
				values.message = data.message.gateway.error;
				
				mmdialog_js.showDialog(dialogId, this.module, 400, 182, "Purchase Response", values);
				return true;
			}
			else{
				this.createFormSubmit(data.message.gateway);
				return true;
			}
		}
		
		if(data.message.url != undefined){
			document.location.href= data.message.url;
			return true;
		}
		
		var message = data.message;
		mmJQuery("#mm-progressbar-container").hide();
		this.closeDialog();
		var dialogId = 'mm-one-click-response';
		this.createDiv(dialogId);
		mmJQuery("#"+dialogId).dialog({autoOpen: false, buttons: {
			"OK": function() { mmOneClick.closeDialog(); }}});
		var values =  {};
		values.mm_action = "responseDialog";
		values.mm_module = 'one_click_response';
		values.message = message;
		
		mmdialog_js.showDialog(dialogId, this.module, 400, 182, "Purchase Response", values);
	},
	
});

var mmOneClick = new MM_OneClickView("MM_OneClickView", "One Click Buy");