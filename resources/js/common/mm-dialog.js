/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_DialogJS = Class.extend({
	init: function() 
	  {	
		  this.method = "performAction";
		  this.action = "module-handle";
		  
	      var size = this.getWindowSize();
		  this.dialogWidth = 650;
		  this.dialogHeight = size.height - 130;
	  },

	  getWindowSize: function(){
	    var size = {
	        height:0,
	        width: 0,
	    };
	    
	    if( typeof( window.innerWidth ) == 'number' ) {
	        //Non-IE
	        size.width = window.innerWidth;
	        size.height = window.innerHeight;
	    } 
	    else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
	        //IE 6+ in 'standards compliant mode'
	        size.width = document.documentElement.clientWidth;
	        size.height = document.documentElement.clientHeight;
	    }
	    else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
	        size.width = document.body.clientWidth;
	        size.height = document.body.clientHeight;
	    }
	    return size;
	  },
	  
	showDialog: function(dialogId, module, width, height, title, params, methodReplace, objName)
	{
		this.dialogId = dialogId;
		
		if(title != undefined) {
			this.dialogTitle = title;
		}
		
		if(width == undefined || width == "") {
		  this.width = this.dialogWidth;
	    }
		else {
			this.width = width;
		}
		 
		if(height == undefined || height == "") {
		  this.height = this.dialogHeight;
		}
		else {
			this.height = height;
		}
		
		var values = {
            mm_action: "showDialog"
        };
		
		if(params != undefined && params != "") {
		  if(typeof params == "object") {
			  for(var key in params) {
				  try{
					  if(typeof params[key] == "string"){
						  eval("values."+key+"='"+params[key].replace(/(\')/g,"")+"'");
					  }
					  else
					  {
						  eval("values."+key+"='"+params[key]+"'");
					  }
				  }
				  catch(e)
				  {
					alert(e+" : "+key);  
				  }
			  }
		  } else {
			  values.id = params;
		  }
	    }
		
		var methodName = this.method;
		if(methodReplace != undefined && methodReplace != "") {
			methodName = methodReplace;
		}
		
		var obj = "mmdialog_js";
		if(objName!=undefined && objName!=""){
			obj = objName;
		}

        var ajax = new MM_Ajax(false, module, this.action, methodName);
        ajax.send(values, false, obj, "showDialogCallback"); 
	},
  
	// for use when a new dialog should pop up
	createDiv: function(id)
	{
		mmJQuery("<div id='"+id+"' style='font-size: 14px;'></div>").hide().appendTo("body").fadeIn();
	},
	
	displayMessage: function(message, width, height){
		this.width = 450;
		this.height = 140;
		if(width != undefined){
			this.width = width;
		}
		if(height != undefined){
			this.height = height;	
		}
		this.dialogId = 'mm-response';
		this.createDiv(this.dialogId);
		this.dialogError(message);
	},

	// For use in normal mmdialog_js instances
	dialogError: function(message){
		if(mmJQuery("#mm-progressbar-container").length){
				mmJQuery("#mm-progressbar-container").hide();
		}
		
		if(this.dialogTitle == undefined && this.dialogTitle!="") {
			this.dialogTitle = "Alert";
		}
		
		mmJQuery("#"+this.dialogId).dialog({width: this.width, height: this.height,  title: "Alert", buttons:{"OK": function() { mmdialog_js.close(this.dialogId) }}});
		mmJQuery("#"+this.dialogId).html(message);
		mmJQuery("#"+this.dialogId).dialog("open");
	},
	
	disableDialog: function(dialogId){
		if(dialogId == undefined) {
			mmJQuery("#"+this.dialogId).dialog("option", "disabled", true);
		}
		else {
			mmJQuery("#"+dialogId).dialog("option", "disabled", true);
		}
	},
	
	getDialogButton: function( dialogSelector, buttonName )
	{
	  var buttons = mmJQuery( dialogSelector + ' .ui-dialog-buttonpane button' );
	  for ( var i = 0; i < buttons.length; ++i )
	  {
	     var jButton = mmJQuery( buttons[i] );
	     if ( jButton.text() == buttonName )
	     {
	         return jButton;
	     }
	  }
	  return null;
	},
	
	disableButton: function(dialogId, buttonName){
		mmJQuery("#"+dialogId).parent().find("button").each(function() {
	        if( mmJQuery(this).text() == buttonName ) {
	        	mmJQuery(this).attr('style', 'filter:alpha(opacity=50);-moz-opacity:0.5;-khtml-opacity: 0.5;opacity: 0.5;');
	        	mmJQuery(this).attr('disabled', 'disabled');
	        }
	    });
	},
	
	enableButton: function(dialogId, buttonName){
		mmJQuery("#"+dialogId).parent().find("button").each(function() {
	        if( mmJQuery(this).text() == buttonName ) {
	        	mmJQuery(this).attr('style', 'filter:alpha(opacity=100);-moz-opacity:1;-khtml-opacity: 1;opacity: 1;');
	        	mmJQuery(this).removeAttr('disabled');
	        }
	    });
	},

	/**
	 * Caution:  This takes the first button that appears within the dialog.
	 * @param dialogId is the id of the dialog in question.
	 */
	findButton: function(dialogId){
		var button = null;
		mmJQuery("#"+dialogId).parent().find("button").each(function() {
				if(button == null && !mmJQuery(this).is(":disabled")){
					button= mmJQuery(this);
				}
	    });
		return button;
	},
	
	bindEnterKey: function(buttonObj, areaId){
		var contentArea = "#"+areaId;
		
		mmJQuery(contentArea).bind("keydown", function(event) {
		      // track enter key
			var shouldEnter = true;
			mmJQuery(contentArea).find("textarea").each(function() {
					if(mmJQuery(this).is(":focus")){
						shouldEnter = false;
					}
		    });
			if(!shouldEnter){
				return true;
			}
		      var keycode = (event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode));
		      if (keycode == 13) {
		    	  mmJQuery(buttonObj).click();
		         return false;
		      } else  {
		         return true;
		      }
		   });
	},
	
	showDialogCallback: function(data)
	{
		if(data.type != undefined && data.type == "error")
		  {
			  if(data.message.length > 0)
			  {  
				  this.dialogError(data.message);
				  return false;
			  }
			  if(data.message.url != undefined)
			  {  
				  document.location.href=data.message.url;
				  return false;
			  }
		  }
		  else if(data.message != undefined && data.message.length > 0)
	     {
			mmJQuery("#"+this.dialogId).dialog("option", "width", this.width);
			mmJQuery("#"+this.dialogId).dialog("option", "height", this.height);
			mmJQuery("#"+this.dialogId).dialog("option", "minWidth", this.width);
			mmJQuery("#"+this.dialogId).dialog("option", "minHeight", this.height);
			
			if(this.dialogTitle != undefined) {
				mmJQuery("#"+this.dialogId).dialog("option", "title", this.dialogTitle);
			}
			
			mmJQuery("#"+this.dialogId).dialog("option", "modal", true);
			
			mmJQuery("#"+this.dialogId).html(data.message);
			
			mmJQuery("#"+this.dialogId).dialog("open");

			this.bindEnterKey(this.findButton(this.dialogId),this.dialogId);
	     } 
		  else {
			  alert("No data received");
		  }
	},
	
	close: function(dialogId)
	{
		if(dialogId == undefined) {
			mmJQuery("#"+this.dialogId).dialog("close");
		}
		else {
			mmJQuery("#"+dialogId).dialog("close");
		}
	}
});

var mmdialog_js = new MM_DialogJS();