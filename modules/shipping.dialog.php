<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
MM_Session::value("campaign_setting_module", 'shipping');

$campaignSettings = new MM_CampaignOptions($p->id);
$p->name = $campaignSettings->getName();
$p->attr = $campaignSettings->getAttr();
?>
<div id="mm-form-container">
<input type='hidden' id='mm_id' value='<?php echo $p->id; ?>' />
<input type='hidden' id='mm_setting_type' value='shipping' />
<table>
<tr>
	<td>Name</td>
	<td><input type='text' id='mm-name' value='<?php echo ((isset($p->name))?htmlentities($p->name,ENT_QUOTES):''); ?>' style='width: 225px;'/></td>
</tr>
<tr>
	<td>Rate</td>
	<td><input type='text' id='mm-rate' value='<?php echo ((isset($p->attr))?$p->attr:''); ?>' style='width: 50px;'/></td>
</tr>
</table>
</div>