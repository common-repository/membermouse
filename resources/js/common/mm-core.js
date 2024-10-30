/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_Core = Class.extend({
  
  init: function(moduleName, entityName) 
  {	
	  if(moduleName == undefined) {
		  alert("MM_Core.js: module name is required (i.e. MM_MemberTypesView, MM_AccessTagsView, etc.)");
	  }
	  
	  if(entityName == undefined) {
		  alert("MM_Core.js: entity name is required (i.e. Member Type, Access Tag, etc.)");
	  }
	  
	  this.module = moduleName;
	  this.entityName = entityName;
	  this.method = "performAction";
	  this.action = "module-handle";
	  this.updateHandler = "dataUpdateHandler";
	  this.mm_page = "mm_configure_site";
	  this.mm_module = "member_types";
	  this.badgeUrl = "";
  },
  
  shouldRedirectExternal: function(urlObj){
	  	var url = "";
	    if(urlObj.url!=undefined){
	  		url = urlObj.url;
	  	}
	    else{
	    	url = urlObj;
	    }
	    
	  	if(url.toLowerCase().indexOf("paypal")>=0 || url.toLowerCase().indexOf("clickbank")>=0){
			return confirm("You will be redirected to "+url);
		}
		return true;
  },
  
  createFormSubmit: function(params, submitButtonId){
	if(params!=null){
		var html  = "<form id='mm-paymentmethod' action='"+params.url+"' method='post'>";
		for(var eachvar in params){
			html+= "<input type='hidden' name='"+eachvar+"' value='"+params[eachvar]+"' />";
		}
		html+="</form>";
		//alert(html);
		mmJQuery("body").append(html);
		if(submitButtonId != undefined){
			if(mmJQuery("#"+submitButtonId).length){
				mmJQuery("#"+submitButtonId).submit();
			}
			else{
				alert("No button defined "+submitButtonId);
			}
		}
		else{
			if(mmJQuery("#mm-paymentmethod").length){
				mmJQuery("#mm-paymentmethod").submit();
			}
			else{
				alert("No button defined mm-paymentmethod");
			}
		}
		
	}
  },
  
  downloadFile: function(url){
	  document.location.href=url;
  },

  isValidURL: function(url){ 
	  return url.match(/^(ht|f)tps?:\/\/[a-z0-9-\.]+\.[a-z]{2,4}\/?([^\s<>\#%"\,\{\}\\|\\\^\[\]`]+)?$/);
  },
  
  ucfirst: function(str)
  {
      return str.charAt(0).toUpperCase() + str.slice(1);
  },
  
  getVar: function(value, defaultValue){
	if(value==undefined){
		return defaultValue;
	}
	return value;
  },
  
  setDataGridProps: function(sortBy, sortDir, crntPage, resultSize)
  {
	  this.sortBy = sortBy;
	  this.sortDir = sortDir;
	  this.crntPage = crntPage;
	  this.resultSize = resultSize;
  },
  
  getQuerystringParam: function(name)
  {
    name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
    
    var regexS = "[\\?&]"+name+"=([^&#]*)";
    var regex = new RegExp( regexS );
    var results = regex.exec( window.location.href );
    
    if(results == null) {
      return "";
  	} else {
      return decodeURIComponent(results[1].replace(/\+/g, " "));
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
	  this.refreshView();
  },
  
  dgPreviousPage: function(dgCrntPage)
  {
	  if(parseInt(dgCrntPage) != 0) {
		  this.crntPage = parseInt(dgCrntPage) - 1;
		  this.refreshView();
	  }
  },
  
  dgNextPage: function(dgCrntPage, dgTotalPages)
  {
	  if(dgCrntPage != (parseInt(dgTotalPages) - 1)) {
		  this.crntPage = parseInt(dgCrntPage) + 1;
		  this.refreshView();
	  }
  },
  
  dgSetResultSize: function()
  {
	  if(mmJQuery("#mm-datagrid-results-per-page").val() != undefined)
	  {
		  this.crntPage = 0;
		  this.resultSize = mmJQuery("#mm-datagrid-results-per-page").val();
		  this.refreshView();
	  }
  },
  
  refreshView: function()
  {
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
  
  refreshViewCallback: function(data)
  {
	  if(data.message != undefined && data.message.length > 0) {
		  mmJQuery("#mm-view-container").html(data.message);
	  }
	  else {
		  alert("No data received");
	  }
  },
  
  save: function(callback, params, callbackFunc) 
  {
	  this.processForm();
	  
	  if(this.validateForm() == true) {
	      var form_obj = new MM_Form('mm-form-container');
	      var values = form_obj.getFields();
	      
	      if (mmJQuery('#mm-badge')) 
	      {
		      values.badge_url = this.badgeUrl;
		      
		      if(this.badgeUrl.length <= 0) {
		    	  values.badge_url = mmJQuery("#mm-badge").attr("src");
		      }
		      
			  this.badgeUrl = "";
	      }
	      
	      values.mm_action = "save";
	      
    	  if(params != undefined && params != "") {
    		  if(typeof params == "object") {
    			  for(var key in params) {
    				  eval("values."+key+"='"+params[key]+"'");
    			  }
    		  }
    	   }
	      
	      // TEST ONLY
    	  //form_obj.dump();
    	  
    	  var callbackObject = "mmjs";
	      if(callback != undefined && callback!="")
	      {
	    	  callbackObject = callback;
	      }

	      var ajax = new MM_Ajax(false, this.module, this.action, this.method);
	      ajax.send(values, false, callbackObject, this.updateHandler); 
	  }
  },
  
  create: function(dialogId, width, height)
  {
	  mmdialog_js.showDialog(dialogId, this.module, width, height, "Create "+this.entityName);
  },
  
  edit: function(dialogId, id, width, height)
  {
	  mmdialog_js.showDialog(dialogId, this.module, width, height, "Edit "+this.entityName, id);
  },
  
  remove: function(id)
  { 
    var doRemove = confirm("Are you sure you want to remove this " + this.entityName.toLowerCase() + "?");
    
    if(doRemove)
    {
        var values = {
            id: id,
            mm_action: "remove"
        };
        
        var ajax = new MM_Ajax(false, this.module, this.action, this.method);
        ajax.send(values, false, 'mmjs', this.updateHandler); 
    }
  },
  
  displayVideo: function(title, embed_code, width, height)
  {	
	  var values = {
	        embed_code: embed_code,
            mm_action: "displayVideo"
	  };
	  
	  mmdialog_js.showDialog("mm-help-dialog", "MM_ContextualHelpView", parseInt(width) + 20, parseInt(height) + 50, title, values);
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
		  this.closeDialog();
	  }
  },

  closeDialog: function()
  {
	  mmdialog_js.close();
  },
  
  /** FORM-SPECIFIC FUNCTION **/
  processForm: function()
  {
	  // define in subclass
  },
  
  validateForm: function()
  {
	  // define in subclass
	  return true;
  },
  
  validatePhone: function(phone)
  {
	var regexs = new Array();
	regexs.push(/^(\+\d)*\s*(\(\d{3}\)\s*)*\d{3}(-{0,1}|\s{0,1})\d{2}(-{0,1}|\s{0,1})\d{2}$/); 
	regexs.push(/^\d{10}$/);
	regexs.push(/^(\d{3})*(\-|\s)*\d{3}(\-|\s)*\d{4}$/);
	
	for(i=0; i<regexs.length; i++)
	{
		if (phone.match(regexs[i])) {
			return true;
		} 
	}
	return false;  
  },

  validateCreditDate: function(year,month)
  {
	  var d = new Date();
	  var curr_date = d.getDate();
	  var curr_month = d.getMonth()+1; /// required to add 1 since their month index starts on 0
	  var curr_year = d.getFullYear();
	  
	  curr_year = curr_year.toString().substring(2);
	  
	  if(parseInt(curr_year)>parseInt(year))
	  {
		return false;  
	  }
	  else if(parseInt(curr_year)== parseInt(year))
	  {
		  if(parseInt(curr_month)>parseInt(month))
		  {
			  return false;
		  }
	  }
	  return true;
  },
  
  validatePassword: function(password)
  {
	  if(password.length<6)
	  {
		return false;  
	  }
	  return true;
  },
  
  validateEmail: function(email) 
  {
	  var apos = email.indexOf("@");
	  var dotpos = email.lastIndexOf(".");
	   
	  if(apos < 1 || dotpos - apos < 2)
	  {
			return false;
	  }
	  
	  return true;
  },
  
  validateUrl: function(s) {
		var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
		return regexp.test(s);
	},
  
  /** BADGE UPLOAD FUNCTIONS **/
  startUpload: function()
  {
      return true;
  },

  stopUpload: function(success, msg, filePath)
  {
      if (success == '1')
      {
         mmJQuery("#mm-badge-container").show();
         mmJQuery("#mm-file-upload-container").hide();
         if(mmJQuery("#mm-badge")[0]){
        	 var tag = mmJQuery("#mm-badge")[0].tagName;
        	 if(tag.toLowerCase() == "div"){
               mmJQuery("#mm-badge").attr("href", msg);
               var fileArr = msg.split('/');
               if(filePath != undefined){
            	   mmJQuery("#mm-badge-hidden").text(filePath);
               }
               mmJQuery("#mm-badge").text(fileArr.pop());
               
        	 }
         }
         else{
             mmJQuery("#mm-badge").attr("src", msg); 
         }
         
         this.badgeUrl = msg;
      }
      else 
      {
         mmJQuery("#mm-badge-container").hide();
         mmJQuery("#mm-file-upload-container").show();
         
         alert(msg);     
      }
      
      return true;   
  },
  
  clearBadge: function()
  {
	  mmJQuery("#fileToUpload").attr("value", "");
	  mmJQuery("#mm-badge-container").hide();
      mmJQuery("#mm-file-upload-container").show();
      mmJQuery("#mm-badge").attr("src", "");
      
      this.badgeUrl = "";
  }
});