<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */


$view = new MM_CampaignSettingsView();
$dataGrid = new MM_DataGrid($_REQUEST, "id", "asc", 10);
$data = $view->getData($dataGrid);
$dataGrid->setTotalRecords($data);
$dataGrid->recordName = "shipping";

MM_Session::value(MM_Session::$KEY_CAMPAIGN_SETTINGS_ID, MM_MODULE_SHIPPING);
$rows = array();

$headers = array
(	    
	'id'				=> array('content' => 'ID'),
   	'name'				=> array('content' => 'Name'),
   	'attr'				=> array('content' => 'Rate'),
   	'actions'			=> array('content' => 'Actions')
);

foreach($data as $key=>$item)
{	
	
    // Actions
	$actions = '<a title="Edit Shipping" onclick="mmjs.edit(\'mm-shipping-dialog\', {id:'.$item->id.',mm_setting_type:\'shipping\'}, 300,195)" style="margin-left: 5px; cursor:pointer"><img src="'.MM_Utils::getImageUrl("edit").'" /></a>';
	$actions .= '<a title="Delete Shipping" onclick="mmjs.remove(\''.$item->id.'\')" style="margin-left: 5px; cursor:pointer;"><img src="'.MM_Utils::getImageUrl("delete").'" /></div></a>';
    $attr = "";
    if(floatval($item->attr)>0){
    	$attr ="\$".$item->attr;
    }
    else{
    	$attr = MM_NO_DATA;
    }
	
	$rows[] = array
    (
    	array('content' => $item->id),
    	array('content' => $item->name, 'attr' => 'class="name"'),
    	array('content' => $attr, 'attr' => 'class="name"'),
    	array('content' => $actions),
    );
}

$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);

$dgHtml = $dataGrid->generateHtml();

if($dgHtml == "") {
	$dgHtml = "<p><i>No shipping.</i></p>";
}
?>
<div class="wrap">
    <h2 class="mm-header-text">Shipping Methods</h2>
	<div class="mm-button-container">
		<a onclick="mmjs.create('mm-shipping-dialog', 300,195)" class="button-secondary"><img src="<?php echo MM_Utils::getImageUrl('add'); ?>" /> Create Shipping Method</a>
	</div>
	
	<div class="clear"></div>
	
	<?php echo $dgHtml; ?>
</div>