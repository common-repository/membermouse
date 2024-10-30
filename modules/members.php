<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
	$view = new MM_MembersView();
?>
<div class="wrap">
    <img src="<?php echo MM_Utils::getImageUrl('lrg_directory'); ?>" class="mm-header-icon" /> 
    <h2 class="mm-header-text">Manage Members</h2>
	
	<?php if(count(MM_MemberType::getMemberTypesList()) > 0) { ?>
		<div class="mm-button-container">
			<a onclick="mmjs.create('mm-create-member-dialog')" class="button-secondary"><img src="<?php echo MM_Utils::getImageUrl('user_add'); ?>" /> Create Member</a>
			<a onclick="mmjs.import()" class="button-secondary"><img src="<?php echo MM_Utils::getImageUrl('user_go'); ?>" /> Import Members</a>
		</div>
	<?php } ?>
	
	<?php echo $view->generateSearchForm($_POST); ?>

	<div style="width: 100%; margin-top: 10px; margin-bottom: 10px;" class="mm-divider"></div> 
	
	<div id="mm-grid-container">
		<?php echo $view->generateDataGrid($_POST); ?>
	</div>
</div>
<script type='text/javascript'>
</script>