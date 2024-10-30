/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_DeliveryScheduleJs = MM_Core.extend({
	  
	/*
	 * Dialogs
	 */
	verifyCopy: function(){
		var areYouSure = confirm("Are you sure you want to copy the schedule?");
		if(areYouSure){
			return true;
		}
		return false;
	},
	
	createImportDiv: function(id)
	{	
		mmJQuery("<div id=\""+id+"\"></div>").hide().appendTo("body").fadeIn();
	},
	
	saveAccessRight:function(){
		var post = mmJQuery("#mm-gar-post").val();
		var page = mmJQuery("#mm-gar-page").val();
		var day = mmJQuery("#mm-gar-day").val();
		
		if(day=='' || day<0){
			alert("Invalid day specified.");
			return false;
		}
		
		var values =  {};
		values.post_id = 0;
		if(this.isTypePost()){
			values.post_id = post;
		}
		else{
			values.post_id = page;
		}
		values.day = day;
		values.access_id = mmJQuery("#mm_access_id").val();
		values.access_type = mmJQuery("#mm_access_type").val();

	    values.mm_action = "addAccessRights";
	    
	    var module = this.module;
	    var method = "performAction";
	    var action = 'module-handle';
	      
	    var ajax = new MM_Ajax(false, module, action, method);
	    ajax.send(values, false, 'mmjs','saveAccessRightsCallback');
	},
	
	saveAccessRightsCallback: function(data){
		var dialogId ="mm-dsm-container";
		mmdialog_js.close(dialogId);

		if(data.type!='error'){
			mmJQuery("#mm_dsm_form_tag").submit();
		}
		alert(data.message);
	},
	
	isTypePost: function(){
		if(mmJQuery("#mm-gar-page-type-post").is(":checked")){
			return true;
		}
		return false;
	},
	
	isTypePage: function(){
		if(mmJQuery("#mm-gar-page-type-post").is(":checked")){
			return false;
		}
		return true;
	},
	
	onTypeChange: function(){
		if(!this.isTypePost()){
			mmJQuery("#mm-gar-post").attr('disabled','disabled');
			mmJQuery("#mm-gar-page").attr('disabled','');
		}
		else{
			mmJQuery("#mm-gar-post").attr('disabled','');
			mmJQuery("#mm-gar-page").attr('disabled','disabled');
		}
	},
	
	saveManualAccessRight: function(accessId, accessType, postId, day, cellId, accessTypeName, pageName){
		var areYouSure = confirm("Are you sure you want to grant '"+accessTypeName+"' members access to '"+pageName+"' on day "+day+"");
		if(areYouSure){
			var values =  {};
			values.day = day;
			values.access_id = accessId;
			values.access_type = accessType;
			values.post_id = postId;
			values.cell_id = cellId;
			values.mm_action = "addAccessRights";
		    
		    var module = this.module;
		    var method = "performAction";
		    var action = 'module-handle';
		      
		    var ajax = new MM_Ajax(false, module, action, method);
		    ajax.send(values, false, 'mmjs','manualUpdateCallback');
		}
		
	},
	
	manualUpdateCallback: function(data){
		if(data.type!='error'){
			/*
			 * <a style='cursor: pointer;' onclick="mmjs.updateAccessRightsDialog('<?php echo $id; ?>','<?php echo $currentType; ?>','<?php echo $pageId; ?>');"><img src='<?php echo MM_Utils::getImageUrl($image); ?>' /></a>
			 */
			mmJQuery("#"+data.message.cell_id).attr("onclick","");
			var link = "<a style='cursor: pointer;' onclick=\"mmjs.updateAccessRightsDialog('"+data.message.access_id+"','"+data.message.access_type+"','"+data.message.post_id+"');\"><img src='"+data.message.image+"' /></a>";
			mmJQuery("#"+data.message.cell_id).html(link);
		}
		else{
			alert(data.message);
		}
	},
	
	updateAccessRights: function(){
		var accessId = mmJQuery("#mm_access_id").val();
		var accessType = mmJQuery("#mm_access_type").val();
		var postId = mmJQuery("#mm_post_id").val();
		var day = mmJQuery("#mm_gar_day").val();
		
//		mm-gar-change
		//mm-gar-remove
		
		if(day<0 || day==''){
			alert("Day must be greater or equal to 0.");
			return false;
		}

		var values =  {};
		values.should_remove = 0;
		if(mmJQuery("#mm-gar-remove").is(":checked")){
			values.should_remove = 1;
		}
		
		values.day = day;
		values.access_id = accessId;
		values.access_type = accessType;
		values.post_id = postId;

	    values.mm_action = "updateAccessRights";
	    
	    var module = this.module;
	    var method = "performAction";
	    var action = 'module-handle';
	      
	    var ajax = new MM_Ajax(false, module, action, method);
	    ajax.send(values, false, 'mmjs','updateAccessRightsCallback');
	},
	
	updateAccessRightsCallback: function(data){
			var dialogId ="mm-dsm-container";
			mmdialog_js.close(dialogId);
			if(data.type!='error'){
				mmJQuery("#mm_dsm_form_tag").submit();
			}
	},
	
	updateAccessRightsDialog: function(accessId, accessType, postId, day){
		var dialogId ="mm-dsm-container";
		this.createImportDiv(dialogId);
		
		mmJQuery("#"+dialogId).dialog({autoOpen: false, buttons: {
			"Update Access Rights": function() { mmjs.updateAccessRights(); },
			"Cancel": function() { mmjs.closeDialog(); }}});
		var values =  {};
		values.access_id = accessId;
		values.access_type = accessType;
		values.post_id = postId;
		values.day = day;
		values.mm_action = "updateAccessRightsDialog";
		values.mm_module = 'delivery_schedule_manager';
		
		mmdialog_js.showDialog(dialogId, this.module, 440, 182, "Access Rights", values);
	},
	
	addAccessRights: function(type, typeName){
		
		var dialogId ="mm-dsm-container";
		this.createImportDiv(dialogId);
		
		mmJQuery("#"+dialogId).dialog({autoOpen: false, buttons: {
			"Add Access Rights": function() { mmjs.saveAccessRight(); },
			"Cancel": function() { mmjs.closeDialog(); }}});
		var values =  {};
		values.type = type;
		values.type_name = typeName;
		values.mm_action = "accessRightsDialog";
		values.mm_module = 'delivery_schedule_manager';
		
		mmdialog_js.showDialog(dialogId, this.module, 420, 292, "Access Rights", values);
	},
	
	
	/** 
	 * Delivery Schedule form functions
	 */
	expandRows: function(totalRows, totalColumns){
	   for(j=1; j<=totalRows; j++){
		   this.expandRow(j,totalColumns);
		   this.showCollapseImage(j);
	   }
	},
	
	collapseRows: function(totalRows, totalColumns){
		for(j=1; j<=totalRows; j++){
			this.collapseRow(j,totalColumns);
			this.showExpandImage(j);
		}
	},
	
	showCollapseImage: function(row){
		var image = mmJQuery("#mm-dsm-row"+row+"col0-image").attr("src");
		if(image !=undefined){
			image = image.replace("expand", "collapse");
			mmJQuery("#mm-dsm-row"+row+"col0-image").attr("src", image);
		}
	},
	
	showExpandImage: function(row){
		var image = mmJQuery("#mm-dsm-row"+row+"col0-image").attr("src");
		if(image !=undefined){
			image = image.replace("collapse", "expand");	
			mmJQuery("#mm-dsm-row"+row+"col0-image").attr("src", image);
		}
	},
	
	toggleRow: function(row, totalRows){
		if(mmJQuery("#mm-dsm-row"+row+"col1-expanded").is(":visible")){
			this.collapseRow(row,totalRows);
			this.showExpandImage(row);
		}
		else{
			this.expandRow(row,totalRows);
			this.showCollapseImage(row);
		}
	},
	
	clearCache: function(){
		mmJQuery("#mm-expanded-rows").val("");
		mmJQuery("#mm-expanded-rows-copy").val("");
	},
	
	resizeDaysColumn: function(row, col, type){
		var rowHeight = mmJQuery("#mm-dsm-row"+row+"col"+col+"-"+type).height();
		mmJQuery('#mm-dsm-row'+row+'col0').css('height',rowHeight);
	},
	
	cacheExpandedRow: function(row){
		var day = mmJQuery("#row-"+row).val();
		
		var cache = mmJQuery("#mm-expanded-rows").val();
		if(cache.length>0){
			mmJQuery("#mm-expanded-rows").val(cache+","+day);
			mmJQuery("#mm-expanded-rows-copy").val(cache+","+day);
		}
		else{
			mmJQuery("#mm-expanded-rows").val(day);
			mmJQuery("#mm-expanded-rows-copy").val(day);
		}
	},
	
	removeExpandedCacheRow: function(row){
		var day = mmJQuery("#row-"+row).val();
		var cache = mmJQuery("#mm-expanded-rows").val();
		var arr = cache.split(",");
		var newCache = "";
		for(i=0; i<arr.length; i++){
			if(arr[i]!=day && arr[i] != ""){
				newCache+=arr[i]+",";
			}
		}
		mmJQuery("#mm-expanded-rows").val(newCache);
		mmJQuery("#mm-expanded-rows-copy").val(newCache);
	},
	
	expandRow: function(row,totalColumns){
		this.cacheExpandedRow(row);
		for(i=1; i<=totalColumns+2; i++){
		  mmJQuery("#mm-dsm-row"+row+"col"+i+"-expanded").show();
		  mmJQuery("#mm-dsm-row"+row+"col"+i+"-collapsed").hide();
		}
		this.resizeDaysColumn(row, 1, 'expanded');
	},
	
	collapseRow: function(row,totalColumns){
		this.removeExpandedCacheRow(row);
		for(i=1; i<=totalColumns+2; i++){
		   mmJQuery("#mm-dsm-row"+row+"col"+i+"-expanded").hide();
		   mmJQuery("#mm-dsm-row"+row+"col"+i+"-collapsed").show();
		}
		this.resizeDaysColumn(row, 1, 'collapsed');
	},
	 
});

var expandTracker = new Array();
var mmjs = new MM_DeliveryScheduleJs("MM_DeliveryScheduleManagerView", "Delivery Schedule Manager");