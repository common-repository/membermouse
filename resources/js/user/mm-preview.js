/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

var MM_Preview = MM_Core.extend({
	adjustZIndex: function(){
		var highestIndex = 0;
		mmJQuery("div,span,table").each( function() {
			var currentZindex = parseInt(mmJQuery(this).css("zIndex"));
		    if(currentZindex > highestIndex) {
		    	highestIndex = currentZindex;
		    }
		});

		mmJQuery("#mm-adminpreview-open").css("zIndex", highestIndex+1);
	},
	
	openPreview: function()
	{
	  mmJQuery("#mm-adminpreview-open").show();
	  mmJQuery("#mm-adminpreview-closed").hide();
	  var values = {};
      values.mm_should_show = '1';
      values.mm_action = "togglePreviewBar";
      
      var module = "MM_PreviewView";
      var method = "performAction";
      var action = 'module-handle';
      
      var ajax = new MM_Ajax(false, module, action, method);
      ajax.send(values, false, 'mmPreviewJs','toggleView');
	},
	
	closePreview: function()
	{
	  mmJQuery("#mm-adminpreview-open").hide();
	  mmJQuery("#mm-adminpreview-closed").show();
	  var values = {};
      values.mm_should_show = '0';
      values.mm_action = "togglePreviewBar";
      
      var module = "MM_PreviewView";
      var method = "performAction";
      var action = 'module-handle';
      
      var ajax = new MM_Ajax(false, module, action, method);
      ajax.send(values, false, 'mmPreviewJs','toggleView');
	},
	
	savePreview:function()
	{
      var form_obj = new MM_Form('mm-adminpreview-open');
      var values = form_obj.getFields();
      values.member_type_id = mmJQuery("#mm-preview-member_type").val();
      values.mm_action = "savePreview";
      values.preview_access_tags = this.getAccessTagsFromField();
      values.mm_preview_days = mmJQuery("#mm-preview-days").val();
      
      var module = "MM_PreviewView";
      var method = "performAction";
      var action = 'module-handle';
	  mmJQuery("#mm-preview_btn").attr('disabled','disabled');
      
      var ajax = new MM_Ajax(false, module, action, method);
      ajax.send(values, false, 'mmPreviewJs','handleSave');
	},
	
	enableChangeButton: function()
	{
		mmJQuery("#mm-preview_btn").removeAttr('disabled');
	},
	
	changeMemberType: function()
	{
      var form_obj = new MM_Form('mm-adminpreview-open');
      var values = form_obj.getFields();
      values.member_type_id = mmJQuery("#mm-preview-member_type").val();
      values.mm_action = "previewChangeMemberType";
      
      var module = "MM_PreviewView";
      var method = "performAction";
      var action = 'module-handle';
      
      var ajax = new MM_Ajax(false, module, action, method);
      ajax.useLoader=false;
      ajax.send(values, false, 'mmPreviewJs','handleUpdate');
	},
	
	changeAccessTags: function()
	{
      var form_obj = new MM_Form('mm-adminpreview-open');
      var values = form_obj.getFields();
      values.member_type_id = mmJQuery("#mm-preview-member_type").val();
      values.mm_action = "previewChangAccessTags";
      values.preview_access_tags = this.getAccessTagsFromField();
      
      var module = "MM_PreviewView";
      var method = "performAction";
      var action = 'module-handle';
      
      var ajax = new MM_Ajax(false, module, action, method);
      ajax.useLoader=false;
      ajax.send(values, false, 'mmPreviewJs','handleUpdate');
	},
	
	getAccessTagsFromField: function()
	{
		var preview_access_tags = "";
		var count = 0;
	    mmJQuery("select[name=preview_access_tags\[\]] :selected").each(function()
	    {
	    	preview_access_tags += mmJQuery(this).val()+",";
	    	count++;
	    });
	    mmJQuery("#mm-applied-tag-count").html(count);
	    return preview_access_tags;
	},
	
	startPreview: function()
	{
		document.location.reload();
	},
	
	showAccessTags: function()
	{
		if(mmJQuery("#mm-preview-access-tags").is(":hidden"))
		{
			mmJQuery("#mm-preview-access-tags").show();
		}
		else
		{
			mmJQuery("#mm-preview-access-tags").hide();
		}
	},
	
	handleSave: function(data)
	{
		if(data.type=='error')
		{
			alert(data.message);
		}
		else
		{
			document.location.reload();
		}
	},
	
	toggleView: function(data)
	{
		if(data.type=='error')
		{
			alert(data.message);
		}
	},
	
	handleUpdate: function(data)
	{
		if(data.type=='error')
		{
			alert(data.message);
		}
		else
		{
			this.enableChangeButton();
			if(data.message.access_tags != undefined)
			{

		        mmJQuery("#preview_access_tags").find("option").remove().end().append(data.message.access_tags);
				//mmJQuery("#mm-preview-access-tag-results").html(data.message.access_tags);
			}
			if(data.message.days != undefined)
			{
		        mmJQuery("#mm-preview-days").find("option").remove().end().append(data.message.days);
			}
		}
	},
	
});

var mmPreviewJs = new MM_Preview("MM_Preview", "Preview");