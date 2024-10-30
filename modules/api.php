<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
$view = new MM_ApiView();
$dataGrid = new MM_DataGrid($_REQUEST, "id", "asc", 10);
$data = $view->getData($dataGrid);
$dataGrid->setTotalRecords($data);
$dataGrid->recordName = "API Key";

$rows = array();

$headers = array
(	    
	'id'				=> array('content' => 'ID'),
   	'name'				=> array('content' => 'Name'),
   	'api_key'				=> array('content' => 'Key'),
   	'api_secret'				=> array('content' => 'Password'),
   	'status'			=> array('content' => 'Status'),
   	'actions'			=> array('content' => 'Actions')
);

foreach($data as $key=>$item)
{	
    // Actions
	$actions = '<a title="Edit API Set" onclick="mmjs.edit(\'mm-api-keys-dialog\', \''.$item->id.'\', 500, 280)" style="margin-left: 5px; cursor:pointer"><img src="'.MM_Utils::getImageUrl("edit").'" /></a>';
	$actions .= '<a title="Delete API Set" onclick="mmjs.remove(\''.$item->id.'\')" style="margin-left: 5px; cursor:pointer;"><img src="'.MM_Utils::getImageUrl("delete").'" /></div></a>';
    $rows[] = array
    (
    	array('content' => $item->id),
    	array('content' => $item->name, 'attr' => 'class="name"'),
    	array('content' => $item->api_key, 'attr' => 'class="name"'),
    	array('content' => $item->api_secret, 'attr' => 'class="name"'),
    	array('content' => MM_Utils::getStatusImage($item->status)),
    	array('content' => $actions),
    );
}

$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);

$dgHtml = $dataGrid->generateHtml();

if($dgHtml == "") {
	$dgHtml = "<p><i>No API sets found.</i></p>";
}
?>
<div class="wrap">
    <img src="<?php echo MM_Utils::getImageUrl('lrg_disk'); ?>" class="mm-header-icon"   /> 
    <h2 class="mm-header-text">API Settings</h2>
	
	<div style="margin-bottom: 8px">
		<table cellspacing="10">
			<tr>
				<td width="80">API URL</td>
				<td><input type="text" value="<?php echo MM_API_URL; ?>" style="width:550px" /></td>
			</tr>
		</table>
	</div>
	
	<a onclick="mmjs.create('mm-api-keys-dialog', 500,280)" class="button-secondary"><img src="<?php echo MM_Utils::getImageUrl('add'); ?>" /> Create API Key</a>
	
	<div class="clear"></div>
	
	<?php echo $dgHtml; ?>
</div>