<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

$view = new MM_EmailAccountsView();
$dataGrid = new MM_DataGrid($_REQUEST, "name", "asc", 10);
$data = $view->getData($dataGrid);
$dataGrid->setTotalRecords($data);
$dataGrid->recordName = "email account";

$rows = array();

$headers = array
(	    
	'id'		=> array('content' => '<a onclick="mmjs.sort(\'id\');" href="#">ID</a> ', 'attr' => ''),
	'default'	=> array('content' => 'Default Account'),
   	'name'		=> array('content' => '<a onclick="mmjs.sort(\'name\');" href="#">Display Name</a>'),
   	'email'		=> array('content' => '<a onclick="mmjs.sort(\'email\');" href="#">Email</a>'),
   	'status'	=> array('content' => '<a onclick="mmjs.sort(\'status\');" href="#">Status</a>'),
   	'actions'	=> array('content' => 'Actions')
);

foreach($data as $key => $item)
{
	$email = new MM_EmailAccount($item->id, false);
	
	// Default
	if($item->is_default == '1') {
		$item->default_col = "<img src='".MM_Utils::getImageUrl("default_flag")."' title='Default Email Account' />";
	}
	else {
		$item->default_col = "";
	}
    	
    // Actions
    $actions = "";
	if($item->is_default != '1' && $item->status == '1') {
		$actions .= '<a title="Set as default" onclick="mmjs.setDefault(\''.$item->id.'\')" style="cursor:pointer"><img src="'.MM_Utils::getImageUrl("set_default").'" /></a>';
	}
	else {
		$actions .= '<img src="'.MM_Utils::getImageUrl("clear").'" />';
	}
	
    $actions .= '<a title="Edit Email Account" onclick="mmjs.edit(\'mm-email-accounts-dialog\', \''.$item->id.'\', 540, 380)" style="margin-left: 5px; cursor:pointer"><img src="'.MM_Utils::getImageUrl("edit").'" /></a>';

    
	if($item->status!='1'){
		$actions .= '<a title="Force Confirm" onclick="mmjs.forceConfirm(\''.$item->id.'\')" style="margin-left: 5px; cursor:pointer"><img src="'.MM_Utils::getImageUrl("accept").'" /></a>';;
	}
    
    if(!$email->hasAssociations())
    {
    	$actions .= '<a title="Delete Email Account" onclick="mmjs.remove(\''.$item->id.'\')" style="margin-left: 5px; cursor:pointer;"><img src="'.MM_Utils::getImageUrl("delete").'" /></div></a>';
    }
    
    
    
    $rows[] = array
    (
    	array('content' => $item->id),
    	array( 'content' => $item->default_col),
    	array( 'content' => $item->name),
    	array( 'content' => $item->email),
    	array( 'content' => MM_Utils::getStatusImage($item->status)),
    	array( 'content' => $actions),
    );
}

$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);

$dgHtml = $dataGrid->generateHtml();

if($dgHtml == "") {
	$dgHtml = "<p><i>No email accounts.</i></p>";
}
?>
<div class="wrap">
    <img src="<?php echo MM_Utils::getImageUrl('lrg_members'); ?>" class="mm-header-icon" /> 
    <h2 class="mm-header-text">Employee Accounts</h2>
	
	<div class="mm-button-container">
		<a onclick="mmjs.create('mm-email-accounts-dialog', 540, 380)" class="button-secondary"><img src="<?php echo MM_Utils::getImageUrl('add'); ?>" /> Create Employee Account</a>
	</div>

	<div class="clear"></div>
	
	<?php echo $dgHtml; ?>
</div>