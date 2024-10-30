<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

$campaignSettings = new MM_CampaignOptions($p->id);
$arr = json_decode($campaignSettings->getAttr());
$gatewayId = 0;
if(is_object($arr)){
	if(isset($arr->gateway_id)){
		$gatewayId = $arr->gateway_id;
	}
}
$p->show_on_reg = $campaignSettings->getShowOnReg();
$p->name = $campaignSettings->getName();

$options = MM_LimeLightUtils::getPaymentOptions();
$htmlOptions = MM_HtmlUtils::generateSelectionsList($options);

$options = MM_CampaignOptions::getOptions("gateway");
$gatewayOptions = MM_HtmlUtils::generateSelectionsList($options,$gatewayId);

?>
<div id="mm-form-container">
<input type='hidden' id='mm_id' value='<?php echo $p->id; ?>' />
<input type='hidden' id='mm_setting_type' value='payment' />
<table>
<tr>
	<td>Name</td>
	<td><input type='text' id='mm-name' value='<?php echo ((isset($p->name))?$p->name:''); ?>' style='width: 225px;'/></td>
</tr>
<tr><td colspan='2'>&nbsp;</td></tr>
<tr>
	<td>
		Method
	</td>
	<td>
		<select id='mm-gateways' onchange="mmjs.chooseGateway()">
			<option value=''>Choose Gateway</option>
			<?php echo $gatewayOptions; ?>
		</select>
	</td>
</tr> 
<tr><td colspan='2'>&nbsp;</td></tr>
<tr id='mm_gateway_info_row' style='display:none;'>
	<td colspan='2'>
		<table id='mm_gateway_info_table' style='padding-left: 70px;'>
			<!-- data inserted here based on attributes -->
		</table>
	</td>
</tr>
<tr><td colspan='2'>&nbsp;</td></tr>
<tr>
	<td>
		Show On Registration
	</td>
	<td>
		<input type='checkbox' id='mm-show-on-reg-chk' onchange="mmjs.setShowOnReg();" <?php echo (($p->show_on_reg=="1")?"checked":""); ?>/>
		<input type='hidden' id='mm-show-on-reg' value="<?php echo $p->show_on_reg; ?>"/>
	</td>
</tr> 
<tr><td colspan='2'>&nbsp;</td></tr>
<tr id='mm-types-row' style='display:none;'>
	<td>Types</td>
	<td>
		<select id='mm-types' multiple size='5' >
			<?php echo $htmlOptions; ?>
		</select>
	</td>
</tr>
</table>
</div>
<script type='text/javascript'>
<?php if($p->id>0){ ?>
mmjs.chooseGateway();
<?php } ?>
</script>