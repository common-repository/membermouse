<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
?>
<div id='mm_change_core_page_container'>
<table>
	<tr>
			<td>Choose Page to replace existing Core Page. You must have pages available that are not already assigned to existing core pages.</td>
	</tr>
	<?php if(!empty($p->options)){ ?>
	<tr><td>&nbsp;</td></tr>
	<tr>
			<td>
				<select id='new_page_id'>
					<?php echo $p->options; ?>
				</select>
			</td>
	</tr>
	<?php }else{ ?>
	<tr><td>&nbsp;</td></tr>
	<tr><td><img src='<?php echo MM_Utils::getImageUrl('error'); ?>' /> You do not have any available pages.  <a href='post-new.php?post_type=page' target='_top'>Click here</a> to add a page.</td></tr>
	<?php } ?>
</table>
</div>