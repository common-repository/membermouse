<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

/*
 * Odd situation where is_admin() WP function returns true and should be false.
 * Function helper is used throughout this file only and a new method can be applied here if needed. 
 */
	function isAdminMode(){
		return MM_Utils::isAdmin();
	}
	$isEditMode = false;
	$selectedMember ="";
	if(isset($p->siteId) && intval($p->siteId)>0){
		$p->siteId = $p->siteId;
	}
	
	$campaignHtml = ""; 
	
	if(isset($p->siteId) && intval($p->siteId) > 0) {
		if(isAdminMode()){
			$result = MM_MemberMouseService::getSite($p->siteId);
			
			if(is_object($result))
			{	
				$selectedMember = $result->member_id;
				$isEditMode = true;
				$site = new MM_Site($p->siteId);
				$campaignArr = array();
				if($site->isValid()) {
					//$campaign = MM_LimeLightService::getCampaign($site->getCampaignIds());
					MM_LimeLightService::validate($site->getLLUrl(),$site->getLLUsername(), $site->getLLPassword());
					$campaignArr = explode(",", $site->getCampaignIds());
					$llCampaigns = MM_LimeLightService::getCampaigns();
					if($llCampaigns!==false){
						foreach($llCampaigns as $campaignId){
							if(array_search($campaignId, $campaignArr)!==false){
								$campaignArr[] = $campaignId;
							}
						}
					}
				}
			}	
			$campaignHtml = MM_SiteMgmtView::getCampaignGroupHtml($campaignArr, true, explode(",", $site->getCampaignsInUse()));
		}
		else{
			$isEditMode = true;
			
			$site = new MM_Site($p->siteId);
			
			$campaignArr = array();
			if($site->isValid()) {
				//existing campaigns
				$campaignArr = explode(",", $site->getCampaignIds());
				
				//validate to set new ll info
				if(MM_LimeLightService::validate($site->getLLUrl(), $site->getLLUsername(), $site->getLLPassword())){
					
					// merge additional campaigns setup after initial activation.
					$llCampaigns = MM_LimeLightService::getCampaigns($site);
					if($llCampaigns!==false){
						foreach($llCampaigns as $campaignId){
							if(array_search($campaignId, $campaignArr)!==false){
								$campaignArr[] = $campaignId;
							}
						}
					}
				}
			}
			$campaignHtml = MM_SiteMgmtView::getCampaignGroupHtml($campaignArr, true, explode(",", $site->getCampaignsInUse()));
		}
	}
	else {
		$campaignHtml = MM_SiteMgmtView::getCampaignGroupHtml();
		$site = new MM_Site("", false);
	}
	$useLL = false;
	
	$llUsername = $site->getLLUsername();
	
	if($isEditMode && strlen($llUsername)>0){
		$useLL =true;
	}
	else if(!$isEditMode){
		$useLL = true;
	}
?>

<style>
.ui-progressbar-value { background-image: url('<?php echo MM_IMAGES_URL."pbar-animated.gif" ?>'); }
</style>
		
<div id="mm-form-container">
	<table cellspacing="10" style="font-size:12px">
		<?php 
			if(isAdminMode()){
				?>
				<tr>
					<td width="200">Member ID</td>
					<td>
						<input id='mm-member-id' type='text' style='width: 50px;' value='<?php echo $selectedMember; ?>'>
					</td>
				</tr>
				<tr>
					<td>Is Dev</td>
					<td>
						<input id="mm-is-dev-chk" type="checkbox" style="width:300px"	 <?php echo (($site->isDev()=='1')?'checked':''); ?> onchange="mmjs.updateDev()"/>
						<input type='hidden' id='mm-is-dev' value='<?php echo (($site->isDev()=='1')?'1':'0'); ?>' />
					</td>
				</tr>
				<tr>
					<td>Is MM</td>
					<td>
						<input id="mm-is-mm-chk" type="checkbox" style="width:300px"	 <?php echo (($site->isMM()=='1')?'checked':''); ?> onchange="mmjs.updateMM()"/>
						<input type='hidden' id='mm-is-mm' value='<?php echo (($site->isMM()=='1')?'1':'0'); ?>' />
					</td>
				</tr>
				<tr>
					<td width="200">Status</td>
					<td>
						<select id='mm-member-status'>
							<option value='0'>Not Activated</option>
							<option value='<?php echo MM_MemberStatus::$ACTIVE; ?>' <?php echo (($site->getStatus()==MM_MemberStatus::$ACTIVE)?"selected":""); ?>>Activated</option>
							<option value='<?php echo MM_MemberStatus::$LOCKED; ?>' <?php echo (($site->getStatus()==MM_MemberStatus::$LOCKED)?"selected":""); ?>>Locked</option>
							<option value='<?php echo MM_MemberStatus::$PAUSED; ?>' <?php echo (($site->getStatus()==MM_MemberStatus::$PAUSED)?"selected":""); ?>>Paused</option>
							<option value='<?php echo MM_MemberStatus::$OVERDUE; ?>' <?php echo (($site->getStatus()==MM_MemberStatus::$OVERDUE)?"selected":""); ?>>Overdue</option>
							<option value='<?php echo MM_MemberStatus::$CANCELED; ?>' <?php echo (($site->getStatus()==MM_MemberStatus::$CANCELED)?"selected":""); ?>>Canceled</option>
							
						</select>
					</td>
				</tr>
				<?php 
			}
		?>
		<tr>
			<td width="200">Site Name</td>
			<td><input id="mm-site-name" type="text" style="width:300px" value='<?php echo $site->getName(); ?>'/></td>
		</tr>
		<tr>
			<td>Site URL</td>
			<td><input id="mm-site-url" type="text" style="width:300px" value='<?php echo $site->getLocation(); ?>'/></td>
		</tr>
		<tr>
			<td>Use Lime Light profile</td>
			<td><input id="mm-site-use-ll" type="checkbox" onchange="sitemgmt_js.toggleLLInfo();" <?php echo (($useLL) ? "checked":""); ?> <?php echo (($isEditMode) ? "disabled='disabled'":""); ?> /></td>
		</tr>
	</table>
	
	<span style="font-size:14px; color: #555;" id="mm-ll-info-title"><b>Lime Light Information</b></span>
	<table cellspacing="10" style="font-size:12px" id="mm-ll-info">
		<?php if($isEditMode) { ?>
			<?php if(isAdminMode()) { ?>
				<tr>
					<td width="100" colspan='2'>Choose Campaign(s)</td>
				</tr>
				<tr>
					<td colspan='2'>
						<div id='mm-campaign-list'><?php echo $campaignHtml; ?></div>
					</td>
				</tr>
				<?php }else{ ?>
				<tr>
					<td width="100" colspan='2'>Choose Campaign(s)</td>
				</tr>
				<tr>
					<td colspan='2'>
						<div id='mm-campaign-list'><?php echo $campaignHtml; ?></div>
					</td>
				</tr>
			<?php } ?>
		<?php } ?>
		<tr>
			<td width="100">URL</td>
			<td><input id="mm-ll-url" type="text" style="width:300px" value='<?php echo $site->getLLUrl(); ?>' <?php echo $isEditMode ? "disabled='disabled'":""; ?>/></td>
		</tr>
		<tr>
			<td>Username</td>
			<td><input id="mm-ll-api-key" type="text" style="width:300px" value='<?php echo $site->getLLUsername(); ?>' <?php echo $isEditMode ? "disabled='disabled'":""; ?>/></td>
		</tr>
		<tr>
			<td>Password</td>
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
					<button onclick="sitemgmt_js.verifyLimeLight();" type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">
						<span class="ui-button-text">Verify Lime Light</span>
					</button>
					</div>
					
					<div id="mm-progressbar-container" style="display:none;">
						<div id="mm-progressbar" style="width:150px"></div>
						<script>
						mmJQuery(function() {
							mmJQuery("#mm-progressbar").progressbar({
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
	<input id="mm-ll-campaign-id" type="hidden" value='<?php echo $site->getCampaignIds(); ?>'/>
	<?php } ?>
	
	<?php if(!isAdminMode()){ ?>
		<input id='mm-member-id' type='hidden' value='<?php echo $p->memberId; ?>' />
	<?php } ?>
	<input id='mm-site-id' type='hidden' value='<?php echo $site->getId(); ?>' />
	<input id="mm-ll-verified" type="hidden" value='<?php echo $isEditMode ? "1":"0"; ?>'/>
</div>
<script type='text/javascript'>
<?php if($isEditMode){ ?>
sitemgmt_js.toggleLLInfo();
<?php } ?>
</script>
