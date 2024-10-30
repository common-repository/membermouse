<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

?>
<?php wp_nonce_field('save-mm-corepages','save-mm-corepages-nonce'); ?>
<div id='mm_publish_box'>

	<div id='mm_access_rights_meta'>
		<table width="100%">
			<tr>
				<td><u>Access Rights</u></td>
				<td align="right">
					<input type='button' name='access_rights' value='Grant Access' onclick="accessrights_js.create('mm-post-meta-dialog', 420, 240);" class="button-secondary"  />
				</td>
			</tr>
		</table>
		<table id="mm_access_rights_table"  width="100%" cellspacing="5">
			<?php echo $p->existing_access_rights; ?>
		</table>
	</div>	
	<p />
	<p /><div style='clear:both;height: 10px;'></div>
	<div id='mm_core_pages_meta' style="<?php echo $p->mm_core_pages_meta_style; ?>">
		
		 <span style="font-family: 'Lucida Grande', Verdana, Arial, 'Bitstream Vera Sans', sans-serif; font-size: 13px;"><u>Core Page Settings</u></span>
		<p />
		<table id='access_rights_existing_container'>
			<tr><td>
			<?php if(isset($p->default_icon)){ ?>
				<img id='default_core_page_icon' src='<?php echo $p->default_icon; ?>' style="vertical-align: middle" />
				<input type='hidden' name='save-mm-corepages[core_page_type_id]' value='<?php echo $p->corePageTypeId; ?>' />
			<?php } ?>
			
			<select id='core_page_type_id' name='save-mm-corepages[core_page_type_id]' onchange="corepages_js.getReferences();" <?php if(isset($p->default_icon)){ ?>disabled='disabled' <?php } ?>>
			<option value=''>None</option>
				<?php echo $p->existing_corepage_features; ?>
			</select> 
			
	<?php if(isset($p->default_icon)){ ?>
		<div style='clear:both; height: 10px;'></div>
			<a id='default_core_page' onclick="corepages_js.create('mm-corepage-dialog',420,220);" style="cursor: pointer">Change Default Core Page</a>
		<?php } ?>
			</td></tr>
		</table>
		<?php if(!isset($p->default_icon) || (isset($p->default_icon) && empty($p->default_icon))){?>
		<table width='100%' id='subtypes' ></table>
		<?php }?>
		
		<div id='mm_required_tags'><?php echo $p->requiredTags; ?></div>
	</div>
</div>

<!-- Post Meta Dialog -->
<div id="mm-corepage-dialog"></div>
<div id="mm-post-meta-dialog"></div>

<script>mmJQuery("#mm-post-meta-dialog").dialog({autoOpen: false, buttons: {
	"Grant Access": function() { accessrights_js.save(); },
	"Cancel": function() { accessrights_js.closeDialog(); }}});
</script>

<script>mmJQuery("#mm-corepage-dialog").dialog({autoOpen: false, buttons: {
	"Update Core Page": function() { corepages_js.updateCorePage(); },
	"Cancel": function() { corepages_js.closeDialog(); }}});
</script>
<script type='text/javascript'>
mmJQuery(document).ready(function(){
	<?php if(!isset($p->default_icon)){ ?>
		corepages_js.getReferences('<?php echo $p->is_free; ?>');
		<?php }?>
	corepages_js.checkAccessRights();
});
</script>
