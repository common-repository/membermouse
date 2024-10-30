/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_OrderHistoryViewJS = MM_Core.extend({
	refundOrder: function(orderId, userId,refundAmt, productId,shouldCancel){
		mmjs.closeDialog();
		var isOk = confirm("Are you sure you want to refund $"+refundAmt+" to this customer?");
		if(isOk){
		    var values = {
		        order_id:orderId,
		        user_id: userId,
		        should_cancel: shouldCancel,
		        product_id: productId,
		        amount: refundAmt,
		        mm_action: "refundOrder"
		    };
		    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		    ajax.send(values, false, 'mmjs', "refundOrderCallback");
		}
	},
	
	refundOrderConfirm: function(orderId, userId,refundAmt, productId){

		var dialogId = 'mm-choose-refund-options';
		mmJQuery("#"+dialogId).dialog({autoOpen: false, buttons: {
			"Refund/Keep": function() { mmjs.refundOrder(orderId, userId,refundAmt, productId,'0'); },
			"Refund/Cancel": function() { mmjs.refundOrder(orderId, userId,refundAmt, productId,'1'); }}});
		var values =  {};
		values.mm_action = "showRefundOptions";
		values.mm_module = 'showRefundOptions';
		
		mmdialog_js.showDialog(dialogId, this.module, 440, 182, "Refund Confirmation", values);
	},
	
	refundOrderCallback: function(response){
		alert(response.message);
		window.location.reload();
	},
	
});

var mmjs = new MM_OrderHistoryViewJS("MM_OrderHistoryView", "Order History");