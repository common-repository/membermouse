/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_APITestViewJS = MM_Core.extend({
    chooseForm: function(){
		var form = mmJQuery("#method").val();
		var values = {};
		values.form = form;
		values.mm_action = "chooseForm";

        var ajax = new MM_Ajax(false, this.module, this.action, this.method);
        ajax.send(values, false, 'mmjs','chooseFormCallback'); 
	},
	
	chooseFormCallback: function(data){
		mmJQuery("#mm-api-test-sent").html("");
		mmJQuery("#mm-api-test-response").html("");
		mmJQuery("#mm-api-test").html(data.message);
	},
	
	changeToLL: function(){
		var memberChoice = mmJQuery("#user_type").val();
		if(memberChoice=="limelight"){
			mmJQuery("#membermouse_list").hide();
			mmJQuery("#limelight_list").show();
		}
		else{
			mmJQuery("#membermouse_list").show();
			mmJQuery("#limelight_list").hide();
		}
	},
	
	setMemberId: function (){
		var memberChoice = mmJQuery("#user_type").val();
		var id = "";
		if(memberChoice=="limelight"){
			id = mmJQuery("#customer_id").val();
		}
		else{
			id = mmJQuery("#user_id").val();
		}
		mmJQuery("#member_id").val(id);
	},
	
	createTextArea: function(id, name, data){
		if(mmJQuery("#"+id).length){
			mmJQuery("#"+id).html(data);
		}
		return "<b>"+name+"</b><br /><textarea id='"+id+"' rows='10' cols='60'>"+data+"</textarea><br />";
	},
	
	/** API Function executions **/
	callApiFunction: function(method){
	    var form_obj = new MM_Form('mm-api-test');
	    var values = form_obj.getFields();
		values.api_method = method;
		values.method = 'performAction';
		values.mm_action = "callApiMethod";

        var ajax = new MM_Ajax(false, this.module, this.action, this.method);
        ajax.send(values, false, 'mmjs','callMethodResponse');
	},
	
	callMethodResponse: function(data){
		var sent = data.message.sent;
		var resp = data.message.response;

		mmJQuery("#mm-api-test-sent").html(this.createTextArea('mm-sent-area','Data Sent',sent));
		mmJQuery("#mm-api-test-response").html(this.createTextArea('mm-respnose-area', 'Data Received',resp));
	},
});

var mmjs = new MM_APITestViewJS("MM_APITestView", "API Test");