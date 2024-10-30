/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var MM_LimeLightJS = MM_Core.extend({
	syncLimeLight: function()
	{
        var values = {
            mm_action: "syncLimeLight"
        };

        var ajax = new MM_Ajax(false, this.module, this.action, this.method);
        ajax.send(values, false, 'mmjs', this.updateHandler); 
	},
	
	showCampaignInfo: function(){
		var campaignId = mmJQuery("#campaign_id").val();
		if(campaignId>0){
			document.location.href="admin.php?page=ecommerce_settings&module=limelight&campaign_id="+campaignId;
		}
	},
});

var mmjs = new MM_LimeLightJS("MM_LimeLightView", "LimeLight");