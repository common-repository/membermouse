/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_EmailAccountsViewJS = MM_Core.extend({
  
	setDefault: function(id)
	{
		var doSet = confirm("Are you sure you want to set this email account as the default?");
	    
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
 
	forceConfirm: function(id){

        var values = {
            id:id,
            mm_action: "forceConfirm"
        };
        var ajax = new MM_Ajax(false, this.module, this.action, this.method);
        ajax.send(values, false, 'mmjs', "forceConfirmCallback"); 
	},
	
	forceConfirmCallback: function(data){
		document.location.reload();
	},
	
	validateForm: function()
	{
		// display name 
		if(mmJQuery('#mm-display-name').val() == "") {
			alert("Display name is required");
			return false;
		}
	   
		// email
		if(mmJQuery('#mm-email').val() == "") {
			alert("Email is required");
			return false;
		}
	   
		if(!this.validateEmail(mmJQuery('#mm-email').val())) 
		{
			alert("Please enter a valid email address");
			return false;
		}
	   
		return true;
	}
});

var mmjs = new MM_EmailAccountsViewJS("MM_EmailAccountsView", "Employee Account");