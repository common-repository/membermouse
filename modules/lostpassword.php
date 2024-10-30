<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
$errors= "";
$success = "";
if(isset($_POST["user_login"]))
{
	$member = get_user_by_email(trim($_POST['user_login']));
	if(isset($member->ID) && intval($member->ID)>0)
	{
		$password=  MM_Utils::createRandomString();
		$user = new MM_User($member->ID);
		$user->setPassword($password);
		$user->commitData();
		
		wp_set_password($password, $member->user_login);	
		$email = new MM_Email();
		$emailAccount = MM_EmailAccount::getDefaultAccount();
		$context = new MM_Context($user, $emailAccount);
		
		$email->setContext($context);
		$email->setSubject("Your account password has been reset.");
		$email->setBody("Your new password: [MM_Member_Password]");
		
		$name = (!empty($member->mm_first_name))?$member->mm_first_name:$member->display_name;
		$email->setToName($name);
		$email->setToAddress($member->user_email);
		$email->setFromName($emailAccount->getName());
		$email->setFromAddress($emailAccount->getAddress());
		$response = $email->send();
		
		if($response->type == MM_Response::$ERROR)
		{
			$error = "Unable to send your password : ".$response->message;
		}
		else
		{
			$success = "Your password has been sent to your account email.";	
		}
	}
	else
		$errors = "Could not find user.";
}
?>
<form method="post">
<table>
<?php if($errors){ ?>
<tr>
	<td colspan='2' align='center'><?php echo $errors; ?></td>
</tr>
<?php } ?>
<?php if(!empty($success)){ ?>
<tr><td colspan='2' align='center'><?php echo $success; ?></td></tr>
<?php }else{?>
	<tr>
		<td>
			<input type="text" name="user_login" id="user_login" value="" size="20" />
		</td>
	</tr>
	<tr>
		<td colspan='2' align='center'>
			<input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="Get New Password" tabindex="100" />
		</td>
	</tr>
	<?php } ?>
</table>
</form>