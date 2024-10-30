<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
	$isEditMode = false;
	$selectedMember ="";
	
	$site = null;
	if(isset($p->id) && intval($p->id) > 0) {
		$result = MM_MemberMouseService::getSite($p->id);
		
		if(is_object($result))
		{	
			$selectedMember = $result->member_id;
			$isEditMode = true;
			$site = new MM_Site($p->id);
			if($site->isValid()) {
				$campaign = MM_LimeLightService::getCampaign($site->getCampaignId());
			}
		}
	}
	else {
		$site = new MM_Site("", false);
	
		// TEST ONLY
		$site->setLLUrl("http://www.tri8crm.com");
		$site->setLLUsername("tri8crm.com");
		$site->setLLPassword("8vdD2nXSaChQRi");
	}
	
?>

<style>
.ui-progressbar-value { background-image: url('<?php echo MM_IMAGES_URL."pbar-animated.gif" ?>'); }
</style>
		
<div id="mm-form-container">
	<table cellspacing="10" style="font-size:12px">
		<tr>
			<td width="100">Member ID#</td>
			<td>
				<input id='mm-member-id' type='text' style='width: 50px;' value='<?php echo $selectedMember; ?>'>
			</td>
		</tr>
		<tr>
			<td width="100">Status</td>
			<td>
				<select id='mm-member-status'>
					<option value='0'>Not Activated</option>
					<option value='<?php echo MM_MemberStatus::ACTIVE; ?>' <?php echo (($site->getStatus()==MM_MemberStatus::ACTIVE)?"selected":""); ?>>Activated</option>
					<option value='<?php echo MM_MemberStatus::LOCKED; ?>' <?php echo (($site->getStatus()==MM_MemberStatus::LOCKED)?"selected":""); ?>>Locked</option>
				</select>
			</td>
		</tr>
		<tr>
			<td width="100">Site Name</td>
			<td><input id="mm-site-name" type="text" style="width:300px" value='<?php echo $site->getName(); ?>'/></td>
		</tr>
		<tr>
			<td>Site URL</td>
			<td><input id="mm-site-url" type="text" style="width:300px" value='<?php echo $site->getLocation(); ?>'/></td>
		</tr>
	</table>
	
	<span style="font-size:14px; color: #555;"><b>Lime Light Information</b></span>
	<table cellspacing="10" style="font-size:12px">
		<?php if($isEditMode) { ?>
		<tr>
			<td width="100">Campaign</td>
			<td><?php echo (isset($campaign["campaign_name"]))?$campaign["campaign_name"]:"N/A"; ?></td>
		</tr>
		<?php } ?>
		<tr>
			<td width="100">URL</td>
			<td><input id="mm-ll-url" type="text" style="width:300px" value='<?php echo $site->getLLUrl(); ?>' <?php echo $isEditMode ? "disabled='disabled'":""; ?>/></td>
		</tr>
		<tr>
			<td>API Key</td>
			<td><input id="mm-ll-api-key" type="text" style="width:300px" value='<?php echo $site->getLLUsername(); ?>' <?php echo $isEditMode ? "disabled='disabled'":""; ?>/></td>
		</tr>
		<tr>
			<td>API Password</td>
			<td><input id="mm-ll-api-password" type="password" style="width:300px" value='<?php echo $site->getLLPassword(); ?>' <?php echo $isEditMode ? "disabled='disabled'":""; ?>/></td>
		</tr>
	</table>
	
	<?php if(!$isEditMode) { ?>
	<div id="mm-ll-campaign-container">
		<table cellspacing="10" style="font-size:12px">
			<tr>
				<td width="100"></td>
				<td>
					<div id="mm-verify-ll-button">
					<button onclick="mmjs.verifyLimeLight();" type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">
						<span class="ui-button-text">Verify Lime Light</span>
					</button>
					</div>
					
					<div id="mm-progressbar-container" style="display:none;">
						<div id="mm-progressbar" style="width:150px"></div>
						<script>
						$(function() {
							$("#mm-progressbar").progressbar({
								value: 100
							});
						});
						</script>
					</div>
					
				</td>
			</tr>
		</table>
	</div>
	<?php } else { ?>
	<input id="mm-ll-campaign-id" type="hidden" value='<?php echo $site->getCampaignId(); ?>'/>
	<?php } ?>
	
	<input id='mm-site-id' type='hidden' value='<?php echo $site->getId(); ?>' />
	<input id="mm-ll-verified" type="hidden" value='<?php echo $isEditMode ? "1":"0"; ?>'/>
</div>
