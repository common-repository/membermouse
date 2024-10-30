/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_AccountTypesViewJS = MM_Core.extend({
     
	processForm: function()
	{
		// status
		mmJQuery("#mm-status").attr('value', mmJQuery('#mm-status-container input:radio:checked').val());
 	  
		// unlimited paid members
		if(mmJQuery('#mm-cb-unlimited-paid-members:checked').val() != undefined) {
			mmJQuery("#mm-unlimited-paid-members").attr('value', 'yes');
			mmJQuery("#mm-num-paid-members").attr("disabled","disabled");
			mmJQuery("#mm-num-paid-members").attr("value","0");
		} else {
			mmJQuery("#mm-unlimited-paid-members").attr('value', 'no');
			mmJQuery("#mm-num-paid-members").attr("disabled","");
		}
	   
		// unlimited total members
		if(mmJQuery('#mm-cb-unlimited-total-members:checked').val() != undefined) {
			mmJQuery("#mm-unlimited-total-members").attr('value', 'yes');
			mmJQuery("#mm-num-total-members").attr("disabled","disabled");
			mmJQuery("#mm-num-total-members").attr("value","0");
		} else {
			mmJQuery("#mm-unlimited-total-members").attr('value', 'no');
			mmJQuery("#mm-num-total-members").attr("disabled","");
		}
	},
	
	validateForm: function()
	{
		// display name 
		if(mmJQuery('#mm-display-name').val() == "") {
			alert("Display name is required");
			return false;
		}
	   
		// # of sites
		if(mmJQuery('#mm-num-sites').val() == "") {
			alert("# of sites is required");
			return false;
		}
		
		// # of paid members
		if(mmJQuery("#mm-unlimited-paid-members").val() == "no" && (mmJQuery("#mm-num-paid-members").val() == "" || mmJQuery("#mm-num-paid-members").val() == "0")) {
			alert("Either mark paid members as unlimited or provide a value.");
			return false;
		}
	   
		// # of total members
		if(mmJQuery("#mm-unlimited-total-members").val() == "no" && (mmJQuery("#mm-num-total-members").val() == "" || mmJQuery("#mm-num-total-members").val() == "0")) {
			alert("Either mark total members as unlimited or provide a value.");
			return false;
		}
		
		// make sure total members is greater than paid members
		if(mmJQuery("#mm-unlimited-total-members").val() == "no" && mmJQuery("#mm-unlimited-paid-members").val() == "yes" ||
			(mmJQuery("#mm-unlimited-total-members").val() == "no" && mmJQuery("#mm-unlimited-paid-members").val() == "no") && 
			(parseInt(mmJQuery("#mm-num-paid-members").val()) > parseInt(mmJQuery("#mm-num-total-members").val()))) {
			alert("Number of total members must be greater than the number of paid members");
			return false;
		}
		
		return true;
	}
});

var mmjs = new MM_AccountTypesViewJS("MM_AccountTypesView", "Account Type");