/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_ContentDeliveryJS = MM_Core.extend({

    toggleContentArea: function(){
		if(mmJQuery("#mm-content-delivery-fields").length){
			if(mmJQuery("#mm-content-delivery-send").is(":checked")){
				mmJQuery("#mm-content-delivery-fields").show();
			}
			else{
				mmJQuery("#mm-content-delivery-fields").hide();
			}
		} 
	},
	
	hideNotification: function(){
		mmJQuery("#membermouse_post_content_delivery").hide();
	},

	showNotification: function(){
		mmJQuery("#membermouse_post_content_delivery").show();
	},
});

var contentDelivery = new MM_ContentDeliveryJS("MM_ContentDeliveryView", "Content Delivery");