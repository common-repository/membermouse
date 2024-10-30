<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
$cpv = new MM_CorePagesView();
$data=  $cpv->getData();
$options = MM_HtmlUtils::generateSelectionsList($data);
?>


<div id='mm_change_core_page_container'>
<table>
	<tr>
			<td>Choose Page to replace existing Core Page</td>
	</tr>
	<tr>
			<td>
				<select id='new_page_id'>
					<?php echo $options; ?>
				</select>
			</td>
	</tr>
	<tr>
			<td>
				<input type='button' onclick="corepages_js.updateCorePage();" value="Change Core Page" class='button-primary' />
				<input type='button' onclick="corepages_js.closeDialog();" value="Cancel" class='button-secondary' />
			</td>
	</tr>
</table>
</div>
