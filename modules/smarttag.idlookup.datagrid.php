<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

$view = new MM_SmartTagLibraryView();
$dataGrid = new MM_DataGrid($_REQUEST, "name", "asc", 10);
$data = $view->getLookupData($p->objectType, $dataGrid);
$dataGrid->showPagingControls = false;

switch($p->objectType)
{
	case MM_TYPE_POST:
		$dataGrid->recordName = "protected post";
		break;
		
	case MM_TYPE_ACCESS_TAG:
		$dataGrid->recordName = "access tag";
		break;
		
	case MM_TYPE_MEMBER_TYPE:
		$dataGrid->recordName = "member type";
		break;
		
	case MM_TYPE_EMAIL_ACCOUNT:
		$dataGrid->recordName = "email account";
		break;
		
	case MM_TYPE_PRODUCT:
		$dataGrid->recordName = "product";
		break;
		
	case MM_TYPE_CUSTOM_FIELD:
		$dataGrid->recordName = "custom field";
		break;
}

$rows = array();

$headers = array
(	    
	'action' 	=> array('content' => ''),
	'id'		=> array('content' => 'ID'),
   	'name'		=> array('content' => 'Name')
);

foreach($data as $key => $item)
{   	
    $action = '<a title="Insert ID \''.$item->id.'\'" onclick="stl_js.insertContent(\''.$item->id.'\')"><img src="'.MM_Utils::getImageUrl("add").'" /></a>';
    
    $ext = "";
    if(MM_Utils::isLimeLightInstall()){
    	if(isset($item->product_id)){
    		$ext = " (".$item->product_id.")";
    	}
    }
    $rows[] = array
    (
    	array( 'content' => $action),
    	array('content' => $item->id),
    	array( 'content' => $item->name . $ext),
    );
}

$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);

$dgHtml = $dataGrid->generateHtml();

if($dgHtml == "") {
	$dgHtml = "<p><i>No ".$dataGrid->recordName."s found.</i></p>";
}

echo $dgHtml; 
?>