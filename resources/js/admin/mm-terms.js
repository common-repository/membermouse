/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_RegistrationSettingsViewJS = MM_Core.extend({
  
	save: function()
	{
		this.processForm();
		  
		  if(this.validateForm() == true) {
		      var form_obj = new MM_Form('mm-form-container');
		      var values = form_obj.getFields();
		      
		      values.mm_action = "saveTerms";
		      values.mm_module = "terms";
		      
		      // TEST ONLY
		      //form_obj.dump();
		      
		      var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		      ajax.send(values, false, 'mmjs', "dataUpdateHandler"); 
		  }
	},
	 
	  dataUpdateHandler: function(data)
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
			  if(data.message != undefined && data.message.length > 0)
			  {
				  alert(data.message);
			  }

			  this.refreshView();
		  }
	  },
	  
	  refreshView: function(data)
	  {
	    var values = {
	    	mm_module: "terms",
	        mm_action: "refreshView"
	    };
	    
	    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
	    ajax.send(values, false, 'mmjs','refreshViewCallback'); 
	  },
	
	processForm: function()
	{
		if(mmJQuery('#mm-cb-include-terms-on-reg:checked').val() != undefined) {
	 		  mmJQuery("#mm-include-terms-on-reg").attr('value', 'yes');
	 	  } else {
	 		  mmJQuery("#mm-include-terms-on-reg").attr('value', 'no');
	 	  }
		
		if(mmJQuery("#mm-include-terms-on-reg").val() == "yes") {
			mmJQuery("#mm-terms-and-conditions").show();
		} 
		else {
			mmJQuery("#mm-terms-and-conditions").hide();
		}
	},
	   
	validateForm: function()
	  {
		   // terms and conditions
		  if(mmJQuery('#mm-cb-include-terms-on-reg:checked').val() != undefined && mmJQuery("#mm-terms-and-conditions").val() == "") {
			   alert("Terms and Conditions are required");
			   return false;
		   }
		   
		   return true;
	  }
});

var mmjs = new MM_RegistrationSettingsViewJS("MM_RegistrationSettingsView", "");