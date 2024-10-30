<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
$view = new MM_MemberTypesView();
$dataGrid = new MM_DataGrid($_REQUEST, "name", "asc", 10);
$data = $view->getData($dataGrid);
$dataGrid->setTotalRecords($data);
$dataGrid->recordName = "member type";

$rows = array();

$headers = array
(	    
	'id'				=> array('content' => '<a onclick="mmjs.sort(\'id\');" href="#">ID</a> ', 'attr' => ''),
	'default'			=> array('content' => ''),
   	'icon'				=> array('content' => ''),
   	'name'				=> array('content' => '<a onclick="mmjs.sort(\'name\');" href="#">Member Type / Subscribers</a>'),
   	'product_id'		=> array('content' => 'Subscription Type'),
   	'access_tags'		=> array('content' => 'Access Tags'),
   	'downgrade_to_id'	=> array('content' => '<a onclick="mmjs.sort(\'downgrade_to_id\');" href="#">Downgrades To</a>'),
   	'upgrade_to_id'		=> array('content' => '<a onclick="mmjs.sort(\'upgrade_to_id\');" href="#">Upgrades To</a>'),
   	'status'			=> array('content' => '<a onclick="mmjs.sort(\'status\');" href="#">Status</a>'),
   	'actions'			=> array('content' => 'Actions')
);

foreach($data as $key=>$item)
{	
    $memberType = new MM_MemberType($item->id, false);
	
    // Default
	if($item->is_default == "1") {
		$defaultCol = "<img src='".MM_Utils::getImageUrl("default_flag")."' title='Default Member Type' />";
	}
	else {
		$defaultCol = "";
	}
	
	// Badge
   	if(!empty($item->badge_url))
    {		    	
    	$item->badge_url = '<img src="' . $item->badge_url .'?rnd='. rand(0,1000). '"/>';
    }
	
    // Member Type / Subscribers		    
    if(!empty($item->member_count))
    {
   		$item->name .= '<p class="has-members"><a href="'.MM_ModuleUtils::getUrl(MM_MODULE_MANAGE_MEMBERS, MM_MODULE_BROWSE_MEMBERS).'&memberTypeId='.$item->id.'"><b>'.$item->member_count.'</b> Members</a></p>';
   	}
   	else
   	{
   		$item->name .= '<p class="no-members"><i>No Members</i></p>';
   	}
    	
   	// Subscription Type
   	$subType = "";
   	
   	if(isset($item->products) && !empty($item->products)) {
   		$productNames = "";
   		foreach($item->products as $product){
   			$productNames.= $product->name.", ";
   		}
   		$productNames = wordwrap(preg_replace("/(\, )$/", "", $productNames), 60, '<br />');
    	$subType = ' <img src="'.MM_Utils::getImageUrl("money").'" style="vertical-align:middle" title="Paid Member Type" /> '.$productNames;
   	}
   	else {
    	$subType = ' <img src="'.MM_Utils::getImageUrl("no_money").'" style="vertical-align:middle" title="Free Member Type" /> '.MM_NO_DATA;
   	}
    
    // Access Tags   	
    $tags = array();
    
    if(!empty($item->access_tags)) {
	   	foreach($item->access_tags as $tag) {
	   		$tags[] = $tag->name;
	   	}
    }

    $item->access_tags = !empty($tags) ? join(', ' , $tags) : MM_NO_DATA;
    
    // Upgrade | Downgrade
    $item->upgrade_to 	= ($item->upgrade_to && !empty($item->upgrade_to->name)) ? $item->upgrade_to->name : MM_NO_DATA;
    $item->downgrade_to  = ($item->downgrade_to && !empty($item->downgrade_to->name)) ? $item->downgrade_to->name : MM_NO_DATA;
    
    // Actions
    $actions = "";
    
	if($item->is_default != '1' && $item->status == '1' && $item->is_free == "1") {
		$actions .= '<a title="Set as default" onclick="mmjs.setDefault(\''.$item->id.'\')" style="cursor:pointer"><img src="'.MM_Utils::getImageUrl("set_default").'" /></a>';
	}
	else {
		$actions .= '<img src="'.MM_Utils::getImageUrl("clear").'" />';
	}
	
    $actions .= '<a title="Edit Member Type" onclick="mmjs.edit(\'mm-member-types-dialog\', \''.$item->id.'\')" style="margin-left: 5px; cursor:pointer"><img src="'.MM_Utils::getImageUrl("edit").'" /></a>';
   	
    if(!$memberType->hasAssociations() && intval($item->member_count)<=0)
    {
    	$actions .= '<a title="Delete Member Type" onclick="mmjs.remove(\''.$item->id.'\')" style="margin-left: 5px; cursor:pointer;"><img src="'.MM_Utils::getImageUrl("delete").'" /></div></a>';
    }
    	
    $rows[] = array
    (
    	array('content' => $item->id),
    	array('content' => $defaultCol),
    	array('content' => $item->badge_url, 'attr' => 'class="center"'),
    	array('content' => $item->name, 'attr' => 'class="name"'),
    	array('content' => $subType),
    	array('content' => $item->access_tags),
    	array('content' => $item->downgrade_to),
    	array('content' => $item->upgrade_to),
    	array('content' => MM_Utils::getStatusImage($item->status)),
    	array('content' => $actions),
    );
}

$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);

$dgHtml = $dataGrid->generateHtml();

if($dgHtml == "") {
	$dgHtml = "<p><i>No member types.</i></p>";
}
?>
<div class="wrap">
    <img src="<?php echo MM_Utils::getImageUrl('lrg_members'); ?>" class="mm-header-icon" /> 
    <h2 class="mm-header-text">Member Types</h2>
	
	<div class="mm-button-container">
		<a onclick="mmjs.create('mm-member-types-dialog')" class="button-secondary"><img src="<?php echo MM_Utils::getImageUrl('add'); ?>" /> Create Member Type</a>
	</div>
	
	<div class="clear"></div>
	
	<?php echo $dgHtml; ?>
</div>