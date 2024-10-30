/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_Login = Class.extend({
	checkFields: function()
	{
		var username = mmJQuery("#log").val();
		var password = mmJQuery("#pwd").val();
		if(username.length<=0)
		{
			mmJQuery("#mm-errors").html("Please provide a username.");
			return false;
		}
		else if(password.length<=0)
		{
			mmJQuery("#mm-errors").html("Please provide a password.");
			return false;
		}
		return true;
	},
	  
});


var login_js = new MM_Login();