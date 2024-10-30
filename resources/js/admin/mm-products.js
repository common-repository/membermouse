/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_ProductViewJS = MM_Core.extend({
    
	saveProduct: function(){
		var params = {};
		params.status = (mmJQuery("#status").is(":checked"))?"1":"0";
		this.save(undefined, params);
	},
	
	validateForm: function(){
		if(mmJQuery("#name").val() == ""){
			alert("You must provide a name");
			return false;
		}
		if(mmJQuery("#price").val() == ""){
			alert("You must provide a price");
			return false;
		}
		
		return true;
	},
	
	changeOption: function(id){
		if(mmJQuery("#"+id).is(":checked")){
			mmJQuery("#"+id+"_val").val("1");
		}
		else{
			mmJQuery("#"+id+"_val").val("0");
		}
	},

	toggleTrial: function(){
		if(mmJQuery("#is_trial").is(":checked")){
			mmJQuery("#mm_is_trial_row").show();
		}
		else{
			mmJQuery("#mm_is_trial_row").hide();
		}
		
		this.changeOption('is_trial');
	},
	
	showClickBankInfo: function(){
		if(mmJQuery("#mm_is_clickbank").is(":checked")){
			mmJQuery("#mm-clickbank-info").show();
		}
		else{
			mmJQuery("#mm-clickbank-info").hide();
		}
		this.changeOption('mm_is_clickbank');
	},
	
	toggleRecurring: function(){
		if(mmJQuery("#is_recurring").is(":checked")){
			mmJQuery("#mm_rebill_row").show();
		}
		else{
			mmJQuery("#mm_rebill_row").hide();
		}
		
		this.changeOption('is_recurring');
	},
});

var mmjs = new MM_ProductViewJS("MM_ProductView", "Product");