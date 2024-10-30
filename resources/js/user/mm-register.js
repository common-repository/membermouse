/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_RegistrationView = MM_Core.extend({
  
  nextStep: function()
  {
	var step = mmJQuery("#mm_step").val();
	var nextStep = mmJQuery("#mm_next_step").val();
	var isFree = mmJQuery("#is_free").val();
	
	var hasAdditionalFields = false;
	if(mmJQuery("#mm-steps5").length){
		hasAdditionalFields = true;
	}
	
	if(nextStep=='done')
	{
		this.createMember();
		return true;
	}
	else if(isFree=='1' && step=='step2' && !hasAdditionalFields)
	{
	  	var ret = this.validateFields(step);
	  	
	  	if(ret !='')
	  	{
	  		mmdialog_js.displayMessage(ret);
	  		return false;
	  	}
		this.createMember();
		return true;
	}
	else if(isFree=='1' && step=='additional' && hasAdditionalFields)
	{
	  	var ret = this.validateFields(step);
	  	
	  	if(ret !='')
	  	{
	  		mmdialog_js.displayMessage(ret);
	  		return false;
	  	}
		this.createMember();
		return true;
	}
	
	this.processForm();
	
  	var ret = this.validateFields(step);
  	
  	if(ret !='')
  	{
  		mmdialog_js.displayMessage(ret);
  		return false;
  	}
  	
    var form_obj = new MM_Form('mm_registration_table');
    var values = form_obj.getFields();
    
    // TEST ONLY
    //form_obj.dump();
    
    values.step=mmJQuery("#mm_next_step").val();
    values.mm_action = "getNextStep";
    values.type ='displayonly';

    if(step=='step1')
    {
    	var member_type = mmJQuery("input[type='radio']:checked").val();
    	values.mm_order_member_type = member_type;
    }
    	
    var ajax = new MM_Ajax('wp-admin/admin-ajax.php', this.module, this.action, this.method);
    ajax.send(values, false, 'mmjs','getStepCallback', 'html'); 

  },
  
  toggleStateList: function(usaID, type, val){
	  
	  var isUs = (mmJQuery("#mm-order-"+type+"-country").val() == usaID)?true:false;
	  
	 if(isUs){
		 mmJQuery("#mm-order-"+type+"-state-txt").hide();
		 mmJQuery("#mm-order-"+type+"-state-sel").show();
		 
		 if(val != undefined){
			 mmJQuery("#mm-order-"+type+"-state-sel").val(val);
		 }
	 }
	 else{
		 mmJQuery("#mm-order-"+type+"-state-txt").show();
		 mmJQuery("#mm-order-"+type+"-state-sel").hide();
	 }
  },
  
  updateBillingState: function(ext){
	  var val = mmJQuery("#mm-order-billing-state-"+ext).val();
	  mmJQuery("#mm-order-billing-state").val(val);
  },
  
  updateShippingState: function(ext){
	  var val = mmJQuery("#mm-order-shipping-state-"+ext).val();
	  mmJQuery("#mm-order-shipping-state").val(val);
  },
  
  processForm: function()
  {
 	if(mmJQuery('#mm-cb-order-shipping-same-as-billing:checked').val() != undefined) {
 	
 		mmJQuery("#mm-order-shipping-address-form").hide();  
 		mmJQuery("#mm-order-shipping-same-as-billing").attr('value', 'YES');
 		
 		// populate shipping form
 		mmJQuery("#mm-order-shipping-address").attr('value', mmJQuery('#mm-order-billing-address').val());
 		mmJQuery("#mm-order-shipping-city").attr('value', mmJQuery('#mm-order-billing-city').val());
 		mmJQuery("#mm-order-shipping-state").attr('value', mmJQuery('#mm-order-billing-state').val());
 		mmJQuery("#mm-order-shipping-zip").attr('value', mmJQuery('#mm-order-billing-zip').val());
 		mmJQuery("#mm-order-shipping-country").attr('value', mmJQuery('#mm-order-billing-country').val());
 	} else {
 		mmJQuery("#mm-order-shipping-address-form").show();
 		mmJQuery("#mm-order-shipping-same-as-billing").attr('value', 'NO');
 	}
  },
  
  getGateway: function(){
	var values = {};
    var gatewayId = mmJQuery("#mm-order-payment-choice").val();
    if(gatewayId<=0){
    	alert("Please choose a valid payment");
    	return false;
    }
	
	values.mm_action = "getGateway";
    values.gateway_id = mmJQuery("#mm-order-payment-choice").val();
    
    var ajax = new MM_Ajax('wp-admin/admin-ajax.php', this.module, this.action, this.method);
    ajax.send(values, false, 'mmjs','getGatewayCallback');
  },
  
  getGatewayCallback: function(data){
	 if(data.message.hidden_onsite ==undefined){
		alert("Error on the given payment option");
		return false;
	 }
	 if(data.message.hidden_onsite=='1'){
		 mmJQuery("#mm-payment-options").show();
	 }
	 else{
		 mmJQuery("#mm-payment-options").hide();
	 }
  },
  
  createMember: function()
  {
		var form_obj = new MM_Form('mm_registration_table');
	    var values = form_obj.getFields();
	      
	    values.mm_action = "placeNewOrder";
	    values.type = "json";
	    values.step=mmJQuery("#mm_step").val();
	    
	    var ajax = new MM_Ajax('wp-admin/admin-ajax.php', this.module, this.action, this.method);
	    ajax.send(values, false, 'mmjs', "goToCorePage"); 
		
  },
  
  prevStep: function()
  {
	  	var step = mmJQuery("#mm_step").val();
	  	
	    var form_obj = new MM_Form('mm_registration_table');
	    var values = form_obj.getFields();
	    values.step=mmJQuery("#mm_prev_step").val();;
	    values.mm_action = "getPrevStep";
	    values.type ='displayonly';
	    
	    // TEST ONLY
	    //form_obj.dump();
	    
	    var ajax = new MM_Ajax('wp-admin/admin-ajax.php', this.module, this.action, this.method);
	    ajax.send(values, false, 'mmjs','getStepCallback', 'html'); 
  },
  
  createFormSubmit: function(params, submitButtonId){
	if(params!=null){
		var html  = "<form id='mm-paymentmethod' action='"+params.url+"' method='post'>";
		for(var eachvar in params){
			html+= "<input type='hidden' name='"+eachvar+"' value='"+params[eachvar]+"' />";
		}
		html+="</form>";
		//alert(html);
		mmJQuery("body").append(html);
		if(submitButtonId != undefined){
			mmJQuery("#"+submitButtonId).submit();
		}
		else{
			mmJQuery("#mm-paymentmethod").submit();
		}
		
	}
  },
  
  
  //// callbacks
  goToCorePage: function(data)
  {
	 if(data.type == 'error')
	 {
		mmdialog_js.displayMessage("Registration Error: "+data.message);
		return false;
	 }
	 if(data.message)
	 {
		 if(this.shouldRedirectExternal(data.message)){
			 if(data.message.url != undefined){
				 this.createFormSubmit(data.message);
			 }
			 else{
				 document.location.href=data.message;
			 }
		 }
		return true;
	 }
	mmdialog_js.displayMessage("Could not find core page.");
  },
  
  
  
  // menu helper funcs
  removeClassFromBar: function(step){
	  for(i=1; i<=5; i++){
		if(mmJQuery("#mm-steps"+i).length){
			var name=  "#mm-steps"+i;
			 mmJQuery(name).removeAttr("class");
			 var currentText = mmJQuery(name).text();
			 var index = currentText.indexOf(":");
			 var newVal = currentText.substring(0, index);
			 if(index>0){
				mmJQuery(name).text(newVal);
			 }
		}
	  }
  },
  
  setMarked: function(currentStepProgressId){
	mmJQuery("#"+currentStepProgressId).attr("class", "mm-mark");
	
	var currentText = mmJQuery("#"+currentStepProgressId).text();
	var index = currentText.indexOf(":");
	var newVal = currentText.substring(0, index);
	if(index>0){
		mmJQuery("#"+currentStepProgressId).text(newVal);
	}
	
  },
  
  setActive: function(currentStepProgressId){
	var name =currentStepProgressId+"-name";
	var currentText = mmJQuery("#"+currentStepProgressId).text();
	var newText = mmJQuery("#"+name).val();
	
	if(mmJQuery("#"+name).length && currentText != newText){
		mmJQuery("#"+currentStepProgressId).text(newText);
	}
	mmJQuery("#"+currentStepProgressId).attr("class", "mm-active");
  },
  // end menu helper funcs
  
  showStepProgress: function(step){
	  this.removeClassFromBar(step);
	  
	  var hasCustomFields = false;
	  if(mmJQuery("#mm-steps5").length){
		  hasCustomFields = true;
	  }
	  var isCheckout = false;
	  if(mmJQuery("#mm-is-checkout").length){
		  isCheckout = true;
	  }
	  
	  switch(step){
	  	case 'step1':
	  		this.setActive('mm-steps1');
	  		break;
	  	case 'step2':
	  		this.setMarked('mm-steps1');
	  		this.setActive('mm-steps2');
			break;
	  	case 'step3':
	  		this.setMarked('mm-steps1');
	  		this.setMarked('mm-steps2');
	  		if(hasCustomFields && !isCheckout){
		  		this.setMarked('mm-steps3');
		  		this.setActive('mm-steps4');
	  		}
	  		else{
		  		this.setActive('mm-steps3');	
	  		}
			break;
	  	case 'step4':
	  		this.setMarked('mm-steps1');
	  		this.setMarked('mm-steps2');
	  		this.setMarked('mm-steps3');
	  		if(hasCustomFields && !isCheckout){
		  		this.setMarked('mm-steps4');
		  		this.setActive('mm-steps5');
	  		}
	  		else{
		  		this.setActive('mm-steps4');
	  		}
			break;
	  	case 'additional':
	  		this.setMarked('mm-steps1');
	  		this.setMarked('mm-steps2');
	  		this.setActive('mm-steps3');
			break;
	  }
  },
  
  setFree: function(isFree){
	  if(isFree=='1')
	  {
		if(mmJQuery("#mm-steps5").length){
	 		mmJQuery("#mm-steps4").hide();
	 		mmJQuery("#mm-steps5").hide();
		}
		else{
	 		mmJQuery("#mm-steps3").hide();
	 		mmJQuery("#mm-steps4").hide();
	 		mmJQuery("#mm-steps5").hide();	
		}
	  }
	  else{
 		mmJQuery("#mm-steps3").show();
 		mmJQuery("#mm-steps4").show();
 		mmJQuery("#mm-steps5").show();  
	  }
  },
  
  getStepCallback: function(data)
  {
 	  mmJQuery("#mm_registration_table tr:not(:last)").remove();
 	  mmJQuery("#mm_registration_table").prepend(data);
 	  
 	  var step = mmJQuery("#mm_step").val();

 	  if(step!='step1')
 	  {
 		 if(mmJQuery("#mm-is-checkout").length){
 			 mmJQuery(".main-heading h1").html("Checkout");
 			 if(step != 'step3'){
 				mmJQuery("#mm-back").show(); 
 			 }
 			 else{
 				mmJQuery("#mm-back").hide(); 
 			 }
 		 }
 		 else{
 			 mmJQuery("#mm-back").show();
 		 }
 	  }
 	  else
 		 mmJQuery("#mm-back").hide();
 	  
 	 this.showStepProgress(step);
 	  
 	  if(step=='step2')
 	  {
 		  var isFree = mmJQuery("#is_free").val();
 		  this.setFree(isFree);
 	  }
 	  else if(step=='step3')
 	  {
 		 this.processForm();
 	  }
  },

  //validation

  validateStep4Fields: function()
  {
	  return '';
  },
  
  validateStep3Fields: function()
  {
	if(mmJQuery('#mm-order-first-name').val() == "") {
		mmJQuery("#mm-order-first-name").focus();
		return "First name is required";
		
	}  
	
	if(mmJQuery('#mm-order-last-name').val() == "") {
		mmJQuery("#mm-order-last-name").focus();
		return "Last name is required";
		
	}  
	
	if(mmJQuery('#mm-order-billing-address').val() == "") {
		return "Billing address is required";
		
	}  
	
	if(mmJQuery('#mm-order-billing-city').val() == "") {
		mmJQuery("#mm-order-billing-city").focus();
		return "Billing city is required";
		
	}
	
	if(mmJQuery('#mm-order-billing-state').val() == "") {
		mmJQuery("#mm-order-billing-state").focus();
		return "Billing state is required";
		
	}
	
	if(mmJQuery('#mm-order-billing-zip').val() == "") {
		mmJQuery("#mm-order-billing-zip").focus();
		return "Billing zip code is required";
		
	}
	var phone = mmJQuery('#mm-order-phone').val().replace(/[^0-9]+/, "");
	
	if(phone.length<10) {
		mmJQuery("#mm-order-phone").focus();
		return "Phone number is not valid.";
	}
//	if(mmJQuery('#mm-order-phone').val() == "" || !this.validatePhone(mmJQuery('#mm-order-phone').val())) {
//		mmJQuery("#mm-order-phone").focus();
//		return "Phone number is not valid.";
//		
//	}

	if(mmJQuery("#mm-order-cc-exp-month").is(":visible")){
			
		if(mmJQuery('#mm-order-cc-number').val() == "") {
			mmJQuery("#mm-order-cc-number").focus();
			return "Credit card number is required";
			
		}
	
	
		if(!this.validateCreditDate(mmJQuery("#mm-order-cc-exp-year").val(),mmJQuery("#mm-order-cc-exp-month").val()))
		{
			mmJQuery("#mm-order-cc-exp-month").focus();
			return "Credit card date is not valid";
		}
		
		if(mmJQuery('#mm-order-cc-security-code').val() == "") {
			mmJQuery("#mm-order-cc-security-code").focus();
			return "Credit card security code is required";
			
		}

	
	}
	if(mmJQuery('#mm-order-shipping-same-as-billing :checked').val() == null) 
	{	
		if(mmJQuery('#mm-order-shipping-address').val() == "") {
			mmJQuery("#mm-order-shipping-address").focus();
			return "Shipping address is required";
			
		}  
		
		if(mmJQuery('#mm-order-shipping-city').val() == "") {
			mmJQuery("#mm-order-shipping-city").focus();
			return "Shipping city is required";
			
		}
		
		if(mmJQuery('#mm-order-shipping-state').val() == "") {
			mmJQuery("#mm-order-shipping-state").focus();
			return "Shipping state is required";
			
		}
		
		if(mmJQuery('#mm-order-shipping-zip').val() == "") {
			mmJQuery("#mm-order-shipping-zip").focus();
			return "Shipping zip code is required";
			
		}
	}
	return "";
  },
  
  validateStep2Fields: function()
  {
	  var username = mmJQuery("#mm-order-username").val();
	  var password1 = mmJQuery("#mm-order-password").val();
	  var password2 = mmJQuery("#mm-order-password-confirm").val();
	  var email1 = mmJQuery("#mm-order-email").val();
	  var email2 = mmJQuery("#mm-order-email-confirm").val();
	  var requiresTerms = mmJQuery("#mm_has_terms").val();
	  
	  if(requiresTerms=='1')
	  {
		if(!mmJQuery('#mm-agree').is(':checked')) 
		{
			mmJQuery('#mm-agree :checked').focus();
			return "You must agree to the terms and conditions.";
		}
	  }
	  
	  if(username.length<4)
	  {
		 mmJQuery("#mm-order-username").focus();
		 return "Username must be greater than 3 characters.";
	  }
	  if(!this.validateEmail(email1))
	  {
		 mmJQuery("#mm-order-email").focus();
		 return "Email is invalid.";  
	  }
	  if(email1 != email2)
	  {
		 mmJQuery("#mm-order-email").focus();
		 return "Emails should match.";
	  }
	  
	  if(!this.validatePassword(mmJQuery("#mm-order-password").val()))
	  {
		 mmJQuery("#mm-order-password").focus();
		 return "Password must be greater than 6 characters.";
	  }
	  if(password1 != password2)
	  {
		 mmJQuery("#mm-order-password").focus();
		 return "Passwords should match.";
	  }
	  
	  
	  return '';
  },
  
  validateStep1Fields: function()
  {
	  if(mmJQuery("#mm-order-member-type").length)
	  {
		  var member_type = mmJQuery("input[type='radio']:checked").val();
		  
		  if(member_type==undefined || member_type == '')
		  {
			  return 'Member Type must be selected.';
		  }
		  return '';
	  }
	  return "Member Type must be selected.";  
  },
  
  validateAdditionalFields: function(){
	  if(requiredFields != undefined){
		  for(var eachvar in requiredFields){
			if(mmJQuery("#"+eachvar).val() == ''){
				return requiredFields[eachvar]+" is required.";
			}
		  }
	  }
	  return '';
  },
  
  validateFields: function(step)
  {
	 var func ="validate"+this.ucfirst(step)+"Fields()";
	 try{
		 if(eval("this."+func)){
			 return eval("this."+func);
		 }
	 }
	 catch(e){
		 
	 }
	 return '';
  },
  
});


var mmjs = new MM_RegistrationView("MM_RegistrationView", "Registration");

