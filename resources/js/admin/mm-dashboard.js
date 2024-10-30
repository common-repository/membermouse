/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_DashboardViewJS = MM_Core.extend({
  
	toggleGuide: function(value)
	{
        var values = {
            toggle_value:value,
            mm_action: "save"
        };

        var ajax = new MM_Ajax(false, this.module, this.action, this.method);
        ajax.send(values, false, 'mmjs',this.updateHandler); 
	},
});

var mmjs = new MM_DashboardViewJS("MM_DashboardView", "");