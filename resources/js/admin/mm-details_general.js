/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_MemberDetailsViewJS = MM_Core.extend({

	updateMember: function(id)
	{	
		this.id = id;
		
		if(this.validateForm()) 
		{
			var form_obj = new MM_Form('mm-form-container');
		    var values = form_obj.getFields();
		     
		    values.mm_action = "updateMember";
		      
		    // TEST ONLY
		    //form_obj.dump();
		    
		    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		    ajax.send(values, false, 'mmjs', "memberUpdateHandler"); 
		}
	},
	
	setCalcMethod: function(method){
		mmJQuery("#mm-calc-method").val(method);
		mmJQuery("#mm-custom-date").val("");
		mmJQuery("#mm-fixed").val("");
	},
	
	sendPasswordEmail: function(user_id){
	    var isOk = confirm("Are you sure you want to send a new password email to this member?");
	    if(isOk){
			var values = {};
		     
		    values.mm_action = "sendPasswordEmail";
		    values.user_id = user_id;
		    
		    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		    ajax.send(values, false, 'mmjs', "passwordUpdateHandler"); 
	    }
	},
	
	passwordUpdateHandler: function(data){
		if(data.type=='error'){
			alert(data.message);
		}
		else{
			alert("Password successfully emailed to member");
		}
	},
	
	sync: function(id, orderId)
	{
		this.id = id;
		
	    var form_obj = new MM_Form('mm-form-container');
	    var values = form_obj.getFields();
	    values.mm_id = id;
		values.order_id = orderId;
		values.mm_action = "syncLimeLight";
	    
	    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
	    ajax.send(values, false, 'mmjs', "memberUpdateHandler"); 
	},
	
	memberUpdateHandler: function(data)
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
	  },
	
	validateForm: function()
	{
		
		if(mmJQuery('#mm-username').val() == "") {
			alert("Username is required");
			return false;
		}
 
		if(mmJQuery('#mm-email').val() == "") {
			alert("Email is required");
			return false;
		}
	   
		if(!this.validateEmail(mmJQuery('#mm-email').val())) 
		{
			alert("Please enter a valid email address");
			return false;
		}
 
		if(mmJQuery('#mm-phone').val() != "" && !this.validatePhone(mmJQuery('#mm-phone').val())) {
			alert("Please enter a valid phone number");
			return false;
		}
		
		if(mmJQuery('#mm-new-password').val() != "") {
			if(mmJQuery('#mm-new-password').val() != mmJQuery('#mm-confirm-password').val()) {
				alert("The new and confirm passwords don't match");
				return false;
			}
		}

		return true;
	}
});

var mmjs = new MM_MemberDetailsViewJS("MM_MemberDetailsView", "Member");