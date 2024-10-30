/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_SSLSettingsViewJS = MM_Core.extend({
	confirmSSLChoice: function(force){
	    var form_obj = new MM_Form('mm-form-container');
	    var values = form_obj.getFields();
		values.mm_action = "confirm_ssl";
	    if(force !=undefined){
	    	values.forceSSL = 1;
	    }
	    
	    if(mmJQuery("#mm_use_ssl").is(":checked")){
	    	values.use_ssl = 1;
	    }
	    else{
	    	values.use_ssl = 0;
	    }
	    
	    if(mmJQuery("#mm_use_ssl_admin").length){
		    if(mmJQuery("#mm_use_ssl_admin").is(":checked")){
		    	values.use_ssl_admin = 1;
		    }
		    else{
		    	values.use_ssl_admin = 0;
		    }
	    }
	    else{
	    	values.use_ssl_admin = 0;
	    }
	    
	    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
	    ajax.send(values, false, 'mmjs', "confirmCallback"); 
	},
	
	confirmCallback: function(data){
		
		if(data.type == 'error'){
			var goAhead = confirm("We could not detect a valid SSL certificate on your system. Are you sure you want to force SSL to be used? ");
			if(goAhead){
				this.confirmSSLChoice(true);
			}
		}
		else{
			alert(data.message);
		}
	},
	
	showAdminSSLOption: function(checked)
	{
		if(mmJQuery("#mm_use_ssl_admin").length){
			this.hideAdminSSLOption();
			return true;
		}
		var checkedStr = "";
		if(checked != undefined){
			if(checked){
				checkedStr = "checked";
			}
		}
		mmJQuery("#mm-ssl-options").append("<input id=\"mm_use_ssl_admin\"  name=\"mm_use_ssl_admin\" type=\"checkbox\" "+checkedStr+" /> Use SSL on MemberMouse administration pages");
	},
	
	hideAdminSSLOption: function()
	{
		mmJQuery("#mm-ssl-options").html("");
	},
	 
});

var mmjs = new MM_SSLSettingsViewJS("MM_SSLView", "SSL Settings");