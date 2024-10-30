<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
?>
<div class="wrap">
    <img src="<?php echo MM_Utils::getImageUrl('lrg_tools'); ?>" class="mm-header-icon" /> 
    <h2 class="mm-header-text">Uninstall MemberMouse</h2>
	
	<div class="mm-button-container">
		<a onclick="mmjs.uninstall()" class="button-secondary"><img src="<?php echo MM_Utils::getImageUrl('error'); ?>" /> Uninstall MemberMouse</a>
	</div>
</div>

<style>
.ui-progressbar-value { background-image: url('<?php echo MM_IMAGES_URL."pbar-animated.gif" ?>'); }
</style>
<div id="mm-progressbar-container" style="display:none;" >
	<div id="mm-progressbar" style="width:150px"></div>
	<script>
	mmJQuery(function() {
		mmJQuery("#mm-progressbar").progressbar({
			value: 100
		});
	});
	</script>
</div>