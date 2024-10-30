<?php 

if(isset($_REQUEST[MM_Session::$PARAM_USER_ID])) {
	global $mmSite;
	
	$user = new MM_User($_REQUEST[MM_Session::$PARAM_USER_ID]);
	
	if($user->isValid()) {
		$error = "";
		$success = "";
		include_once MM_MODULES."/details.header.php";
		
		if(isset($_POST["custom_submit"])){
			foreach($_POST as $k=>$v){
				if(preg_match("/(mm_custom_)/", $k)){
					$fieldId = preg_replace("/[^0-9]+/", "", $k);
					$response = $user->setCustomData($fieldId, $v);
					if($response->type == MM_Response::$ERROR){
						if(!empty($v)){
							$error = $response->message;
						}
					}
				}
			}
			if(empty($error)){
				$success = "Update success.";
			}
		}
		
		$fields = MM_CustomField::getCustomFields($user->getId());
		
?>
<form name='mm_custom_post' method='post'>
<div id="mm-form-container">
<div style='height: 10px;clear:both;'></div>
	<table>
	<?php 
		foreach($fields as $field){
		?>
			<tr>
				<td width='120px'>
					<?php echo $field->field_label; ?>
				</td>
				<td>
					<input type='text' style='width: 250px;' name='mm_custom_<?php echo $field->id; ?>' value="<?php echo htmlentities($field->value); ?>" />
				</td>
			</tr>
		<?php 	
		}
	?>
	<tr>
		<td colspan='2'>
			<input type="submit" name='custom_submit' class="button-primary" value="Update Custom Fields" >		
		</td>
	</tr>

	</table>
</div></form>
<?php 
	}
}
?>
<script type='text/javascript'>
<?php if(!empty($error)){ ?>
	alert('<?php echo $error; ?>');
<?php } ?>
</script>
