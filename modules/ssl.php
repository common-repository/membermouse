<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
$checked = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_SSL);
$checkedAdmin = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_SSL_ADMIN);
?>
<form name='ssl' method='post' onsubmit="mmjs.confirmSSLChoice();">
<div class="wrap">
    <img src="<?php echo MM_Utils::getImageUrl('lrg_tools'); ?>" class="mm-header-icon" /> 
    <h2 class="mm-header-text">SSL Settings</h2>
	
	<div id="mm-form-container" style="margin-top: 10px; margin-bottom: 15px;">	
		<div>
			<input id="mm_use_ssl" name="mm_use_ssl" type="checkbox" <?php echo (($checked!="1")?"":"checked"); ?> onchange="mmjs.showAdminSSLOption();" />
			I have an SSL Certificate installed on this domain and would like use it on my site
			<div id='mm-ssl-options' style="padding-top:5px; padding-left:25px;"></div>
		</div>
	</div>
	<input type='button' name='save_ssl' value='Save Settings' onclick="mmjs.confirmSSLChoice();" class="button-primary" />
</div>
</form>
<script type="text/javascript">
<?php if($checkedAdmin=='1'){ ?>
mmjs.showAdminSSLOption(true);
<?php }else if($checked=='1'){ ?>
mmjs.showAdminSSLOption(false);
<?php } ?>
</script>