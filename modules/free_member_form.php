<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */ 
function createTextField($name, $type = "text", $value=""){
	return "<input type='{$type}' name='{$name}' value='{$value}' />";
}

function createRow($label, $field, $colspan=""){
	if(intval($colspan)==2){
		return "<tr>
	<td colspan='2'>{$field}</td>
</tr>\n";
	}
	return "<tr>
	<td>{$label}</td>
	<td>{$field}</td>
</tr>\n";
}

function generateRows($fields, $requirePost=false){
	$generatedHtml = "";
	foreach($fields as $field=>$typeArr){
		$type = $typeArr['type'];
		$title = $typeArr['title'];
		if(!$requirePost || ($requirePost && isset($_POST[$field]))){
			$generatedHtml.=createRow($title, createTextField($field, $type));
		}
	}
	return $generatedHtml;
}

$requiredCustomFields = array();
$optionalCustomFields = array();
$fields = MM_CustomField::getCustomFieldsList();
foreach($fields as $id=>$val){
	$customField = new MM_CustomField($id);
	if($customField->isValid()){
		if($customField->getShowOnReg()=='1'){
			$fieldName = $customField->getFieldName();
			if($customField->getRequired() == '1'){
				$requiredCustomFields[] = $customField;
			}
			else{
				$optionalCustomFields[] = $customField;
			}
		}
 	}
}

$memberTypes = MM_MemberType::getMemberTypesList(true, MM_MemberType::$SUB_TYPE_FREE);
if(empty($memberTypes)){
	?>
	Please <a href='admin.php?page=mm_configure_site'>create a free member type</a> to use this integration tool. 
	<?php
	exit; 
}
$postedMemberTypeId = (isset($_POST["member_type"]))?$_POST["member_type"]:0;
$memberTypeOptions = MM_HtmlUtils::generateSelectionsList($memberTypes,$postedMemberTypeId);

$generatedHtml = "";
if(isset($_POST["member_type"]))
{
	$hiddenFields = array(
		'member_type'
	);
	
	$reqFields = array(
		'email'=>array('type'=>'text', 'title'=>'Email'),
	);
	
	//required custom fields

	if(count($requiredCustomFields)>0){
		foreach($requiredCustomFields as $customField){
			$fieldName =$customField->getFieldName();
			$reqFields[$fieldName] = array('type'=>"text", 'title'=>$customField->getFieldLabel());	
		}
	}
	
	$optFields = array(
		'first_name'=>array('type'=>'text', 'title'=>'First Name'),
		'last_name'=>array('type'=>'text', 'title'=>'Last Name'),
		'username'=>array('type'=>'text', 'title'=>'Username'),
		'password'=>array('type'=>'password', 'title'=>'Password'),
	);
	
	//custom fields
	if(count($optionalCustomFields)>0){
		foreach($optionalCustomFields as $customField){
			$fieldName =$customField->getFieldName();
			$optFields[$fieldName] = array('type'=>"text", 'title'=>$customField->getFieldLabel());	
		}
	}
	
	$generatedHtml = "<form method='post' action='".MM_API_BASE_URL."/webform.php'>\n";
	$generatedHtml .= "<table>\n";
	$generatedHtml .= generateRows($reqFields);
	$generatedHtml .= generateRows($optFields, true);
	
	// get buttons and hidden fields
	$submitField = createTextField("Submit", "submit", "Submit");
	$hiddenFieldsHtml = "";
	foreach($hiddenFields as $field){
		$value = (isset($_POST[$field]))?$_POST[$field]:"";
		$hiddenFieldsHtml.= createTextField($field, "hidden", $value);
	}
	$generatedHtml.= createRow("", "\n\t".$submitField."\n\t".$hiddenFieldsHtml."\n\t",2);
	$generatedHtml.="</table>\n</form>";
}

?>
	<form method='post'>
<div class="wrap">
	<div style='padding-left: 10px;margin-top:10px;'>
    	<img src="<?php echo MM_Utils::getImageUrl('lrg_form'); ?>" class="mm-header-icon" /> 
	    <h2 class="mm-header-text">Free Member Web Form</h2>
	    <div style='clear:both; height: 10px;'></div>
	    <div style='width:650px'>
On this page you can build a web form that can be included on any site. It allows prospects to sign up for a free membership on your site. To create the form, just select the free member type you want prospects to sign up as, then check off the fields that you want to include in the form and click <i>Generate HTML</i>.
<br /><br />
The HTML for your web form will show up in the text box on the right. Just copy and paste this code to your site.
</div>
	</div>
	<div style='padding-left: 10px;margin-top:15px;float:left;width: 350px; '>
	
	<table >
		<tr>
			<td width='145px'><span style='color: red;'></span> Member Type</td>
			<td>
				<select name='member_type'>
				<?php 
					echo $memberTypeOptions;
				?>
				</select>
			</td>
		</tr>
	</table>
    <h2 class="mm-header-text">Form Elements</h2>
	
	<div style='margin-top:10px;'></div>
	<table >
		<tr>
			<td width='145px'><span style='color: red;'>*</span> Email</td>
			<td>
				<input type='checkbox' name='email' value='1' checked disabled='disabled'/>
			</td>
		</tr>
		<?php 
			if(count($requiredCustomFields)>0){
				foreach($requiredCustomFields as $customField){
					echo "
						<tr>
							<td><span style='color: red;'>*</span> ".$customField->getFieldLabel()."</td>
							<td>
								<input type='checkbox' name='".$customField->getFieldName()."' value='1' checked disabled='disabled' /> 
							</td>
						</tr>";
				}	
			}
		?>
		<tr>
			<td>First Name</td>
			<td>
				<input type='checkbox' name='first_name' value='1' <?php echo ((isset($_POST["first_name"]))?"checked":""); ?>/> 
			</td>
		</tr>
		<tr>
			<td>Last Name</td>
			<td>
				<input type='checkbox' name='last_name' value='1' <?php echo ((isset($_POST["last_name"]))?"checked":""); ?>/> 
			</td>
		</tr>
		<tr>
			<td>Username</td>
			<td>
				<input type='checkbox' name='username' value='1' <?php echo ((isset($_POST["username"]))?"checked":""); ?>/> 
			</td>
		</tr>
		<tr>
			<td>Password</td>
			<td>
				<input type='checkbox' name='password' value='1' <?php echo ((isset($_POST["password"]))?"checked":""); ?>/> 
			</td>
		</tr>
		<?php 
			if(count($optionalCustomFields)>0){
				foreach($optionalCustomFields as $customField){
					$fieldName = $customField->getFieldName();
					$checked = ((isset($_POST[$fieldName]))?"checked":"");
					echo "
						<tr>
							<td>".$customField->getFieldLabel()."</td>
							<td>
								<input type='checkbox' name='".$fieldName."' value='1' {$checked}/> 
							</td>
						</tr>";
				}	
			}
		?>
		<tr>
			<td colspan='2'>
			&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan='2'><input type='submit' name='submit' value='Generate HTML'  class="button-secondary" /></td>
		</tr>
	
	</table>
	</div>
	<div style='padding-top: 15px; padding-left: 20px;float:left; '>
		<textarea rows='18' cols='60' style='font-family: Courier New '><?php echo $generatedHtml; ?></textarea>
	</div>
</div>
	</form>
