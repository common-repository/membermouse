<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

if(isset($_POST["overdue_notices"])){
	MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_EVENT_HANDLING_OVERDUE,$_POST["overdue_notices"]);
}
if(isset($_POST["cancel_method"])){
	MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_EVENT_HANDLING_CANCEL,$_POST["cancel_method"]);
}

$cancelChecked = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_EVENT_HANDLING_CANCEL);
$overdueChecked = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_EVENT_HANDLING_OVERDUE);

?>
<form method='post' name='notices'>
<div class="wrap" style='width: 600px;'>
    <img src="<?php echo MM_Utils::getImageUrl('lrg_disk'); ?>" class="mm-header-icon"   /> 
    <h2 class="mm-header-text">Event Handling</h2>
	<div style='clear:both; height: 10px;'></div>
	<h3>Overdue Payment Handling</h3>
When a member's card is declined, MemberMouse will be informed and will perform one of the following two actions:<br /><br />
	<input type='radio' value='<?php echo MM_EventHandlingStatus::$OVERDUE_CHANGE; ?>' id='overdue_notices' name='overdue_notices'  <?php echo (($overdueChecked!="send_email_reminder")?"checked":""); ?> /> Change status of member's account to overdue and send them an email requesting that they update their billing information. Note that when a member's account is in overdue status, they can login but can only access unprotected content and their My Account page. All protected content will be unavailable.
<br /><br />
	<input type='radio' value='<?php echo MM_EventHandlingStatus::$OVERDUE_EMAIL; ?>' id='overdue_notices' name='overdue_notices'  <?php echo (($overdueChecked=="send_email_reminder")?"checked":""); ?> /> Send member an email requesting that they update their billing information.
	
	<div style='clear:both; height: 10px;'></div>
	<h3>Cancellation Method</h3>
MemberMouse supports two different cancellation methods: a hard cancel and pause. With a hard cancel, the member won't be able to log in at all. With a pause, the member will be able to log in and access all the protected content they had access to at the time their account was paused. With the paused status, their content delivery schedule won't progress so they won't get access to any additional content unless they reactivate their account which they can do by going to their My Account page.
<br /><br />
There are 3 ways a member's account can be canceled:
<br /><br />
<ol>
<li>By the member themselves through the [MM_CancelMembership] or [MM_PauseMembership] SmartTags</li>
<li>By you through the Member Details > Manage Access Rights page by clicking Cancel Membership or Pause Membership</li>
<li>By MemberMouse, when responding to an API call from an event that occurs in Lime Light (i.e. stop recurring, void or refund)</li>
</ol><br />
It's the third case that you need to tell MemberMouse which method to use.
<br /><br />
Which cancellation method do you want to use?
<br /><br />
<input type='radio' value='<?php echo MM_EventHandlingStatus::$CANCEL_HARD; ?>' id='cancel_method' name='cancel_method' <?php echo (($cancelChecked!="pause")?"checked":""); ?> /> Hard Cancel
<br /><br />
<input type='radio' value='<?php echo MM_EventHandlingStatus::$CANCEL_PAUSE; ?>' id='cancel_method' name='cancel_method' <?php echo (($cancelChecked=="pause")?"checked":""); ?>  /> Pause
<br /><br />
	<input type='submit' name='submit' value='Save Event(s)' class="button-primary" />
</div>
</form>