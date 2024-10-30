/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_AccessTagsViewJS = MM_Core.extend({
  
	processForm: function()
	{
		// status
		mmJQuery("#mm-status").attr('value', mmJQuery('#mm-status-container input:radio:checked').val());
 	  
		// subscribtion type
		var subTypeSelection = mmJQuery('#mm-subscription-container input:radio:checked').val();
 	  
		mmJQuery("#mm-subscription-type").attr('value', subTypeSelection);
 	  
		if(subTypeSelection == 'paid') {
			mmJQuery("#mm-products\\[\\]").attr("disabled","");
			mmJQuery("#mm-products\\[\\]").show();
		} else {
			mmJQuery("#mm-products\\[\\]").attr("disabled","disabled");
			mmJQuery("#mm-products\\[\\]").hide();
		}
		


	    mmJQuery("select[id=mm-products\[\]] :disabled").each(function()
	    	    {
	    	mmJQuery(this).attr("selected","selected");
	    	mmJQuery(this).removeAttr("disabled");
	    	    });
	},

	setRequiredMemberTypes: function(){
		
	    mmJQuery("select[id=mm-products\[\]] :disabled").each(function()
	    	    {
	    	mmJQuery(this).attr("selected","selected");
	    	mmJQuery(this).attr("disabled","disabled");
	    	    });
		
	},
   
	validateForm: function()
	{
		// display name 
		if(mmJQuery('#mm-display-name').val() == "") {
			alert("Display name is required");
			return false;
		}
	   
		// subscription type
		if(mmJQuery("#mm-subscription-type").val() == "paid" && mmJQuery("#mm-products\\[\\]").val() == null) {
			alert("Please select a product or set subscription type to Free");
			return false;
		}
	   
		return true;
	}
});

var mmjs = new MM_AccessTagsViewJS("MM_AccessTagsView", "Access Tag");

