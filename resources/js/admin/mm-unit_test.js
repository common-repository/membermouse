/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_UnitTestViewJS = MM_Core.extend({

    startTest: function(){
		var test = mmJQuery("#unit-test-name").val();
		var values = {};
		values.mm_action = "runGroup"+test;

		var results = "";
		if(test==3){
			results= this.testJsObjects();
			if(results==false){
				return false;
			}
			else{
				values.results = results;
			}
		}
		
		mmJQuery("#mm-unit-test-progress").html("Initiating test group "+test);
        var ajax = new MM_Ajax(false, this.module, this.action, this.method);
        ajax.send(values, false, 'unitTest','testCallback'); 
	},
	
	createTextArea: function(id, name, data){
		if(mmJQuery("#"+id).length){
			mmJQuery("#"+id).html(data);
		}
		return "<b>"+name+"</b><br /><textarea id='"+id+"' rows='20' cols='80'>"+data+"</textarea><br />";
	},
	
	testCallback: function(data){
		if(data.type=='error'){
			alert(data.message);
		}
		else{
			mmJQuery("#mm-unit-test-progress").html("");
			var html = this.createTextArea('mm-unit-test-area', 'Unit Test Results', data.message);
			mmJQuery("#mm-unit-test").html(html);
		}
	},
	
	isValidObject: function(viewObjName, objName, objTitle){
		var testObj = "var obj = new "+objName+"('"+viewObjName+"', '"+objTitle+"');";
		try{
			eval(testObj);
			return true;
		}
		catch(e){
			return false;
		}
	},
	
	testJsObjects: function(){
		var objs = new Array();
		objs.push("MM_AccessTagsViewJS");
		objs.push("MM_MemberTypesViewJS");
		objs.push("MM_MembersViewJS");
		objs.push("MM_InstantNotificationViewJS");
		objs.push("MM_MemberDetailsViewJS");
		
		var results = "";
		for(i=0; i<objs.length; i++){
			if(this.isValidObject("MM_TestView", objs[i], "Test Object")==false){
				var html = this.createTextArea('mm-unit-test-area', 'Unit Test Results', "Could not invoke "+objs[i]);
				mmJQuery("#mm-unit-test").html(html);
				return false;
			}
			else{
				results+= "--"+objs[i]+" was valid.\n"; 
			}
		}
		return results;
	},
	
});

var unitTest = new MM_UnitTestViewJS("MM_UnitTestView", "Unit Test");