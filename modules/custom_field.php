<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
$view = new MM_CustomFieldView();
$dataGrid = new MM_DataGrid($_REQUEST, "id", "asc", 10);
$data = $view->getData($dataGrid);
$dataGrid->setTotalRecords($data);
$dataGrid->recordName = "custom field";

$rows = array();

$headers = array
(	    
	'id'				=> array('content' => 'ID'),
   	'field_name'				=> array('content' => 'Name'),
   	'field_label'				=> array('content' => 'Label'),
   	'is_required'				=> array('content' => 'Is Required'),
   	'show_on_reg'			=> array('content' => 'Show On Registration'),
   	'show_on_myaccount'			=> array('content' => 'Show On My Account'),
   	'actions'			=> array('content' => 'Actions')
);

foreach($data as $key=>$item)
{	
	if($item->is_required=='1'){
		$item->is_required = "<img src='".MM_Utils::getImageUrl("tick")."' />";
	}
	else{
		$item->is_required = "<img src='".MM_Utils::getImageUrl("cross")."' />";
	}	
	
	if($item->show_on_reg=='1'){
		$item->show_on_reg = "<img src='".MM_Utils::getImageUrl("tick")."' />";
	}
	else{
		$item->show_on_reg = "<img src='".MM_Utils::getImageUrl("cross")."' />";
	}
	
	if($item->show_on_myaccount=='1'){
		$item->show_on_myaccount = "<img src='".MM_Utils::getImageUrl("tick")."' />";
	}
	else{
		$item->show_on_myaccount = "<img src='".MM_Utils::getImageUrl("cross")."' />";
	}
	
    // Actions
	$actions = '<a title="Edit Custom Field" onclick="mmjs.edit(\'mm-custom-fields-dialog\', \''.$item->id.'\', 500,395)" style="margin-left: 5px; cursor:pointer"><img src="'.MM_Utils::getImageUrl("edit").'" /></a>';
	
	$cf = new MM_CustomField($item->id);
	if(!$cf->hasAssociation()){
		$actions .= '<a title="Delete Custom Field" onclick="mmjs.remove(\''.$item->id.'\')" style="margin-left: 5px; cursor:pointer;"><img src="'.MM_Utils::getImageUrl("delete").'" /></div></a>';
	}
    $rows[] = array
    (
    	array('content' => $item->id),
    	array('content' => $item->field_name, 'attr' => 'class="name"'),
    	array('content' => $item->field_label, 'attr' => 'class="name"'),
    	array('content' => $item->is_required, 'attr' => 'class="name"'),
    	array('content' => $item->show_on_reg, 'attr' => 'class="name"'),
    	array('content' => $item->show_on_myaccount, 'attr' => 'class="name"'),
    	array('content' => $actions),
    );
}

$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);

$dgHtml = $dataGrid->generateHtml();

if($dgHtml == "") {
	$dgHtml = "<p><i>No custom fields.</i></p>";
}
?>
<div class="wrap">
    <img src="<?php echo MM_Utils::getImageUrl('lrg_form'); ?>" class="mm-header-icon" /> 
    <h2 class="mm-header-text">Custom Fields</h2>
	<div class="mm-button-container">
		<a onclick="mmjs.create('mm-custom-fields-dialog', 500,395)" class="button-secondary"><img src="<?php echo MM_Utils::getImageUrl('add'); ?>" /> Create Custom Field</a>
	</div>
	
	<div class="clear"></div>
	
	<?php echo $dgHtml; ?>
</div>