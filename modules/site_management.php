<?php
/**	
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

$error = "";
$selectedVersion = "";
$started = false;
$exportedVersion = "";
if(isset($_POST["check"])){
    $checked = "";
    $minorVersion = "";
    $selectedVersion = $_POST["export_versions"];
    $exportedVersion = $_POST["export_versions"];
    if(preg_match("/(\-)/", $exportedVersion)){
    	$arr = explode("-", $exportedVersion);
    	if(count($arr)==2){
    		$exportedVersion = $arr[0];
    		$minorVersion = $arr[1];
    	}
    }
    if(isset($_POST["check"])){
    	$started = true;
    	$domainsToCheck = $_POST["check"];
    	foreach($domainsToCheck as $check){
    		$minorVars = (isset($minorVersion) && !empty($minorVersion))?"&minor_version=".$minorVersion:"";
			$site=  new MM_Site($check);
			$domain = $site->getLocation();
			
			$result = MM_MemberMouseService::deployRelease($domain,$exportedVersion,$minorVars, $check);
			
			if(!isset($result->response_code) || (isset($result->response_code) && $result->response_code!="200")){
				$error = ((isset($result->response_message))?$result->response_message:"") . " : Error deploying to site ".$site->getLocation();
				unset($domainsToCheck);
			}
    	}
    }

}
$versionsArr = MM_MemberMouseService::getReleases();
$versions = array();
if(is_array($versionsArr)){
	asort($versionsArr);
	foreach($versionsArr as $val){
		$versions[$val] = $val;
	}
	$versions = array_reverse($versions,true);
}

$view = new MM_SiteMgmtView();
$dataGrid = new MM_DataGrid($_REQUEST, "date_added", "asc", 10);
$data = $view->getData($dataGrid);

$dataGrid->recordName = "site";

$rows = array();

$headers = array	
(	     
	'check'			=> array('content' => "<input type='checkbox' id='checkall'  name='checkall' value='1' onchange=\"mmjs.selectAll()\" class='checkall' />"),
	'id'			=> array('content' => '<a onclick="mmjs.sort(\'id\');" href="#">ID</a> ', 'attr' => ''),
	'name'			=> array('content' => '<a onclick="mmjs.sort(\'name\');" href="#">Name</a>'),
   	'location'		=> array('content' => '<a onclick="mmjs.sort(\'location\');" href="#">Location</a>'),
   	'paid_members'	=> array('content' => '<a onclick="mmjs.sort(\'paid_members\');" href="#">Paid Members</a>'),
   	'total_members'	=> array('content' => '<a onclick="mmjs.sort(\'total_members\');" href="#">Total Members</a>'),
   	'apikey'		=> array('content' => 'API Key'),
   	'apisecret'		=> array('content' => 'API Secret'),
   	'version'		=> array('content' => 'Version No.'),
   	'minor_version'		=> array('content' => 'Minor Version'),
   	'last_update'		=> array('content' => 'Last Updated'),
   	'is_dev'		=> array('content' => 'Is Dev'),
   	'date_added'	=> array('content' => '<a onclick="mmjs.sort(\'date_added\');" href="#">Date Added</a>'),
   	'status'		=> array('content' => '<a onclick="mmjs.sort(\'status\');" href="#">Status</a>'),
   	'actions'		=> array('content' => 'Actions')
);

$data = is_null($data) ? array() : $data;

foreach($data as $key=>$item)
{	
    $site = new MM_Site($item->id, false);
    
    // Actions
    $actions = '<a title="Edit Site" onclick="mmjs.edit(\'mm-site-management-dialog\', \''.$item->id.'\',\''. MM_SiteMgmtView::$DIALOG_ADMIN_WIDTH .'\',\''.MM_SiteMgmtView::$DIALOG_ADMIN_HEIGHT.'\')" style="cursor:pointer;"><img src="'.MM_Utils::getImageUrl("edit").'" /></a>';
   	
    if($item->status>1)
    {
    	$actions .= '<a title="Archive Site" onclick="mmjs.remove(\''.$item->id.'\')" style="margin-left: 5px; cursor:pointer;"><img src="'.MM_Utils::getImageUrl("box").'" /></div></a>';
    }
   
    $status = MM_MemberStatus::getImage($item->status);
    if(empty($status))
    {
    	$status = MM_NO_DATA;
    }
    
    if($item->is_dev=='1'){
    	$item->is_dev = "<img src='".MM_Utils::getImageUrl("tick")."' />";
    }
    else{
    	
    	$item->is_dev = "<img src='".MM_Utils::getImageUrl("cross")."' />";
    }
    $checked = "";
    if(isset($_POST["check"])){
    	foreach($_POST["check"] as $check){
    		if($check==$item->id){
    			$checked= "checked";
    		}
    	}
    }
    $minorColumn = ((empty($item->minor_version))?MM_NO_DATA:$item->minor_version);
    $currentFails = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_MINOR_VERSION_FAILS);
    $cfArr = explode(",", $currentFails);
    if(in_array($item->id, $cfArr)){
    	$altText =  "";
    	if($minorColumn!=MM_NO_DATA){
    		$altText = "Previous version is {$minorColumn}";
    	}
    	$minorColumn= "<img src='".MM_Utils::getImageUrl("error")."' alt='{$altText}' />";
    }
    
    $rows[] = array
    (
    	array('content' => "<input type='checkbox' id='check[]' name='check[]' value='{$item->id}' class='checkall' {$checked} />"),
    	array('content' => $item->id),
    	array('content' => $item->name),
    	array('content' => $item->location),
    	array('content' => $item->paid_members),
    	array('content' => $item->total_members),
    	array('content' => $item->apikey),
    	array('content' => $item->apisecret),
    	array('content' => ((empty($item->version))?MM_NO_DATA:$item->version)),
    	array('content' => $minorColumn),
    	array('content' => Date("M d, Y g:i a", strtotime($item->last_update))),
    	array('content' => $item->is_dev),
    	array('content' => Date("M d, Y g:i a", strtotime($item->date_added))),
    	array('content' => $status),
    	array('content' => $actions),
    );
}

$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);
$dataGrid->setTotalRecords($data);
$dgHtml = $dataGrid->generateHtml();

if($dgHtml == "") {
	$dgHtml = "<p><i>No Sites.</i></p>";
}

if($started){
	?>
	<script type='text/javascript'>
	<?php 
	if(!empty($error)){
		?>
alert('<?php echo $error; ?>');
		<?php 
	}
	else{
		?>
alert("Data exported successfully");
		<?php 	
	}
	?>
	</script>
	<?php 
}
//echo "<pre>";
//var_dump($_POST["check"]);
//echo "</pre>";
?>

<div class="wrap">
    <img src="<?php echo MM_Utils::getImageUrl('lrg_sitemap'); ?>" class="mm-header-icon" /> 
    <h2 class="mm-header-text">Site Management</h2>
	
	
	<div class="mm-button-container">
		<a onclick="mmjs.create('mm-site-management-dialog','<?php echo MM_SiteMgmtView::$DIALOG_ADMIN_WIDTH; ?>','<?php echo MM_SiteMgmtView::$DIALOG_ADMIN_HEIGHT; ?>')" class="button-secondary"><img src="<?php echo MM_Utils::getImageUrl('add'); ?>" /> Create Site</a>
	</div>
<form name='deploy' method='post' action='admin.php?page=mm_admintools' >
	<div class="clear"></div>
	<select name='export_versions'><?php echo MM_HtmlUtils::generateSelectionsList($versions,$selectedVersion); ?></select> <input type='submit' id='deployButton' name='deployButton' class='button' value='Deploy'  onclick="return mmjs.confirmDeploy();" />
	<div class="clear"></div>
	
	<?php echo $dgHtml; ?>
</form>
</div>