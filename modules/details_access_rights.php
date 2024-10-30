<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

if(isset($_REQUEST[MM_Session::$PARAM_USER_ID])) {
	global $mmSite;
	
	$user = new MM_User($_REQUEST[MM_Session::$PARAM_USER_ID]);
	
	if($user->isValid()) {
		include_once MM_MODULES."/details.header.php";
		$memberType = new MM_MemberType($user->getMemberTypeId());
		$memberTypeName = $memberType->getName();
		
		$flagNoPay = "0";
		if(MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_INSTALL_TYPE) != MM_Site::$INSTALL_TYPE_LIMELIGHT){
			$flagNoPay = "1";
		}
?>
<div id="mm-form-container">
	<table cellspacing="8">
		<tr>
			<td width="130"><span class="mm-section-header">Change Status</span></td>
			<td>
			<td>
				<input id="mm-order-id" type="hidden" value="<?php echo $user->getLastOrderId(); ?>" />
				<?php
					if(!$user->isFree()) 
					{
						$product = new MM_Product($memberType->getRegistrationProduct());
						$productName = $product->getName();
					}
					else {
						$productName = "";	
					}
					
					$hasActiveSubscriptions = count($user->getAccessTags(true)) > 0;
				?>
				<?php if($user->getStatus() == MM_MemberStatus::$CANCELED || $user->getStatus() == MM_MemberStatus::$OVERDUE) { ?>
					<a onclick="mmjs.activateMembership('<?php echo $user->getId(); ?>', '<?php echo $productName; ?>','<?php echo $flagNoPay; ?>')" class="button-secondary"><img src="<?php echo MM_Utils::getImageUrl('accept'); ?>" /> Activate Membership</a>
				<?php } ?>
				
				<?php if($user->getStatus() == MM_MemberStatus::$PAUSED) { ?>
					<a onclick="mmjs.activateMembership('<?php echo $user->getId(); ?>', '<?php echo $productName; ?>','<?php echo $flagNoPay; ?>')" class="button-secondary"><img src="<?php echo MM_Utils::getImageUrl('accept'); ?>" /> Activate Membership</a>
				<?php } else if($user->getStatus() != MM_MemberStatus::$CANCELED && $user->getStatus() != MM_MemberStatus::$OVERDUE) { ?>
					<?php if($user->getStatus() != MM_MemberStatus::$LOCKED) { ?>
						<a onclick="mmjs.lockAccount('<?php echo $user->getId(); ?>', '<?php echo $productName; ?>')" class="button-secondary"><img src="<?php echo MM_Utils::getImageUrl('lock'); ?>" /> Lock Account</a>
					
					<?php } else { ?>
						<a onclick="mmjs.unlockAccount('<?php echo $user->getId(); ?>')" class="button-secondary"><img src="<?php echo MM_Utils::getImageUrl('lock_open'); ?>" /> Unlock Account</a>
					<?php } ?>
				<?php } ?>
				
				<?php if($user->getStatus() == MM_MemberStatus::$ACTIVE) { ?>
						<a onclick="mmjs.cancelMembership('<?php echo $user->getId(); ?>', '<?php echo $productName; ?>', '<?php echo $hasActiveSubscriptions; ?>')" class="button-secondary"><img src="<?php echo MM_Utils::getImageUrl('stop'); ?>" /> Cancel Membership</a>
						<a onclick="mmjs.pauseMembership('<?php echo $user->getId(); ?>', '<?php echo $productName; ?>', '<?php echo $hasActiveSubscriptions; ?>')" class="button-secondary"><img src="<?php echo MM_Utils::getImageUrl('pause'); ?>" /> Pause Membership</a>
				<?php } ?>
			</td>
		</tr>
	</table>
	
	<?php if($user->getStatus() == MM_MemberStatus::$ACTIVE) { ?>
	<div style="width: 750px; margin-top: 8px;" class="mm-divider"></div>
	
	<table cellspacing="8">
		<tr>
			<td width="130"><span class="mm-section-header">Change Membership</span></td>
			<td>
			<td>
				<?php 
					if($user->isFree() && !$user->hasCardOnFile()) {
						$subType = MM_MemberType::$SUB_TYPE_FREE;
					} 
					else {
						$subType = "";
					}
				?>
				Change from <?php echo $memberTypeName; ?> to 
				<select id="mm-new-membership-selection">
					<?php echo MM_HtmlUtils::getMemberTypesList($user->getMemberTypeId(), true, $subType); ?>
				</select>
				<a onclick="mmjs.changeMembership('<?php echo $user->getId(); ?>', '<?php echo $user->getMemberTypeId(); ?>','<?php echo $flagNoPay; ?>')" class="button-secondary"><img src="<?php echo MM_Utils::getImageUrl('user_edit'); ?>" /> Change</a>
				
				<?php if($user->isFree() && !$user->hasCardOnFile() && MM_Site::$INSTALL_TYPE_LIMELIGHT == MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_INSTALL_TYPE)) { ?>
					<div style="margin-top: 10px; padding: 10px; color: #876C33; background-color: #FFFFE0; line-height: 8px; width: 450px; border: 1px solid #E6DB55;">
						<b>Instructions for Changing to a Paid Membership</b>
						
						<p>This member doesn't have a credit card on file. Please follow the instructions below to change to a paid membership:</p>
						<ol>
							<li>
								<?php $paidMemberTypes = MM_HtmlUtils::getMemberTypesList($user->getMemberTypeId(), true, MM_MemberType::$SUB_TYPE_PAID); ?>
								Select the paid membership you want to change to: <br/>
								<select id="mm-new-membership-paid-selection" onchange="mmjs.updatePaidProductSelection()">
									<option value="0">Select a paid membership</option>
									<?php echo $paidMemberTypes; ?>
								</select>
							</li>
							<li>
								Go to the <a href="<?php echo MM_LimeLightUtils::getLLPlaceOrderUrl() ?>" target="_blank">place order</a> form in Lime Light CRM
							</li>
							<li>
								<?php 
									$campaign = new MM_Campaign();
									if(isset($product) && $product instanceof MM_Product){
										$campaign = new MM_Campaign($product->getCampaignId());
									}
									
								?>
								Select the campaign <span class="mm-code" id="mm-campaign-name" style="margin-left: 8px;"></span>
								<input type='hidden' id='mm-campaign-id' value='' />
							</li>
							<li>
								Select the product <span id="mm-paid-product" style="margin-left: 8px;" class="mm-code"><i>no member type selected</i></span>
							</li>
							<li>
								Fill out the customer's information and click <i>Process Order</i>
							</li>
							<li>
								Enter the new order ID below and click <i>Attach Order</i>:<br/>
								
								<div style="margin-left: 10px;">
								<table cellspacing="6" style="width:270px">
									<tr>
										<td>Order ID: </td>
										<td align="right"><input id="mm-attach-order-id" type="text" /></td>
									</tr>
									<tr><td colspan="2" align="right">
										<a onclick="mmjs.attachOrder('<?php echo $user->getId(); ?>')" class="button-secondary">Attach Order</a>
									</td></tr>
								</table>
								</div>
							</li>
						</ol>
					</div>
				<?php } ?>
			</td>
		</tr>
	</table>
	
	<div style="width: 750px; margin-top: 8px;" class="mm-divider"></div>
	
	<table cellspacing="8">
		<tr>
			<td width="130"><span class="mm-section-header">Manage Access Tags</span></td>
			<td>
			<td>
				<div>
					<?php 
						$tags = $memberType->getAccessTags();
						
						if($tags) {
					?>
					<div style="margin-bottom: 15px;">
						<span style="font-weight: bold; color: #555;">Access Tags Applied via Member Type</span><br/>
						<img src="<?php echo MM_Utils::getImageUrl('tag'); ?>" style="vertical-align: middle" />
						<?php 
						$str = "";
						foreach($tags as $id=>$name) 
						{
							if($str != "") {
								$str .= ", ";
							} 
							
							$str .= $name;
						}
						
						echo $str;
						?>
					</div>
					<?php } ?>
					
					<div id="mm-grid-container" style="width:600px;">
						<?php include_once MM_MODULES."/details_access_rights.accesstags.php"; ?>
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>

<?php 
		}
	}
}
?>	