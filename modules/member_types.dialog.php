<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
	global $mmSite;
	
	$mt = new MM_MemberType($p->id);
	$product = new MM_Product($mt->getRegistrationProduct());
	if(!$mt->isFree() && count($mt->getProductIds()) > 0 && !$mt->hasSubscribers()) {
		$productsDisabled = "";
	}
	else {
		$productsDisabled = "disabled='disabled'";
	}
	
	if($mt->hasSubscribers()) {
		$subTypeDisabled = "disabled='disabled'";
	} 
	else {
		$subTypeDisabled = "";	
	}
	
	if($mt->isDefault() == "0") {
		$disableForDefault = "";
	} 
	else {
		$disableForDefault = "disabled='disabled'";	
		$subTypeDisabled = "disabled='disabled'";	
	}
	
	$welcomeEmailChecked = ($mt->getWelcomeEmailEnabled()=='1')?"checked":"";
					$pids = $mt->getProductIds();
					
		
?>
<div id="mm-form-container">
	<table cellspacing="10">
		<tr>
			<td width="160">Display Name</td>
			<td><input id="mm-display-name" type="text" class="long-text" value='<?php echo htmlentities($mt->getName(),ENT_QUOTES); ?>'/></td>
		</tr>
		
		<tr>
			<td>Member Type Status</td>
			<td>
				<div id="mm-status-container">
					<input type="radio" name="status" value="active" onclick="mmjs.processForm()" <?php echo (($mt->getStatus()=="1")?"checked":""); ?> <?php echo $disableForDefault; ?> /> Active
					<input type="radio" name="status" value="inactive" onclick="mmjs.processForm()" <?php echo (($mt->getStatus()=="0")?"checked":""); ?> <?php echo $disableForDefault; ?> /> Inactive
				</div>
				
				<input id="mm-status" type="hidden" />
			</td>
		</tr>
		
		<?php if($mmSite->isMM()) { ?>
		<tr>
			<td>Account Type</td>
			<td>
				<select id="mm-account-types" class="long-text">
				<?php echo MM_HtmlUtils::getAccountTypesList($mt->getAccountTypeId()); ?>
				</select>
			</td>
		</tr>
		<?php } ?>
		
		<tr>
			<td>Subscription Type</td>
			<td>
				<div id="mm-subscription-container">
					<input type="radio" name="subscription-type" value="free" onclick="mmjs.processForm()" <?php echo ($mt->isFree() ? "checked":""); ?> <?php echo $subTypeDisabled; ?> /> Free
					<input type="radio" name="subscription-type" value="paid" onclick="mmjs.processForm()" <?php echo (!$mt->isFree() ? "checked":""); ?> <?php echo $subTypeDisabled; ?> /> Paid
				</div>
				
				<input id="mm-has-associations" type="hidden" value="<?php echo $mt->hasSubscribers() ? "yes" : "no"; ?>" />
				<input id="mm-subscription-type" type="hidden" />
				
				<div style="margin-top:5px">
					<select id="mm-products[]"  multiple  style='width: 95%; <?php if($mt->isFree()){ echo "display:none;"; } ?>' size='6' onchange="mmjs.filterRegistrationProducts();">
						<?php
							if(!$mt->isFree()){
								echo MM_HtmlUtils::getMemberTypeProducts($mt->getId(), $pids, $pids);
							}
							else{
								echo MM_HtmlUtils::getMemberTypeProducts($mt->getId(),$pids);
							}
						 ?>
					</select>
				</div>
				
			</td>
		</tr>
		<tr id='mm-registration-page-settings' style='<?php if($mt->isFree()){ echo "display:none;"; } ?>'>
		<td>&nbsp;</td>
		<td>
			<div style='margin-top: 5px;  <?php echo (($mt->getIncludeOnReg()!="1")?"display: none;":""); ?>' id='mm-register-product' >
			Default Product<br />
						<select id='mm-registration-product-id'  >
							<?php 
								if($product->isValid() && $product->getId()>0){
									?>
									<?php  
									$products = $mt->getProductIds();
									$productsArr = array();
									if(is_array($products)){
										foreach($products as $pid){
											$p = new MM_Product($pid);
											$productsArr[$pid] = $p->getProductDisplayName();
										}
										$selections = MM_HtmlUtils::generateSelectionsList($productsArr, $product->getId());
										if(empty($selections)){
	
									?>
									<option value=''>Choose Registration Product</option>
									<?php 
										}
										else{
											echo $selections;
										}	
									}
									else{
									?>
									<option value=''>Choose Registration Product</option>
									<?php 
									}
									?>
									<?php 
								}else{
									?>
									<option value=''>Choose Registration Product</option>
									<?php 
								}
							?>
						</select>
					</div>
			</td>
			</tr>
		
		<tr>
			<td colspan="2">
			<div style="width: 600px; margin-top: 8px;" class="mm-divider"></div>
			</td>
		</tr>
		
		<tr>
			<td>Registration Page Settings</td>
			<td>		
				<div>
					<input id="mm-cb-include-on-reg" type="checkbox" onclick="mmjs.processForm()" <?php echo (($mt->getIncludeOnReg()=="1")?"checked":""); ?>  />
					Show on Registration Page
					
					<input id="mm-include-on-reg" type="hidden" />
				</div>
			</td>
		</tr>
		<tr id='mm-description-row' <?php echo (($mt->getIncludeOnReg()!="1")?"style='display:none;'":""); ?>>
			<td></td>
			<td>
				Registration Page Description
				<textarea id="mm-description" name='description' class="long-text" style="font-size: 11px"><?php echo $mt->getDescription(); ?></textarea>
			</td>
		</tr>
		
		<tr>
			<td colspan="2">
			<div style="width: 600px; margin-top: 8px;" class="mm-divider"></div>
			</td>
		</tr>
		<tr>
			<td>Welcome Email</td>
			<td>
				<div>
				<input type='checkbox' id='mm-welcome-email-enabled-field' <?php echo $welcomeEmailChecked; ?> onchange="mmjs.welcomeEmailChanged()" /> Send welcome email to new members
			<input type='hidden' id='mm-welcome-email-enabled' value='<?php echo $mt->getWelcomeEmailEnabled(); ?>' />
				</div>
				<div style='clear:both;'>&nbsp;</div>
				<div  id='mm-welcome-email-row'>
					<div>
						From
						<select id="mm-email-from" class="medium-text">
						<?php echo MM_HtmlUtils::getEmailAccounts($mt->getEmailFromId(), true); ?>
						</select>
					</div>
				
					<div style="margin-top:5px">
						Subject
						<input id="mm-email-subject" type="text" class="medium-text" value="<?php echo $mt->getEmailSubject(); ?>"/>
					</div>
					
					<div style="margin-top:5px">
						Body <?php echo MM_SmartTagLibraryView::smartTagLibraryButtons("mm-email-body"); ?>
					</div>
					
					<div style="margin-top:5px">
						<textarea id='mm-email-body' class='long-text' rows="6" style="font-size: 11px;"><?php echo $mt->getEmailBody(); ?></textarea>
					</div>
				</div>
				<input id='id' type='hidden' value='<?php if($mt->getId() != 0) { echo $mt->getId(); } ?>' />
			</td>
		</tr>
		
		<tr>
			<td colspan="2">
			<div style="width: 600px; margin-top: 8px;" class="mm-divider"></div>
			</td>
		</tr>
		
		<tr>
			<td>Access Tags</td>
			<td>
				<select id="mm-access-tags[]" size="3" multiple="multiple">
				<?php echo MM_HtmlUtils::getAccessTagsList($mt->getAccessTags()); ?>
				</select>
			</td>
		</tr>
	
		<tr>
			<td>Downgrades To</td>
			<td>
				<select id="mm-downgrade-to">
					<option value="">NONE</option>
					<?php echo MM_HtmlUtils::getMemberTypesList($mt->getDowngradeId()); ?>
				</select>
			</td>
		</tr>

		<tr>
			<td>Upgrades To</td>
			<td>
				<select id="mm-upgrade-to">	
					<option value="">NONE</option>
					<?php echo MM_HtmlUtils::getMemberTypesList($mt->getUpgradeId()); ?>
				</select>
			</td>
		</tr>
		
		<tr>
			<td>Member Type Badge</td>
			<td>
				<?php 
					$badgeUrl = $mt->getBadgeUrl();
					$showUploadForm = !isset($badgeUrl) || (isset($badgeUrl) && $badgeUrl=="");
					
					if($showUploadForm) {
						$badgeUrl = "";
					}
				?>
				<div id="mm-badge-container" <?php if($showUploadForm) { echo "style='display:none;'"; } ?>>
					<img id="mm-badge" src="<?php echo $badgeUrl; ?>" class="badge l" />
					&nbsp;&nbsp;<a onclick="mmjs.clearBadge()" class="button-secondary">Clear</a>
				</div>
				
				<div id="mm-file-upload-container" <?php if(!$showUploadForm) { echo "style='display:none;'"; } ?>>
					<form action="admin-ajax.php" name='badge-upload' method="post" enctype="multipart/form-data" target="upload_target" onsubmit="mmjs.startUpload();" >
	                  	<input id="fileToUpload" name="fileToUpload" type="file" size="30" />
	                  	<input type="submit" name="submitBtn" class="button-secondary" value="Upload" />
	
	                    <input type='hidden' name='method' value='uploadBadge' />
	                    <input type='hidden' name='module' value='MM_MemberTypesView' />
	                    <input type='hidden' name='action' value='module-handle' />
	                    <iframe id="upload_target" name="upload_target" style="width:0;height:0;border:0px solid #fff;"></iframe>
	                </form>
                </div>
			</td>	
		</tr>	
	</table>
	
	<input id='mm-is-default' type='hidden' value='<?php echo $mt->isDefault(); ?>' />
	
	<script type='text/javascript'>
	mmjs.welcomeEmailChanged();
	</script>
</div>