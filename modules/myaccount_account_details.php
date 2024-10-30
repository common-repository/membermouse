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
$readonly = false;
if(MM_Utils::isAdmin()){
	$readonly = true;	
}
$errors = "";
$user = new MM_User($current_user->ID);

$emailAccount = MM_EmailAccount::getDefaultAccount();
$context = new MM_Context($user, $emailAccount);
if(isset($_POST["mm_myaccount_account_first_name"])){
	$user->setFirstName($_POST["mm_myaccount_account_first_name"]);
	$user->setLastName($_POST["mm_myaccount_account_last_name"]);
	$user->setPhone($_POST["mm_myaccount_account_phone"]);
	$user->setEmail($_POST["mm_myaccount_account_email"]);
	
	$passwordChanged = false;
	if(strlen($_POST["mm_myaccount_account_password"])>0 || strlen($_POST["mm_myaccount_account_password_confirm"])>0){
		if($_POST["mm_myaccount_account_password"]==$_POST["mm_myaccount_account_password_confirm"]){
			$user->setPassword($_POST["mm_myaccount_account_password"]);
			$passwordChanged = true;
		}
		else{
			$errors = "Passwords are not the same. ";
		}
	}
	
	if(empty($errors)){
		$memberType = new MM_MemberType($user->getMemberTypeId());
		if(!$memberType->isFree()){
			$response = MM_LimeLightService::updateCustomerInfo($user);
		}
		else{
			$response = new MM_Response();
		}
		if($response instanceof MM_Response){
			if($response->type == MM_Response::$ERROR){
				$errors = $response->message;
			}
			else{
				$response = $user->commitData();
				if($response->type != MM_Response::$SUCCESS){
					$errors = $response->message;
				}
				else{
					if($passwordChanged){
						MM_Session::value(MM_Session::$KEY_UPDATE_USER_ID, $user->getId());
					}
				}
			}
			
			$fields = MM_CustomField::getCustomFieldsList();
			foreach($fields as $id=>$val) {
				$customField = new MM_CustomField($id);
				
				if($customField->isValid()) {
					if($customField->getShowOnMyAccount() == '1') {
						$fieldName = $customField->getFieldLabel();
						$fieldId = $customField->getId();
						if(isset($_POST["mm_myaccount_customfield_".$fieldId])){
							$user->setCustomData($fieldId,$_POST["mm_myaccount_customfield_".$fieldId]);	
						}
					}
			 	}
			}
		}
	}
}

$customFields = array();
$fields = MM_CustomField::getCustomFieldsList();
foreach($fields as $id=>$val) {
	$customField = new MM_CustomField($id);
	
	if($customField->isValid()) {
		if($customField->getShowOnMyAccount() == '1') {
			$fieldName = $customField->getFieldLabel();
			$fieldId = $customField->getFieldName();
			$value = $user->getCustomDataByName($customField->getFieldName());
			if(empty($value)){
				$value = MM_NO_DATA;
			}
			$customFields[] = $customField;
		}
 	}
}
?>
<form method='post'>
<div class='mm-myaccount-error'><?php echo $errors; ?></div>
<table id='mm-subpage-account-details' class='mm-myaccount-details-table'>
	
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan='2'>
				<span class='mm-subpage-title'>Account Details</span>
			</td>
		</tr>
		<tr>
			<td><span class='mm-subpage-labels'>First Name</span></td>
			<td><input <?php echo (($readonly)?"disabled='disabled'":""); ?> name="mm_myaccount_account_first_name" type="text" class="medium-text"  value="<?php echo $user->getFirstName(); ?>"/></td>
		</tr>
		<tr>
			<td><span class='mm-subpage-labels'>Last Name</span></td>
			<td><input <?php echo (($readonly)?"disabled='disabled'":""); ?> name="mm_myaccount_account_last_name" type="text" class="medium-text"  value="<?php echo $user->getLastName(); ?>"/></td>
		</tr>
		<tr>
			<td><span class='mm-subpage-labels'>Phone Number</span></td>
			<td><input <?php echo (($readonly)?"disabled='disabled'":""); ?> name="mm_myaccount_account_phone" type="text" class="medium-text"  value="<?php echo $user->getPhone(); ?>"/></td>
		</tr>
		<tr>
			<td><span class='mm-subpage-labels'>Email</span></td>
			<td><input <?php echo (($readonly)?"disabled='disabled'":""); ?> name="mm_myaccount_account_email" type="text" class="medium-text"  value="<?php echo $user->getEmail(); ?>"/></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<?php if(count($customFields)>0){ ?>
		<tr>
			<td colspan='2'>
				<span class='mm-subpage-title'>Additional Information</span>
			</td>
		</tr>
		<?php 
			foreach($customFields as $customObj){
				$value = $user->getCustomDataByName($customObj->getFieldName());
				?>
					<tr>
						<td><span class='mm-subpage-labels'><?php echo $customObj->getFieldLabel(); ?></span></td>
						<td><input name="mm_myaccount_customfield_<?php echo $customObj->getId(); ?>" type="text" class="medium-text"  value="<?php echo htmlentities(stripslashes($value)); ?>"/></td>
					</tr>
				<?php 	
			}
		?>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<?php } ?>
		<tr>
			<td colspan='2'>
				<span class='mm-subpage-title'>Change Password</span>
			</td>
		</tr>
		<tr>
			<td><span class='mm-subpage-labels'>New Password</span></td>
			<td><input <?php echo (($readonly)?"disabled='disabled'":""); ?> name="mm_myaccount_account_password" type="password" class="medium-text"  value=""/></td>
		</tr>
		<tr>
			<td><span class='mm-subpage-labels'>Confirm Password</span></td>
			<td><input <?php echo (($readonly)?"disabled='disabled'":""); ?> name="mm_myaccount_account_password_confirm" type="password" class="medium-text"  value=""/></td>
		</tr>
		
		
		<?php if(!$readonly){?>
		<tr>
			<td colspan='2'>
				<input type='submit' class="button-secondary"  name='mm-myaccount-submit' name='mm-myaccount-submit' value='Save'     />

			</td>
		</tr>
		<?php } ?>
</table></form>