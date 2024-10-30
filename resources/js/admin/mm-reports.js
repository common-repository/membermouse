/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_ReportViewJS = MM_Core.extend({

	includeMemberType: function(){
		if(!mmJQuery("#mm_member_types").is(":visible")){
			mmJQuery("#mm_member_types").show();
		}
		else{
			mmJQuery("#mm_member_types").hide();
		}
	},
	
	validateType: function(tagName, title){

		if(mmJQuery("#mm_include_"+tagName).is(":checked")){
			var optionTag = "";
			if(mmJQuery('#mm_'+tagName+'_opt_all').is(":checked")){
				optionTag = "all";
			}
			else{
				optionTag = "selected";
			}
			
			if(optionTag == 'selected'){
				var countTypes =0;
			    mmJQuery("select[id=mm_"+tagName+"_sel\[\]] :selected").each(function()
	    	    {
	    	    	var id = mmJQuery(this).val();
	    	    	var text = mmJQuery(this).text();
	    	    	countTypes++;
	    	    });
			    
			    if(countTypes<=0){
			    	alert("Please select at least one "+title+".");
			    	return false;
			    }
			}
		}
		return true;
	},
	
	validateForm: function(){
		
		var ret = this.validateType('access_tags', 'Access Tags');
		if(!ret){
			return ret;
		}
		ret = this.validateType('member_types', 'Member Types');
		if(!ret){
			return ret;
		}
		
		var fromDate = mmJQuery("#mm_from_date").val();
		var toDate = mmJQuery("#mm_to_date").val();
		
		if(fromDate.length<=0){
			alert("Must include a from date.");
			return false;
		}
		
		if(toDate.length<=0){
			alert("Must include a from date.");
			return false;
		}
		return true;
	},
	
	includeAccessTag: function(){
		if(!mmJQuery("#mm_access_tags").is(":visible")){
			mmJQuery("#mm_access_tags").show();
		}
		else{
			mmJQuery("#mm_access_tags").hide();
		}
	},
	
	showMemberTypes: function(){
		if(mmJQuery("#mm_member_types_sel\\[\\]").attr("disabled")){
			mmJQuery("#mm_member_types_sel\\[\\]").attr('disabled','');
		}
		else{
			mmJQuery("#mm_member_types_sel\\[\\]").attr('disabled','disabled');
		}
	},
	
	showAccessTags: function(){
		if(mmJQuery("#mm_access_tags_sel\\[\\]").attr("disabled")){
			mmJQuery("#mm_access_tags_sel\\[\\]").attr('disabled','');
		}
		else{
			mmJQuery("#mm_access_tags_sel\\[\\]").attr('disabled','disabled');
		}
	},
	
});

var mmjs = new MM_ReportViewJS("MM_RetentionReportsView", "Reports View");