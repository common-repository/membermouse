<?php

if($p->dialog=="updateAccessRights"){
	?>
	<table width='98%'>
	<tr>
		<td colspan='2'><?php echo $p->type_name; ?> members get access to <?php echo $p->page_name; ?> ... </td>
	</tr>
		<tr>
			<td><input type='radio' id='mm-gar-change' name='mm-gar-change' value='change' checked /> </td>
			<td>
				on day <input type='text' id='mm_gar_day' value='<?php echo $p->day; ?>' style='width: 50px;' />	
				<input type='hidden' id='mm_access_id' value='<?php echo $p->access_id; ?>' />
				<input type='hidden' id='mm_access_type' value='<?php echo $p->access_type; ?>' />
				<input type='hidden' id='mm_post_id' value='<?php echo $p->post_id; ?>' />
			</td>
		</tr>
		<tr>
			<td><input type='radio' id='mm-gar-remove' name='mm-gar-change' value='remove'  /> </td>
			<td>
				Revoke access	
			</td>
		</tr>
	</table>
	
	<?php 
}
else if($p->dialog=="showAddAccessRigths"){
	?>
	<table width='98%'>
		<tr>
			<td colspan='3'>
				Grant <?php echo $p->type_name; ?> members access to ...
			</td>
		</tr>
		<tr><td colspan='3'>&nbsp;</td></tr>
		<?php if(!empty($p->posts_select)){ ?>
		<tr>
			<td><input type='radio' id='mm-gar-page-type-post' name='mm-gar-page-type' value='post' onchange="mmjs.onTypeChange()" checked /> </td>
			<td>Post</td>
			<td>
				<select id='mm-gar-post'>
					<?php 
					echo $p->posts_select;
					?>
				</select>
			</td>
		</tr>
		<tr><td colspan='3'>&nbsp;</td></tr>
		<?php } ?>
		<?php if(!empty($p->pages_select)){ ?>
		<tr>
			<td><input type='radio' id='mm-gar-page-type-page' name='mm-gar-page-type' value='page' onchange="mmjs.onTypeChange()" /> </td>
			<td>Page</td>
			<td>
				<select id='mm-gar-page'>
					<?php 
					echo $p->pages_select;
					?>
				</select>
			</td>
		</tr>
		<tr><td colspan='3'>&nbsp;</td></tr>
		<?php } ?>
		<tr>
			<td colspan='3'>
				... on day <input type='text' id='mm-gar-day' value=''  style='width: 50px;' />
				<input type='hidden' id='mm_access_id' value='<?php echo $p->id; ?>' />
				<input type='hidden' id='mm_access_type' value='<?php echo $p->type; ?>' />
			</td>
		</tr>
	</table>
	<?php 
}
else{
?>
No dialog found.
<?php } ?>
<script type='text/javascript'>
mmjs.onTypeChange();
</script>