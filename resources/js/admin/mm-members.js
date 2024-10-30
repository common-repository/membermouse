/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_MembersViewJS = MM_Core.extend({
  
	createMember: function()
	{
		this.processForm();
		if(this.validateForm()) 
		{
			var form_obj = new MM_Form('mm-order-form-container');
		    var values = form_obj.getFields();
		    if(mmJQuery("#mm-order-cc-number").val().length>2){
		    	values.mm_onsite_billing= "1";
		    }
		    values.mm_action = "placeNewOrder";
		    
		    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		    ajax.send(values, false, 'mmjs', this.updateHandler); 
		}
	},
	
	downloadTemplate: function(){
		
	},
	

	createImportDiv: function(id)
	{	
		mmJQuery("<div id=\""+id+"\"></div>").hide().appendTo("body").fadeIn();
	},
	
	enableDatePicker: function(id){
		mmJQuery("#"+id).datepicker("enable");
		mmJQuery("#"+id).datepicker("open");
	},
	
	import: function(){
		var dialogId ="mm-import-container";
		this.createImportDiv(dialogId);
		
		mmJQuery("#"+dialogId).dialog({autoOpen: false, buttons: {
			"Import Members": function() { mmjs.importMembers(); },
			"Cancel": function() { mmjs.closeDialog(); }}});
		var values =  {};
		values.mm_action = "getImportForm";
		values.mm_module = 'import';
		
		mmdialog_js.showDialog(dialogId, this.module, 800, 632, "Import Members", values);
		mmdialog_js.disableButton(dialogId, 'Import Members');
	},
	
	findMembers: function(fromDate, toDate){
		var dialogId ="mm-import-container";
		mmdialog_js.disableButton(dialogId, 'Import Members');
		
		var values = {};
		values.from_date = mmJQuery("#mm-import-from-date").val();
		values.to_date = mmJQuery("#mm-import-to-date").val();
		values.campaign_id = mmJQuery("#mm-import-campaign").val();
		
		if(values.from_date.length<=0 || values.to_date.length<=0){
			alert("You must choose a start and end date.");
			return false;
		}
		if(values.campaign_id<=0){
			alert("You must choose a campaign.");
			return false;
		}
		mmJQuery("#mm-progressbar-container").show();
	    values.mm_action = "findMembers";
	    
	    var module = this.module;
	    var method = "performAction";
	    var action = 'module-handle';
	      
	    var ajax = new MM_Ajax(false, module, action, method);
	    ajax.send(values, false, 'mmjs','findMembersCallback');
	},
	
	checkAllMembers: function(){
		if(mmJQuery("#mm-import-select").is(":checked")){
			mmJQuery('#mm-members-import-list').find(':checkbox').attr('checked', 'checked');
		}
		else{
			mmJQuery('#mm-members-import-list').find(':checkbox').attr('checked', '');
		}
	},
	
	toggleCustomDate: function(){
		if(mmJQuery("#mm_use_purchase_date").is(":checked")){
			mmJQuery("#mm_use_custom_date").hide();
		}
		else{
			mmJQuery("#mm_use_custom_date").show();
		}
	},

	saveCsvImport: function(){
		//var href=mmJQuery("#mm-badge").attr("href");
		var src = mmJQuery("#mm-badge-hidden").html();
		if(src==undefined){
			alert("You must click 'Upload' before importing members.");
			return false;
		}
		if(src.length<=0){
			alert("You must click 'Upload' before importing members.");
			return false;
		}
		
		var values = {};
		mmJQuery("#mm-progressbar-container").show();
	    values.mm_action = "getCSVMembers";
	    values.mm_firstrow_header = (mmJQuery("#mm-first-row-header").is(":checked"))?"1":"0";
		values.member_type = mmJQuery("#mm-member-type").val();
		values.csv_file = src;
		values.mm_delim = mmJQuery("#mm-delim").val();
	    
	    var module = this.module;
	    var method = "performAction";
	    var action = 'module-handle';
	      
	    var ajax = new MM_Ajax(false, module, action, method);
	    ajax.send(values, false, 'mmjs','getCSVMembersCallback');
	},
	
	getCSVMembersCallback:function(data){
		//alert(data.message);
		var dialogId ="mm-import-container";
		mmdialog_js.enableButton(dialogId, "Import Members");
		mmJQuery("#mm-import-results-csv").html(data.message);
		mmJQuery("#mm-progressbar-container").hide();
	},
	
	findMembersCallback: function(data){
		mmJQuery("#mm-import-results").html(data.message);
		mmJQuery("#mm-progressbar-container").hide();
	},
	
	getMemberDetails: function(){
		mmJQuery("#mm-progressbar-container").show();
		mmJQuery("#mm-members-find-results").hide();
		
		var values = {};
		values.from_date = mmJQuery("#mm-import-from-date").val();
		values.to_date = mmJQuery("#mm-import-to-date").val();
	    values.order_ids = mmJQuery("#mm-order-ids").html();
		values.campaign_id = mmJQuery("#mm-import-campaign").val();
	    
	    values.mm_action = "getImportMemberDetails";
	    
	    var module = this.module;
	    var method = "performAction";
	    var action = 'module-handle';
	      
	    var ajax = new MM_Ajax(false, module, action, method);
	    ajax.send(values, false, 'mmjs','getMemberDetailsCallback');
	},
	
	getMemberDetailsCallback: function(data){
		var dialogId ="mm-import-container";
		mmdialog_js.enableButton(dialogId, "Import Members");
		mmJQuery("#mm-import-results").html(data.message);
		mmJQuery("#mm-progressbar-container").hide();
	},
	
	importMembers: function(){
		//importMembers
		mmJQuery("#mm-progressbar-container").show();
		mmJQuery("#mm-import-wrap").hide();
		
		var values = {};
	    values.mm_action = "importMembers";
	    
	    values.custom_date = "";
	    values.use_purchase_date = "0";
	    if(mmJQuery("#mm_use_purchase_date").is(":checked")){
	    	values.use_purchase_date = "1";
	    }
	    else{
	    	values.custom_date = mmJQuery("#mm-custom-date").val();
	    } 
	    
	    values.send_notifications = "0";
	    if(mmJQuery("#mm_send_instant_notifications").is(":checked")){
		    values.send_notifications = "1";
	    }
	    
	    values.send_welcome_email = "0";
	    if(mmJQuery("#mm_send_welcome_emails").is(":checked")){
		    values.send_welcome_email = "1";
	    }
	   
	    
		var order_ids = "";
	    mmJQuery("input[name=order_ids\\[\\]]:checked").each(function()
	    {
	    	order_ids += mmJQuery(this).val()+",";
	    });
		values.order_ids = order_ids;
	
	    var module = this.module;
	    var method = "performAction";
	    var action = 'module-handle';
	      
	    var ajax = new MM_Ajax(false, module, action, method);
	    ajax.send(values, false, 'mmjs','getImportMembersCallback');
	},
	
	getImportMembersCallback: function(data){
		//mmJQuery("#mm-import-results").html(data.message);
		mmJQuery("#mm-progressbar-container").hide();
		alert(data.message);
		document.location.reload();
	},
	
	updateMemberType: function()
	{
		if(!mmJQuery("#mm-onsite-billing").length){
			mmJQuery("#mm-order-form-container").append("<input type='hidden' id='mm-onsite-billing' value='0' />");
		}
		
	    var form_obj = new MM_Form('mm-order-form-container');
	    var values = form_obj.getFields();
		values.mm_action = "getMemberTypeInfo";
	    
	    var ajax = new MM_Ajax(false, this.module, this.action, this.method);
	    ajax.send(values, false, 'mmjs', "memberTypeSelectionHandler"); 
	},
	
	memberTypeSelectionHandler: function(data)
	{
		  if(data.type != "error") {
			  mmJQuery("#mm-member-type-is-free").attr('value', data.message.is_free);
			  mmJQuery("#mm-order-billing-country").find("option").remove().end().append(data.message.country_list);
			  mmJQuery("#mm-order-shipping-country").find("option").remove().end().append(data.message.country_list);
			  mmJQuery("#mm-order-shipping-method").find("option").remove().end().append(data.message.shipping_list);
			  mmJQuery("#mm-order-payment-method").find("option").remove().end().append(data.message.payments_list);
		  }
		  else {
			  mmJQuery("#mm-member-type-is-free").attr('value', 'yes');
			  alert("No data received");
		  }
		  
		  this.processForm(data.message.is_onsite_billing);
	},
	
	processForm: function(onsiteBilling)
	{
		if(onsiteBilling ==undefined){
			onsiteBilling = "0";
		}
		
		mmJQuery("#mm-onsite-billing").val(onsiteBilling);
		
		if(mmJQuery("#mm-member-type-is-free").val() == "yes" || onsiteBilling=="0") {
			mmJQuery("#mm-order-paid-membership-form").hide(); 
		}
		else 
		{
			mmJQuery("#mm-order-paid-membership-form").show(); 
			
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
		}
	},
	
	validateForm: function()
	{
		var canCheckBilling = true;
		if(mmJQuery("#mm-onsite-billing").length){
			if(mmJQuery("#mm-onsite-billing").val() == "0"){
				canCheckBilling = false;
			}
		}
		
		if(mmJQuery('#mm-order-first-name').val() == "") {
			alert("First name is required");
			return false;
		}  
		
		if(mmJQuery('#mm-order-last-name').val() == "") {
			alert("Last name is required");
			return false;
		}  
	
		if(mmJQuery('#mm-order-username').val() == "") {
			alert("Username is required");
			return false;
		} 
		
		if(mmJQuery('#mm-order-password').val() == "") {
			alert("Password is required");
			return false;
		} 
 
		if(mmJQuery('#mm-order-email').val() == "") {
			alert("Email is required");
			return false;
		}
	   
		if(!this.validateEmail(mmJQuery('#mm-order-email').val())) 
		{
			alert("Please enter a valid email address");
			return false;
		}
		
		if(mmJQuery("#mm-member-type-is-free").val() == "no" && canCheckBilling) 
		{
			if(mmJQuery('#mm-order-billing-address').val() == "") {
				alert("Billing address is required");
				return false;
			}  
			
			if(mmJQuery('#mm-order-billing-city').val() == "") {
				alert("Billing city is required");
				return false;
			}
			
			if(mmJQuery('#mm-order-billing-state').val() == "") {
				alert("Billing state is required");
				return false;
			}
			
			if(mmJQuery('#mm-order-billing-zip').val() == "") {
				alert("Billing zip code is required");
				return false;
			}
			
			if(mmJQuery('#mm-order-phone').val() == "") {
				alert("Phone number is required");
				return false;
			}
			
			if(mmJQuery('#mm-order-cc-number').val() == "") {
				alert("Credit card number is required");
				return false;
			}
			
			if(mmJQuery('#mm-order-cc-security-code').val() == "") {
				alert("Credit card security code is required");
				return false;
			}
			
			if(mmJQuery('#mm-order-shipping-same-as-billing:checked').val() == null) 
			{	
				if(mmJQuery('#mm-order-shipping-address').val() == "") {
					alert("Shipping address is required");
					return false;
				}  
				
				if(mmJQuery('#mm-order-shipping-city').val() == "") {
					alert("Shipping city is required");
					return false;
				}
				
				if(mmJQuery('#mm-order-shipping-state').val() == "") {
					alert("Shipping state is required");
					return false;
				}
				
				if(mmJQuery('#mm-order-shipping-zip').val() == "") {
					alert("Shipping zip code is required");
					return false;
				}
			}
		}

		return true;
	},
	  
	  resetForm: function()
	  {
		  var values = {
				  mm_action: "resetForm"
		  };
	  
		  var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		  ajax.send(values, false, "mmjs", "resetFormHandler");
	  },
  
	  resetFormHandler: function(data)
	  {
		  if(data) {
			  mmJQuery("#mm-form-container").html(data);
		  }
	  },
  
	  sort: function(columnName) 
	  {
		  var newSortDir = "asc";
		  
		  if(columnName == this.sortBy)
		  {
			  if(this.sortDir=="asc") {
				  newSortDir = "desc";
			  }
		  }
		  
		  this.sortBy = columnName;
		  this.sortDir = newSortDir;
		  
		  this.search();
	  },
	  
	  dgPreviousPage: function(crntPage)
	  {	
		  if(parseInt(crntPage) != 0) {
			  this.crntPage = parseInt(crntPage) - 1;
			  this.search();
		  }
	  },
	  
	  dgNextPage: function(crntPage, totalPages)
	  {
		  if(crntPage != (parseInt(totalPages) - 1)) {
			  this.crntPage = parseInt(crntPage) + 1;
			  this.search();
		  }
	  },
	  
	  dgSetResultSize: function()
	  {
		  if(mmJQuery("#mm-datagrid-results-per-page").val() != undefined)
		  {
			  this.crntPage = 0;
			  this.resultSize = mmJQuery("#mm-datagrid-results-per-page").val();
			  this.search();
		  }
	  },
	  
	  changeCustomField: function(field){
		var customField = mmJQuery("#"+field).val();
		if(customField==''){
			mmJQuery("#"+field+"-value").hide();
		}
		else{
			mmJQuery("#"+field+"-value").show();
		}
	  },
  
	  search: function(crntPage) 
	  {
		  var form_obj = new MM_Form('mm-form-container');
		  var values = form_obj.getFields();
		  
		  if(crntPage != undefined) {
			  this.crntPage = crntPage;
		  }
      
		  values.sortBy = this.sortBy;
		  values.sortDir = this.sortDir;
		  values.crntPage = this.crntPage;
		  values.resultSize = this.resultSize;
		  values.mm_action = "search";
	  
		  // TEST ONLY
		  //form_obj.dump();
      
		  var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		  ajax.send(values, false, "mmjs", "resetGridHandler"); 
	  },
	  
	  csvExport:function(crntPage){
		  var form_obj = new MM_Form('mm-form-container');
		  var values = form_obj.getFields();
		  
		  if(crntPage != undefined) {
			  this.crntPage = crntPage;
		  }
      
		  values.sortBy = this.sortBy;
		  values.sortDir = this.sortDir;
		  values.crntPage = this.crntPage;
		  values.resultSize = this.resultSize;
		  values.csv = 1;
		  values.mm_action = "search";
	  
		  // TEST ONLY
//		  form_obj.dump();
      
		  var ajax = new MM_Ajax(false, this.module, this.action, this.method);
		  ajax.send(values, false, "mmjs", "csvCallback"); 
	  },
  
	  csvCallback: function(data){
	    mmJQuery("#mm_members_csv").append('<form id="mm_exportform" method="post" target="_blank"><input type="hidden" id="mm_exportdata" name="exportdata" /></form>');
	    mmJQuery("#mm_exportform").submit().remove();
	    
	    this.search();
	    return true; 
	  },
	  
	  resetGridHandler: function(data)
	  {
		  if(data) {
			  mmJQuery("#mm-grid-container").html(data);
		  }
	  }
});

var mmjs = new MM_MembersViewJS("MM_MembersView", "Member");