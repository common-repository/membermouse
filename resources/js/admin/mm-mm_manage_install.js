/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_ManageInstallViewJS = MM_Core.extend({
    
	showReleaseNotes: function(version){

		var dialogId = 'mm-release-notes-dialog';
		mmJQuery("#"+dialogId).dialog({autoOpen: false, buttons: {
			"Done": function() { mmjs.closeDialog(); }}});
		var values =  {};
		values.mm_action = "showReleaseNotes";
		values.mm_module = 'showReleaseNotes';
		values.version = version;
		
		
		mmdialog_js.showDialog(dialogId, this.module, 640, 282, "Release Notes "+version, values);
		
	},

});

var mmjs = new MM_ManageInstallViewJS("MM_ManageInstallView", "Release Notes");