/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_SiteMgmtViewJS = MM_Core.extend({
  
	  create: function(memberId)
	  {	
		var params = {};
		removalQueue = new Array();
		
		if(memberId != undefined){
			params.memberId = memberId;
		}
        params.mm_module = "sitemgmt";
		mmdialog_js.showDialog("mm-site-dialog", this.module, "500", "450", "Create Site", params);
	  },
	  
	  edit: function(memberId, siteId, name, location, llUrl, llUsername, llPassword, llCampaignId)
	  {
		    removalQueue = new Array();
			var params = {
				memberId: memberId,
				siteId: siteId,
				name: name,
				location: location,
				llUrl: llUrl,
				llUsername: llUsername,
				llPassword: llPassword,
				llCampaignId: llCampaignId,
	            mm_module: "sitemgmt"
	        };
		  	
		  mmdialog_js.showDialog("mm-site-dialog", this.module, "500", "450", "Edit Site", params);
	  },
	  
	 isUsingLL:function(){

		  if(mmJQuery("#mm-site-use-ll").is(":checked")){
			  return true;
		  }
		  return false;
	  },
	  
  toggleLLInfo: function(){
	  if(this.isUsingLL()){
		  mmJQuery("#mm-ll-info").show();
		  mmJQuery("#mm-ll-info-title").show();
		  mmJQuery("#mm-ll-campaign-container").show();	
	  }
	  else{
		  mmJQuery("#mm-ll-info").hide();
		  mmJQuery("#mm-ll-info-title").hide();
		  mmJQuery("#mm-ll-campaign-container").hide();
	  }
  },
	  
	save: function(module, action, objName, objReturnFunc)
	{
		this.processForm();  
		  if(this.validateForm() == true) {
			  this.removeCommit();
			  
		      var form_obj = new MM_Form('mm-form-container');
		      var values = form_obj.getFields();
		      
		      values.mm_action = (action==undefined)?"save":action;
		      values.mm_module = (module==undefined)?"sitemgmt":module;
		      
		      // TEST ONLY
		      //form_obj.dump();
		      var callingObjectName = 'sitemgmt_js';
		      if(objName != undefined){
		    	callingObjectName = objName;  
		      }
		      var objReturnFuncName = 'dataUpdateHandler';
		      if(objReturnFunc != undefined){
		    	  objReturnFuncName = objReturnFunc;  
		      }

				var selectedCampaigns = "";
			    mmJQuery("input[name=mm-ll-campaign-id\[\]]:checked").each(function()
			    {
			    	selectedCampaigns += mmJQuery(this).val()+",";
			    });
			  values.mm_campaign_ids = selectedCampaigns;
		      	
		      var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		      ajax.send(values, false, callingObjectName, objReturnFuncName); 
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
			  mmdialog_js.close();
			  
			  if(data.message != undefined && data.message.length > 0) {
				  alert(data.message);
			  }

			  this.refreshView();
		  }
	  },
	  
	  refreshView: function(data)
	  {
	    var values = {
	    	mm_module: "sitemgmt",
	        mm_action: "refreshView"
	    };
	    
	    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
	    ajax.send(values, false, 'sitemgmt_js','refreshViewCallback'); 
	  },

	removeCampaigns: function(id, module, action, objName, objReturnFunc){
		  removalQueue.push(id);
		  mmJQuery("#image_"+id).hide();
		  mmJQuery("input[name=mm-ll-campaign-id\[\]]:checked").each(function()
		    {
		    	if(mmJQuery(this).val() == id){
		    		mmJQuery(this).attr('disabled','');
		    		mmJQuery(this).removeAttr('checked');
		    	}
		    });
	},
	removeCommit: function(){
		var siteId = mmJQuery("#mm-site-id").val();
		if(siteId>0){
			if(removalQueue.length>0){
			   var removalIds = "";
			   for(i=0; i<removalQueue.length; i++){   
				  if(removalQueue[i]>0){
					  removalIds+=removalQueue[i]+",";
				  }
			   }
			   if(removalIds.length>0){
				  var values = {};
			      values.mm_action = "removeCampaign";
			      values.mm_module = "sitemgmt";
			      values.mm_member_id = mmJQuery("#mm-member-id").val();
				  values.mm_campaign_ids = removalIds;
				  values.mm_site_id = siteId;
				  
			      var ajax = new MM_Ajax(false, this.module, this.action, this.method);
			      ajax.send(values, false, 'sitemgmt_js', 'removalCallback');
			   }
			}
		}
	},
	
	removalCallback: function(data){
		if(data.type=='error'){
			mmdialog_js.displayMessage(data.message);
		}
	},
	
	_removeCampaigns: function(id, module, action, objName, objReturnFunc){
      var values = {};
      values.mm_action = (action==undefined)?"removeCampaign":action;
      values.mm_module = (module==undefined)?"sitemgmt":module;
      
      var callingObjectName = 'sitemgmt_js';
      if(objName != undefined){
    	callingObjectName = objName;  
      }
      var objReturnFuncName = 'campaignRemovalCallback';
      if(objReturnFunc != undefined){
    	  objReturnFuncName = objReturnFunc;  
      }

      values.mm_member_id = mmJQuery("#mm-member-id").val();
	  values.mm_campaign_id = id;
	  values.mm_site_id = mmJQuery("#mm-site-id").val();
      
      var ajax = new MM_Ajax(false, this.module, this.action, this.method);
      ajax.send(values, false, callingObjectName, objReturnFuncName); 
	},
	
	_campaignRemovalCallback: function(data){
		if(data.type == 'error'){
			mmdialog_js.displayMessage(data.message);
		}
		else{
			var values = {};
			values.mm_action = "refreshCampaignList";
			values.mm_member_id = mmJQuery("#mm-member-id").val();
			values.mm_site_id = mmJQuery("#mm-site-id").val();
			
		    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		    ajax.send(values, false, 'sitemgmt_js', "campaignListCallback");
		}
	},
	
	campaignListCallback: function(data){
		mmJQuery("#mm-campaign-list").html(data.message);
	},
	  
	verifyLimeLight: function()
	{
		if(this.validateLLForm())
		{
			mmJQuery("#mm-verify-ll-button").hide();
			mmJQuery("#mm-progressbar-container").show();
			
			var form_obj = new MM_Form('mm-form-container');
		    var values = form_obj.getFields();
			values.mm_action = "verifyLL";
		    
		    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		    ajax.send(values, false, 'sitemgmt_js', "verifyLLHandler"); 
		}
	},
	
	verifyLLHandler: function(data)
	{
		if(data.message != undefined && data.message.length > 0) 
		{
			if(data.type == "error") {
				mmJQuery("#mm-verify-ll-button").show();
				mmJQuery("#mm-progressbar-container").hide();
				alert(data.message);
			}
			else {
				mmJQuery("#mm-ll-campaign-container").html(data.message);
				
				mmJQuery("#mm-ll-url").attr("disabled", true);
				mmJQuery("#mm-ll-api-key").attr("disabled", true);
				mmJQuery("#mm-ll-api-password").attr("disabled", true);
				
				mmJQuery("#mm-ll-verified").attr("value", "1");
			}
		}
		else {
		   mmJQuery("#mm-verify-ll-button").show();
		   mmJQuery("#mm-progressbar-container").hide();
		   alert("No data received");
		}
	},
	  
	processForm: function()
	{
		siteUrl = mmJQuery('#mm-site-url').val();
		index = siteUrl.lastIndexOf("/");
		
		if(index != -1 && siteUrl.length-1 == index){
			mmJQuery('#mm-site-url').attr("value", $siteUrl.substring(0, siteUrl.length-1));
		}
	},
	   
	validateForm: function()
	  {
		if(mmJQuery('#mm-site-name').val() == "") {
			alert("Site name is required");
			return false;
		}
		
		if(mmJQuery('#mm-site-url').val() == "") {
			alert("Site URL is required");
			return false;
		}
		
		if(this.validateUrl(mmJQuery('#mm-site-url').val()) == false) {
			alert("Please enter a valid site URL");
			return false;
		}

		if(this.isUsingLL()){
			
			if(this.validateLLForm()) 
			{		
				if(mmJQuery('#mm-ll-verified').val() != "1") {
					alert("Please verify your Lime Light credentials and select a campaign");
					return false;
				}
				var selectedCampaigns = 0;
			    mmJQuery("input[name=mm-ll-campaign-id\[\]]:checked").each(function()
			    {
			    	selectedCampaigns++;
			    });
				
				if(selectedCampaigns<=0) {
					alert("At least one Lime Light API Campaign ID is required");
					return false;
				}
			}
		}
		
		return true;
	  },
	  
	 validateLLForm: function() 
	 {
		 if(mmJQuery('#mm-ll-url').val() == "") {
				alert("Lime Light URL is required");
				return false;
			}
			
			if(this.validateUrl(mmJQuery('#mm-ll-url').val()) == false) {
				alert("Please enter a valid Lime Light URL");
				return false;
			}
			
			if(mmJQuery('#mm-ll-api-key').val() == "") {
				alert("Lime Light API key is required");
				return false;
			}
			
			if(mmJQuery('#mm-ll-api-password').val() == "") {
				alert("Lime Light API password is required");
				return false;
			}
			
			return true;
	 }
});
var removalQueue = new Array();
var sitemgmt_js = new MM_SiteMgmtViewJS("MM_SiteMgmtView", "Site");