<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
global $current_user;
if(!isset($_SESSION)){
	session_start();
}
$user = new MM_User($current_user->ID);
if(is_admin() && isset($userId)){
	$user = new MM_User($userId);
}

$readonly = true;
if($user->getStatus() == MM_MemberStatus::$ACTIVE || $user->getStatus() == MM_MemberStatus::$OVERDUE){ 
	
	$readonly = false;
}

$errors = "";
if(isset($_POST["mm_myaccount_shipping_address"])){
	$user->setShippingAddress($_POST["mm_myaccount_shipping_address"]);
	$user->setShippingCity($_POST["mm_myaccount_shipping_city"]);
	$user->setShippingState($_POST["mm_myaccount_shipping_state"]);
	$user->setShippingZipCode($_POST["mm_myaccount_shipping_zip"]);
	
	$memberType = new MM_MemberType($user->getMemberTypeId());
	$response = new MM_Response();
	if(!$memberType->isFree()){
		$user->setShippingCountry($_POST["mm_myaccount_shipping_country"]);
		if(MM_OptionUtils::getOption("mm-install_type") == MM_Site::$INSTALL_TYPE_LIMELIGHT){
			$response = MM_LimeLightService::updateShippingInfo($user);
		}
		else{
			$user->setShippingCountry($_POST["mm_myaccount_shipping_country"]);
			$user->doUpdateLL = false;
		}
	}
	if($response instanceof MM_Response){
		if($response->type == MM_Response::$ERROR){
			$errors = $response->message;
		}
		else{
			$response = $user->commitData();
			
			if($response->type == MM_Response::$ERROR){
				$errors = $response->message;
			}
		}
	}
	
}

if(MM_Utils::isAdmin() ){
	$readonly = true;	
}

$campaignId =0;
$memberTypeId = $user->getMemberTypeId();
$memberType = new MM_MemberType($memberTypeId);
$showCountry =false;
if(!$memberType->isFree()){
$showCountry =true;
	$productId = $memberType->getRegistrationProduct();
	$product = new MM_Product($productId);
	$campaignId = $product->getCampaignId();
}
$campaignListHtml = MM_HtmlUtils::getCampaignCountryList($campaignId, $user->getShippingCountry());

if(empty($campaignListHtml)){
	$options = MM_CampaignOptions::getOptions("country");
	$campaignListHtml = MM_HtmlUtils::generateSelectionsList($options, $user->getShippingCountry());
}

if(MM_Utils::isAdmin() && is_admin()){
	$readonly= false;
}
?>
<div id="mm-form-container">
<form method='post'>
<div class='mm-myaccount-error'><?php echo $errors; ?></div>
<table cellspacing="8" class='mm-myaccount-details-table' id='mm-subpage-shipping-details'>
	
		<tr>
			<td width='140'><span class='mm-subpage-labels'>Address</span></td>
			<td><input <?php echo (($readonly)?"disabled='disabled'":""); ?> name="mm_myaccount_shipping_address" type="text" class="medium-text"  value="<?php echo $user->getShippingAddress(); ?>"/></td>
		</tr>
		<tr>
			<td width='120'><span class='mm-subpage-labels'>City</span></td>
			<td><input <?php echo (($readonly)?"disabled='disabled'":""); ?> name="mm_myaccount_shipping_city" type="text" class="medium-text"  value="<?php echo $user->getShippingCity(); ?>"/></td>
		</tr>
		<tr>
			<td><span class='mm-subpage-labels'>State</span></td>
			<td><input <?php echo (($readonly)?"disabled='disabled'":""); ?> name="mm_myaccount_shipping_state" type="text" class="medium-text"  value="<?php echo $user->getShippingState(); ?>"/></td>
		</tr>
		<tr>
			<td><span class='mm-subpage-labels'>Zip Code</span></td>
			<td><input <?php echo (($readonly)?"disabled='disabled'":""); ?> name="mm_myaccount_shipping_zip" type="text" class="medium-text"  value="<?php echo $user->getShippingZipCode(); ?>"/></td>
		</tr>
		<?php 
		if($showCountry){
		?>
		<tr>
			<td><span class='mm-subpage-labels'>Country</span></td>
			<td><select <?php echo (($readonly)?"disabled='disabled'":""); ?> name="mm_myaccount_shipping_country"><?php echo $campaignListHtml ?></select></td>
		</tr>
		<?php } ?>
		<?php if(!$readonly){?>
		<tr>
			<td colspan='2'>
				<?php if(!is_admin()){?>
					<input type='submit' class="button-secondary"  name='mm-myaccount-submit' name='mm-myaccount-submit' value='Save'     />
				<?php }else{ 
				?>
					<input type='submit' class="button-primary"  name='mm-myaccount-submit' name='mm-myaccount-submit' value='Save Shipping Information' style='width: 155px;'    />
				<?php 
				}?>

			</td>
		</tr>
		<?php } ?>
</table></form>
</div>
