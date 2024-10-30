/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_CustomFieldViewJS = MM_Core.extend({
    
	validateForm: function(){
		var fieldName = mmJQuery("#mm-field-name").val();
		var fieldLabel = mmJQuery("#mm-field-label").val();
		
		var validField = new RegExp('^[a-zA-Z\_0-9]+$','g');
		if(!validField.test(fieldName)){
			alert("Field Name must be alpha-numeric with no spaces.");
			return false;
		}
		
		if(fieldLabel.length<=0){
			alert("Please enter a value for field label.");
			return false;
		}
		return true;
	},

	setShowOnReg: function(){
		if(mmJQuery("#mm-show-on-reg-field").is(":checked")){
			mmJQuery("#mm-show-on-reg").val("1");
		}
		else{
			mmJQuery("#mm-show-on-reg").val("0");
		}
	},

	setShowOnMyAccount: function(){
		if(mmJQuery("#mm-show-on-myaccount-field").is(":checked")){
			mmJQuery("#mm-show-on-myaccount").val("1");
		}
		else{
			mmJQuery("#mm-show-on-myaccount").val("0");
		}
	},
	
	setIsRequired: function(){
		if(mmJQuery("#mm-is-required-field").is(":checked")){
			mmJQuery("#mm-is-required").val("1");
		}
		else{
			mmJQuery("#mm-is-required").val("0");
		}
	},
});

var mmjs = new MM_CustomFieldViewJS("MM_CustomFieldView", "Custom Field");