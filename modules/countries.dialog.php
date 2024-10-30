<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
MM_Session::value("campaign_setting_module", 'country');

$campaignSettings = new MM_CampaignOptions($p->id);
$p->name = $campaignSettings->getName();

$options = MM_LimeLightUtils::getCountryOptions();
$htmlOptions = MM_HtmlUtils::generateSelectionsList($options, $campaignSettings->getAttr());

?>
<div id="mm-form-container">
<input type='hidden' id='mm_id' value='<?php echo $p->id; ?>' />
<input type='hidden' id='mm_setting_type' value='country' />
<table>
<tr>
	<td>Country Name</td>
	<td><select id='mm-name'>
		<?php echo $htmlOptions; ?>
	</select></td>
</tr>
</table>
</div>