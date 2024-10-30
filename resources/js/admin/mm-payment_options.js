/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_PaymentViewJS = MM_Core.extend({
    
	validateForm: function(){
	
		return true;
	},
	
	showPaymentOption: function(option){
		if(mmJQuery("#"+option).length){
			if(mmJQuery("#"+option).is(":checked")){
				mmJQuery("#payment_option_"+option).show();
			}
			else{
				mmJQuery("#payment_option_"+option).hide();
			}
		}
		else{
			alert("Could not find element "+option);
		}
		
	},	
	
	chooseGateway: function(){
		var option = mmJQuery("#mm-gateways").val();
		var id = mmJQuery("#mm_id").val();
        var values = {
            id:id,
            option: option,
            mm_action: "gateway_options"
        };

        var ajax = new MM_Ajax(false, this.module, this.action, this.method);
        ajax.send(values, false, 'mmjs','optionsHandler'); 
	},
	
	setShowOnReg: function(){
		if(mmJQuery("#mm-show-on-reg-chk").is(":checked")){
			mmJQuery("#mm-show-on-reg").val("1");
		}
		else{
			mmJQuery("#mm-show-on-reg").val("0");
		}
	},
	
	optionsHandler: function(data){
		if(data.type=='error'){
			mmJQuery("#mm_gateway_info_row").hide();
			alert(data.message);
		}
		else{
			mmJQuery("#mm_gateway_info_row").show();
			if(data.message.show_types!=undefined){
				if(data.message.show_types=='1'){
					mmJQuery("#mm-types").show();
				}
			}
			mmJQuery("#mm_gateway_info_table").find('tr').remove().end().append(data.message.html);
		}
	},

});

var mmjs = new MM_PaymentViewJS("MM_CampaignSettingsView", "Payment Method");