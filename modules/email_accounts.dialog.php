<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
	$email = new MM_EmailAccount($p->id);
	global $current_user;
	$disableRole = "";
	if($current_user->ID == $email->getUserId()){
		$disableRole = "disabled='disabled'";
	}
?>
<div id="mm-form-container">
	<div id="mm-messages-container"></div>
	
	<table cellspacing="10">
		<tr>
			<td>Display Name</td>
			<td><input id="mm-display-name" type="text" class="medium-text" value='<?php echo $email->getName(); ?>'/></td>
		</tr>
		<tr>
			<td>Name</td>
			<td><input id="mm-name" type="text" class="medium-text" value='<?php echo $email->getName(); ?>'  /></td>
		</tr>
		<tr>
			<td>Email</td>
			<td><input id="mm-email" type="text" class="medium-text" value='<?php echo $email->getAddress(); ?>' <?php echo ($email->getId() != 0) ? "disabled":""; ?>/></td>
		</tr>
		<tr>
			<td>Username</td>
			<td><input id="mm-username" type="text" class="medium-text" value='<?php echo $email->getUsername(); ?>' /></td>
		</tr>
		<tr>
			<td>Password</td>
			<td><input id="mm-password" type="password" class="medium-text" value='' /></td>
		</tr>
		<tr>
			<td>Phone</td>
			<td><input id="mm-phone" type="text" class="medium-text" value='<?php echo $email->getPhone(); ?>' /></td>
		</tr>
		<tr>
			<td>Role</td>
			<td>
				<select id='mm-role-id' <?php echo $disableRole; ?>>
				<?php 
				echo MM_HtmlUtils::generateSelectionsList(MM_Role::getRoleList(), $email->getRoleId());
				?>
				</select>
			</td>
		</tr>
	</table>
	
	<input id='id' type='hidden' value='<?php echo $email->getId(); ?>' />
	<input id='mm-status' type='hidden' value='<?php echo $email->getStatus(); ?>' />
	<input id='mm-is-default' type='hidden' value='<?php echo $email->isDefault(); ?>' />
</div>
