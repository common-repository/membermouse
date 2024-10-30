/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_UninstallJS = MM_Core.extend({
	uninstall: function()
	{
		var doUninstall1 = confirm("WARNING!!\n\nUninstalling MemberMouse with permanently delete all your MemberMouse data.\n\nAre you sure you want to continue?");
		
		if(doUninstall1) 
		{
			var doUninstall2 = confirm("WARNING!!\n\nPlease confirm again that you want to uninstall MemberMouse and permanently delete all your MemberMouse data.");
			
			if(doUninstall2) 
			{
				mmJQuery("#mm-progressbar-container").show();
		        values = {
		           mm_action: "uninstall"
		        };
		
		        var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		        ajax.send(values, false, 'mmjs', 'pluginDeactivated'); 
			}
		}
	},
	
	pluginDeactivated: function(data)
	{
		mmJQuery("#mm-progressbar-container").hide();
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

			  document.location.href = 'index.php';
		  }
	},
});

var mmjs = new MM_UninstallJS("MM_UninstallView", "");