<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
?>
<tr>
	<td style="font-size: 11px">
		<img src='<?php echo $p->type_icon; ?>' style="vertical-align: middle;" />
		<?php echo $p->access_name; ?> on day <?php echo $p->days; ?>
		<input type='hidden' id='has_access_rigths' value='1' />
	</td>
	<td align="right">
		<a href='#' onclick="accessrights_js.edit('mm-post-meta-dialog','<?php echo $p->access_id; ?>','<?php echo $p->access_type; ?>')"><img style="vertical-align: middle;" src='<?php echo $p->edit_icon; ?>' /></a>
		<a href='#'  onclick="accessrights_js.remove('<?php echo $p->access_id; ?>','<?php echo $p->access_type; ?>')"><img style="vertical-align: middle;" src='<?php echo $p->delete_icon; ?>' /></a>
	</td>
</tr>
		