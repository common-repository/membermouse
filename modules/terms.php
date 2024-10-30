<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

$terms = MM_OptionUtils::getOption(MM_OPTION_TERMS_CONTENT);
$status = MM_OptionUtils::getOption(MM_OPTION_TERMS_STATUS);
?>
<div class="wrap">
    <img src="<?php echo MM_Utils::getImageUrl('lrg_signature'); ?>" class="mm-header-icon" /> 
    <h2 class="mm-header-text">Terms and Conditions</h2>
	
	<div id="mm-form-container" style="margin-top: 10px; margin-bottom: 15px;">	
		<div>
			<input id="mm-cb-include-terms-on-reg" type="checkbox" onclick="mmjs.processForm()" <?php echo (($status=="0")?"":"checked"); ?>  />
			New members must accept terms and conditions
			
			<input id="mm-include-terms-on-reg" type="hidden" />
		</div>
		<div style="margin-top:5px">
			<textarea id='mm-terms-and-conditions' cols="100" rows="10" style="font-size: 11px; display:none;"><?php echo $terms; ?></textarea>
		</div>
	</div>
	
	<a onclick="mmjs.save()" class="button-primary">Save Settings</a>
</div>

<script>mmjs.processForm();</script>