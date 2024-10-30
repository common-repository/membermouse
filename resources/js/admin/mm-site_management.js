/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_SiteManagementViewJS = MM_SiteMgmtViewJS.extend({

	saveSite: function()
	{
		sitemgmt_js.save(undefined, 'saveSite', 'mmjs','dataUpdateHandler');
//		if(this.validateForm()){
//			var values = {};
//
//			var core = new MM_Core("MM_SiteMgmtView", "Site");
//			core.save(undefined, values); //module, action, objName, objReturnFunc
//		}
	},
	
	confirmDeploy: function(){
		var isOk = confirm("Are you sure you want to deploy this version to the selected installs?");
		if(isOk){
			
			return true;
		}
		return false;
	},
	
	selectAll: function(){
		if(mmJQuery("#checkall").is(":checked")){
			mmJQuery("body").find(':checkbox').attr('checked', "checked");
		}
		else{
			mmJQuery("body").find(':checkbox').attr('checked', "");
		}
	},
	
	updateDev: function(){
		if(mmJQuery("#mm-is-dev-chk").is(":checked")){
			mmJQuery("#mm-is-dev").val("1");
		}
		else{
			mmJQuery("#mm-is-dev").val("0");
		}
	},
	
	updateMM: function(){
		if(mmJQuery("#mm-is-mm-chk").is(":checked")){
			mmJQuery("#mm-is-mm").val("1");
		}
		else{
			mmJQuery("#mm-is-mm").val("0");
		}
	},
	
	refreshView: function(){
	    var values = {
	        sortBy: this.sortBy,
	        sortDir: this.sortDir,
	        crntPage: this.crntPage,
	        resultSize: this.resultSize,
	        mm_action: "refreshView"
	    };
	    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
	    ajax.send(values, false, 'mmjs','refreshViewCallback'); 
	},
	
	refreshViewCallback: function(data){

		  if(data.message != undefined && data.message.length > 0) {
			  mmJQuery("#mm-view-container").html(data.message);
		  }
		  else {
			  alert("No data received");
		  }
	},
	
	dataUpdateHandler: function(data)
	{
		var core = new MM_Core("MM_SiteMgmtView", "Site");

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

			  core.refreshView();
			  this.closeDialog();
		  }
	  },
});

var mmjs = new MM_SiteManagementViewJS("MM_SiteMgmtView", "Site");

