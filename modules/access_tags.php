<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
$view = new MM_AccessTagsView();
$dataGrid = new MM_DataGrid($_REQUEST, "name", "asc", 10);
$data = $view->getData($dataGrid);
$dataGrid->setTotalRecords($data);
$dataGrid->recordName = "access tag";

$rows = array();

$headers = array
(	    
	'id'			=> array('content' => '<a onclick="mmjs.sort(\'id\');" href="#">ID</a> ', 'attr' => ''),
    'icon'			=> array('content' => ''),
    'name'			=> array('content' => '<a onclick="mmjs.sort(\'name\');" href="#">Access Tag / Subscribers</a>'),
    'subType'		=> array('content' => 'Subscription Type'),
    'status'		=> array('content' => '<a onclick="mmjs.sort(\'status\');" href="#">Status</a>'),
    'actions'		=> array('content' => 'Actions')
);

foreach($data as $key => $item)
{
    $tag = new MM_AccessTag($item->id, false);
	
	// Badge
   	if(!empty($item->badge_url))
    {		    	
    	$item->badge_url = '<img src="' . $item->badge_url .'?rnd='. rand(0,1000). '"/>';
    }

    // Member Type / Subscribers		    
    if(!empty($item->member_count))
    {
   		$item->name .= '<p class="has-members"><a href="'.MM_ModuleUtils::getUrl(MM_MODULE_MANAGE_MEMBERS, MM_MODULE_BROWSE_MEMBERS).'&accessTagId='.$item->id.'"><b>'.$item->member_count.'</b> Members</a></p>';
   	}
   	else
   	{
   		$item->name .= '<p class="no-members"><i>No Subscribers</i></p>';
   	}
    
	// Subscription Type
	if($item->is_free != "1")
	{  	
	    $products = array();
	    
	    if(!empty($item->products)) {
		   	foreach($item->products as $product) {
		   		$products[] = $product->name;
		   	}
	    }
	    
		$subType = ' <img src="'.MM_Utils::getImageUrl("money").'" style="vertical-align:middle" title="Paid Access Tag" /> '.join(', ' , $products);
	}
	else {
		$subType = ' <img src="'.MM_Utils::getImageUrl("no_money").'" style="vertical-align:middle" title="Free Access Tag" /> '.MM_NO_DATA;
	}  

    // Actions
    $actions = '<a title="Edit Access Tag" onclick="mmjs.edit(\'mm-access-tags-dialog\', \''.$item->id.'\', 580, 435)" style="cursor:pointer"><img src="'.MM_Utils::getImageUrl("edit").'" /></a>';
    	
    if(!$tag->hasAssociations() && intval($item->member_count)<=0)
    {
    	$actions .= '<a title="Delete Access Tag" onclick="mmjs.remove(\''.$item->id.'\')" style="margin-left: 5px; cursor:pointer;"><img src="'.MM_Utils::getImageUrl("delete").'" /></div></a>';
    }
    	
    $rows[] = array
    (
    	array( 'content' => $item->id),
    	array( 'content' => $item->badge_url, 'attr' => 'class="center"'),
    	array( 'content' => $item->name, 'attr' => 'class="name"'),
    	array( 'content' => $subType),
    	array( 'content' => MM_Utils::getStatusImage($item->status)),
    	array( 'content' => $actions),
    );
}

$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);

$dgHtml = $dataGrid->generateHtml();

if($dgHtml == "") {
	$dgHtml = "<p><i>No access tags.</i></p>";
}
?>
<div class="wrap">
    <img src="<?php echo MM_Utils::getImageUrl('lrg_tag'); ?>" class="mm-header-icon" /> 
    <h2 class="mm-header-text">Access Tags</h2>
	
	<div class="mm-button-container">
		<a onclick="mmjs.create('mm-access-tags-dialog', 580, 435)" class="button-secondary"><img src="<?php echo MM_Utils::getImageUrl('add'); ?>" /> Create Access Tag</a>
	</div>

	<div class="clear"></div>
	
	<?php echo $dgHtml; ?>
</div>