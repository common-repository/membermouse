<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */


?>
<div style='clear:both; height: 20px;'></div>
Provider IPN: 
<select id="provider" name="provider" onchange="mmjs.chooseProvider()" >
	<option value="">Choose</option>
	<option value="paypal">PayPal</option>
	<option value="clickbank">ClickBank</option>
</select>
<select id='method' name='method' onchange="mmjs.chooseForm();">
	<option value="">Select a Provider</option>

</select>
<input class='button-secondary' type='button' value='Reset' name='change_api'  onclick="mmjs.chooseForm();" />
<div style='clear:both; height:20px;'></div>
<div style='float:left;'>
	<div id='mm-api-test' ></div>
</div>
<div style='float:left; margin-left: 20px;'>
	<div id='mm-api-test-sent'></div>
	<div id='mm-api-test-response'></div>
</div>