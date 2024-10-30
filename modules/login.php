<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
$corePageEngine = new MM_CorePageEngine();
$url = $corePageEngine->getUrl(MM_CorePageType::$FORGOT_PASSWORD, '');
$errors = MM_Messages::get(MM_Session::$KEY_ERRORS);

MM_Messages::clear();

// handle email confirmation
if(isset($_GET[MM_Session::$PARAM_CONFIRMATION_KEY])) 
{
	$email = new MM_EmailAccount();
	$email->setId(base64_decode(urldecode($_GET[MM_Session::$PARAM_CONFIRMATION_KEY])));
	
	$result = $email->confirmEmailAccount();
	
	if($result->type == MM_Response::$SUCCESS) {
		$success = $result->message;
	}
	else {
		$errors = array($result->message);
	}
}
 MM_Session::clear(MM_Session::$KEY_REGISTRATION);
?>
<form action="<?php echo get_option('siteurl'); ?>/wp-login.php" method="post" onsubmit="return login_js.checkFields()">
<div id='mm-errors' style='color:red'><?php echo is_array($errors) ? implode("<br />", $errors) : ""; ?></div>
<div id='mm-success' style='color:green'><?php echo isset($success) ? $success : ""; ?></div>
<table class='mm-login-form'>
	<tr>
		<td width='110px'><span class='mm-login-label'>Username</span></td>
		<td>
			<input type="text" name="log" class='fields' id="log" value="" size="20" />
		</td>
	</tr>
	<tr>
		<td width='110px'><span class='mm-login-label'>Password</span></td>
		<td>
			<input type="password" class='fields' name="pwd" id="pwd" size="20" />
		</td>
	</tr>
	<tr>
		<td colspan='2' align='center'>
			<input type="submit" name="submit" value="Login" class="mm-login-button"  />
		</td>
	</tr>
	<tr>
		<td width='110px' >
		 	<label for="rememberme"><input name="rememberme" id="rememberme" type="checkbox" checked="checked" value="forever" /> Remember me </label>  
		</td>
		<td>
		<a href='<?php echo $url; ?>' style='font-size: 12px;'>Forgot Password</a>
		</td>
	</tr>
</table>
</form>