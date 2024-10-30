/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_CorePagesViewJS = MM_Core.extend({
  
  /**** UI Arrangement & Dialog *****/
  updateElements: function(should_hide, ref_id)
  {
    if(should_hide)
    {
       mmJQuery("#core_page_type_id").attr("disabled","");  
       mmJQuery("#core_page_type_id").val("");
       
       if(ref_id!=undefined)
            mmJQuery("#core_page_type_id option[value='"+ref_id+"']").remove();
       
       mmJQuery("#default_core_page").hide();
       mmJQuery("#default_core_page_icon").hide();
       mmJQuery("#mm_required_tags").html("");
    }
  },
  
  checkAccessRights: function()
  {
    if(mmJQuery("#core_page_type_id").val()>0)
    {
        mmJQuery("#mm_access_rights_meta").hide();
    }
    else
        mmJQuery("#mm_access_rights_meta").show();
  },
  
  showMessage: function(str)
  {
    mmJQuery("#message").html(str);
  },
  
  /**** END UI Arrangement *****/
  
  /***** Callbacks *****/
  corePageSelectionCallback: function(data)
  {
  	if(data.type=='error')
  	{
  		alert(data.message);
  	}
  	else
  	{
      mmJQuery("#subtypes").find("tr").remove().end();
      
      var requiredTags=  "";
      
      if(data.message.requiredTags != undefined)
      {
    	  requiredTags = data.message.requiredTags;
      }
      mmJQuery("#mm_required_tags").html(requiredTags);
      
      if(data.message.content != undefined){
    	  mmJQuery("#subtypes").append(data.message.content);
      }
      else
      {
    	  mmJQuery("#subtypes").append(data.message);
      }
      this.checkAccessRights();
    }
  },
  
  updateCorePageCallback: function(data)
  {
    if(data.type=='error')
    {
        alert(data.message);   
    }
    else
    {
       mmJQuery("#mm_access_rights_meta").show();
       this.showMessage("You have successfully re-assigned the core page.");
       mmJQuery("#core_page_type_id").attr("disabled","");
       this.updateElements(true); 
       this.closeDialog();
    }
  },
  /***** END Callbacks *****/

  
  /** DATABASE FUNCTIONS **/
  
  updateCorePage: function()
  {
      var form_obj = new MM_Form('mm-adminpreview');
      var values = form_obj.getFields();
      values.post_ID = mmJQuery("#post_ID").val();
      values.new_page_id = mmJQuery("#new_page_id").val();
      
      var module = "MM_CorePagesView";
      var method = "changeDefaultPage";
      var action = 'module-handle';
      
      var ajax = new MM_Ajax(false, module, action, method);
      ajax.send(values, false, 'corepages_js','updateCorePageCallback');
  },
  
  getReferences: function(isConfFree)
  {
    var isFree = '';
	if(isConfFree!=undefined)
	{
		isFree= isConfFree;
	}
	
    var do_corepage = true;
    if(!mmJQuery("#mm_access_rights_meta").is(":hidden") && mmJQuery("#has_access_rigths").length && mmJQuery("#core_page_type_id").val()!='')
    {
        do_corepage = confirm("If you save this page as a Core Page you will remove any access rights associated with it.  Continue?");
    }
    if(mmJQuery("#core_page_type_id").val()=="")
    {
        mmJQuery("#subtypes").find("tr").remove().end();
        do_corepage = false;
    }
    
    if(mmJQuery("#core_page_type_id").val()>0){
	  	  if(mmJQuery("#membermouse_post_content_delivery").length){
	  		  mmJQuery("#membermouse_post_content_delivery").hide();
	  	  }
	      mmJQuery("#mm-content-delivery-table").hide();
    }
    else{
    	  if(mmJQuery("#membermouse_post_content_delivery").length){
    		  mmJQuery("#membermouse_post_content_delivery").show();
    	  }
	      mmJQuery("#mm-content-delivery-table").show();
    }
    
    if(do_corepage)
    {
        if(isFree == '' && mmJQuery("#is_free").length)
        {
        	isFree = mmJQuery("#is_free").val();
        }
        
        var module = "MM_CorePagesView";
        var action = 'module-handle';
        var method = "getOptionsByCorePageType";
        var values = {
            post_ID:mmJQuery("#post_ID").val(),
            core_page_type_id:mmJQuery("#core_page_type_id").val(),
            is_free: isFree,
        };  
        var ajax = new MM_Ajax(false, module, action, method);
        ajax.send(values, false, 'corepages_js','corePageSelectionCallback'); 
    }
    else
    {
        this.checkAccessRights();
        mmJQuery("#core_page_type_id").val('');    
    }
  },
});

var corepages_js = new MM_CorePagesViewJS("MM_CorePagesView", "Core Pages");

