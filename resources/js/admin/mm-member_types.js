/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_MemberTypesViewJS = MM_Core.extend({
	
	setDefault: function(id)
	{
		var doSet = confirm("Are you sure you want to set this member type as the default?");
	    
	    if(doSet)
	    {
	        var values = {
	            id:id,
	            mm_action: "setDefault"
	        };

	        var ajax = new MM_Ajax(false, this.module, this.action, this.method);
	        ajax.send(values, false, 'mmjs',this.updateHandler); 
	    }
	},
	
	welcomeEmailChanged: function(){
		if(!mmJQuery("#mm-welcome-email-enabled-field").is(":checked")){
			mmJQuery("#mm-welcome-email-row").hide();
			mmJQuery("#mm-welcome-email-enabled").val("0");
		}
		else{
			mmJQuery("#mm-welcome-email-row").show();
			mmJQuery("#mm-welcome-email-enabled").val("1");
		}
	},

	setRequiredMemberTypes: function(){

	    mmJQuery("select[id=mm-products\[\]] :disabled").each(function()
	    	    {
	    	mmJQuery(this).attr("selected","selected");
	    	mmJQuery(this).attr("disabled","disabled");
	    	    });
		
	},
	
	filterRegistrationProducts: function(){
	    var selected = mmJQuery("#mm-registration-product-id").val();
		mmJQuery("#mm-registration-product-id").find('option').remove().end();
		
		
		var options = new Array();
	    mmJQuery("select[id=mm-products\[\]] :selected").each(function()
	    {
		    	mmJQuery("#mm-registration-product-id").append("<option value='"+mmJQuery(this).val()+"'>"+mmJQuery(this).text()+"</option>");
	    });
	    
	    
	    mmJQuery("select[id=mm-products\[\]] :disabled").each(function()
	    	    {
	    			var val = mmJQuery(this).val();
	    			if(!mmJQuery.inArray(val, options)){
	    				mmJQuery("#mm-registration-product-id").append("<option value='"+mmJQuery(this).val()+"'>"+mmJQuery(this).text()+"</option>");
	    			}
	    	    });
	    
	    mmJQuery("#mm-registration-product-id").val(selected);
	   this.setRequiredMemberTypes();
	},
	
  processForm: function()
  {
	 if(mmJQuery("#mm-cb-include-on-reg").is(":checked")){
		mmJQuery("#mm-register-product").show();
	 }
	 else{
		 mmJQuery("#mm-register-product").hide();
	 }
		
 	  // status
 	  mmJQuery("#mm-status").attr('value', mmJQuery('#mm-status-container input:radio:checked').val());
 	  
 	  // subscribtion type
 	  var subTypeSelection = mmJQuery('#mm-subscription-container input:radio:checked').val();

 	  mmJQuery("#mm-subscription-type").attr('value', subTypeSelection);
 	  
 	  if(subTypeSelection == 'paid' && mmJQuery("#mm-has-associations").val() == "no") {
 		  mmJQuery("#mm-products\\[\\]").attr("disabled","");
 		  mmJQuery("#mm-registration-product-id").attr("disabled","");
 	  } 
 	  
 	  if(subTypeSelection == 'paid'){
 		  mmJQuery("#mm-products\\[\\]").show();
 		  mmJQuery("#mm-registration-page-settings").show();
 	  }
 	  else{
 		  mmJQuery("#mm-products\\[\\]").hide();
 		  mmJQuery("#mm-registration-page-settings").hide();
 	  }
 	  
 	  // registration settings
 	  if(mmJQuery('#mm-cb-include-on-reg:checked').val() != undefined) {
 		  mmJQuery("#mm-include-on-reg").attr('value', 'yes');
 		 mmJQuery("#mm-description-row").show();
 	  } else {
 		  mmJQuery("#mm-include-on-reg").attr('value', 'no');
 		  mmJQuery("#mm-description-row").hide();
 	  }
 	  

	    mmJQuery("select[id=mm-products\[\]] :disabled").each(function()
	    	    {
	    	mmJQuery(this).attr("selected","selected");
	    	mmJQuery(this).removeAttr("disabled");
	    	    });
  },
   
  validateForm: function()
  {
	   // display name 
	   if(mmJQuery('#mm-display-name').val() == "") {
		   alert("Display name is required");
		   return false;
	   }
	   
	   // subscription type
	   if(mmJQuery("#mm-subscription-type").val() == "paid" && mmJQuery("#mm-products").val() == "") {
		   alert("Please select a product or set subscription type to Free");
		   return false;
	   }
	   
	   // registration description
	   if(mmJQuery("#mm-include-on-reg").val() == "yes" && mmJQuery('#mm-description').val() == "") {
		   alert("Registration page description is required when the 'Show on Registration Page' option is checked");
		   return false;
	   }
	   
	   // email subject
	   if(mmJQuery("#mm-email-subject").val() == "") {
		   alert("Welcome email subject is required");
		   return false;
	   }
	   
	   // email body
	   if(mmJQuery("#mm-email-body").val() == "") {
		   alert("Welcome email body is required");
		   return false;
	   }
	   
	   return true;
  }
});

var mmjs = new MM_MemberTypesViewJS("MM_MemberTypesView", "Member Type");
