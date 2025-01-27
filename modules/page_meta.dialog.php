<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

$accessRights = new MM_AccessRightsView();

if(!isset($p->day))
	$p = $accessRights->getData(); 
?>
<div id="mm-access_container_div">
	<div id='mm_container_msg'></div>
	<table>
		<tr>
			<td>Grant access by</td>
			<td>
				<select id="access_rights_choice" name="access_rights_choice" onchange="accessrights_js.showOptions()">
					<?php echo $p->access_rights_choice; ?>
				</select>
			</td>
		</tr>
	</table>
	<div style='clear:both; height: 10px;'></div>	
	<table id='access_rights_container_at_table' style='<?php echo $p->access_rights_at_style; ?>'>
		<tr>
			<td>
				Grant access to the following access tag:
			</td>
		</tr>
		<tr>
			<td>
				<select id='mm_access_tags_opt' ><?php echo ((isset($p->options))?$p->options:""); ?></select>
			</td>
		</tr>
		<tr>
			<td>
				Grant access on day <input type='text' id='at_day' name='at_day' style="width:40px;" value='<?php echo $p->day; ?>' />
			</td>
		</tr>
	</table>
	<table id='access_rights_container_mt_table' style='<?php echo $p->access_rights_mt_style; ?>'>
		<tr>
			<td>
				Grant access to the following member type:
			</td>
		</tr>
		<tr>
			<td>
				<select id='mm_member_types_opt' ><?php echo ((isset($p->options))?$p->options:""); ?></select>
			</td>
		</tr>
		<tr>
			<td>
				Grant access on day <input type='text' name='mt_day' id='mt_day' style="width:40px;" value='<?php echo $p->day; ?>' />
			</td>
		</tr>
	</table>
	
<input type='hidden' id='edit_id' name='edit_id' value='<?php echo ((isset($p->access_id))?$p->access_id:''); ?>' />
</div>
<script type='text/javascript'>
mmJQuery(document).ready(function() {
	<?php if($p->edit!='1') { ?>
    	accessrights_js.showOptions('<?php echo $p->access_id; ?>', '<?php echo $p->access_type; ?>');
    <?php } ?>
});
</script>
