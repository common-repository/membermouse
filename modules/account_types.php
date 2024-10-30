<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

$view = new MM_AccountTypesView();
$dataGrid = new MM_DataGrid($_REQUEST, "name", "asc", 10);
$data = $view->getData($dataGrid);
$dataGrid->setTotalRecords($data);
$dataGrid->recordName = "account type";

$rows = array();

$headers = array
(	    
	'id'				=> array('content' => '<a onclick="mmjs.sort(\'id\');" href="#">ID</a> ', 'attr' => ''),
    'name'				=> array('content' => '<a onclick="mmjs.sort(\'name\');" href="#">Account Types</a>'),
    'num_sites'			=> array('content' => '<a onclick="mmjs.sort(\'num_sites\');" href="#"># Sites</a>'),
    'num_paid_members'	=> array('content' => '<a onclick="mmjs.sort(\'num_paid_members\');" href="#"># Paid Members</a>'),
    'num_total_members'	=> array('content' => '<a onclick="mmjs.sort(\'num_total_members\');" href="#"># Total Members</a>'),
    'status'			=> array('content' => '<a onclick="mmjs.sort(\'status\');" href="#">Status</a>'),
    'actions'			=> array('content' => 'Actions')
);

foreach($data as $key => $item)
{
	$acctType = new MM_AccountType($item->id, false);
    
    // # Paid Members
    if($item->unlimited_paid_members == "1") {
    	$item->num_paid_members = MM_AccountType::$MM_UNLIMITED;
    }
    
    // # Total Members
    if($item->unlimited_total_members == "1") {
    	$item->num_total_members = MM_AccountType::$MM_UNLIMITED;
    }
    	
    // Actions
    $actions = '<a title="Edit Account Type" onclick="mmjs.edit(\'mm-account-types-dialog\', \''.$item->id.'\', 500, 280)" style="cursor:pointer"><img src="'.MM_Utils::getImageUrl("edit").'" /></a>';
    	
    if(!$acctType->hasAssociations())
    {
    	$actions .= '<a title="Delete Account Type" onclick="mmjs.remove(\''.$item->id.'\')" style="margin-left: 5px; cursor:pointer;"><img src="'.MM_Utils::getImageUrl("delete").'" /></div></a>';
    }
    
    $rows[] = array
    (
    	array('content' => $item->id),
    	array( 'content' => $item->name),
    	array( 'content' => $item->num_sites),
    	array( 'content' => ($item->num_paid_members == MM_AccountType::$MM_UNLIMITED) ? MM_AccountType::$MM_UNLIMITED : number_format($item->num_paid_members)),
    	array( 'content' => ($item->num_total_members == MM_AccountType::$MM_UNLIMITED) ? MM_AccountType::$MM_UNLIMITED : number_format($item->num_total_members)),
    	array( 'content' => MM_Utils::getStatusImage($item->status)),
    	array( 'content' => $actions),
    );
}
	
$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);

$dgHtml = $dataGrid->generateHtml();

if($dgHtml == "") {
	$dgHtml = "<p><i>No account types.</i></p>";
}
?>
<div class="wrap">
    <img src="<?php echo MM_Utils::getImageUrl('lrg_tools'); ?>" class="mm-header-icon" /> 
    <h2 class="mm-header-text">Account Types</h2>
	
	<div class="mm-button-container">
		<a onclick="mmjs.create('mm-account-types-dialog', 500, 280)" class="button-secondary"><img src="<?php echo MM_Utils::getImageUrl('add'); ?>" /> Create Account Type</a>
	</div>

	<div class="clear"></div>
	
	<?php echo $dgHtml; ?>
</div>