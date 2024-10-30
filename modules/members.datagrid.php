<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
$startTime = (float) array_sum(explode(' ',microtime()));
 
$useCustomField = false;
$useCustomField2 = false;
if(isset($_REQUEST["mm_member_custom_field"])){
	$useCustomField = true;
}
if(isset($_REQUEST["mm_member_custom_field2"])){
	$useCustomField2 = true;
}

$view = new MM_MembersView();
$dataGrid = new MM_DataGrid($_REQUEST, "mm_registered", "desc");

if(isset($_REQUEST["csv"])){
	$data = $view->search($_REQUEST, $dataGrid, true);
}
else{
	$data = $view->search($_REQUEST, $dataGrid, false);
}

$timeStamp = (float) array_sum(explode(' ',microtime()));
$timeStr ="Processing time [".__LINE__."]: ". sprintf("%.4f", ($timeStamp-$startTime))." seconds"; 
LogMe::write($timeStr);

$dataGrid->setTotalRecords($data);
$dataGrid->recordName = "member";

$rows = array();

$headers = array
(	    
	'ID'				=> array('content' => '<a onclick="mmjs.sort(\'ID\');" href="#">ID</a>'),
   	'mm_last_name'		=> array('content' => '<a onclick="mmjs.sort(\'mm_last_name\');" href="#">Name</a>'),
   	'user_email'		=> array('content' => '<a onclick="mmjs.sort(\'user_email\');" href="#">Email</a>'),
   	'mm_phone'			=> array('content' => '<a onclick="mmjs.sort(\'mm_phone\');" href="#">Phone</a>'),
   	'user_login'		=> array('content' => '<a onclick="mmjs.sort(\'user_login\');" href="#">Username</a>'),
   	'mm_member_type_id'	=> array('content' => '<a onclick="mmjs.sort(\'mm_member_type_id\');" href="#">Member Type</a>'),
   	'accessTags'		=> array('content' => 'Access Tags'),
);
$csvHeader = array(
	'ID', 'Name', 'Email', 'Phone', 'Login', 'Member Type', 'LL Main Order ID', 'LL Last Order ID','Access Tags', 'Registered', 
	'Billing Address', 'Billing City', 'Billing State', 'Billing Zip', 'Billing Country',
	'Shipping Address', 'Shipping City', 'Shipping State', 'Shipping Zip', 'Shipping Country', 'Affiliate', 'Sub Affiliate'
);
if($useCustomField){
	$field = new MM_CustomField($_REQUEST["mm_member_custom_field"]);
	if($field->isValid()){
		$headers["mm_custom_field"] = array('content' => $field->getFieldLabel());
	}
	else{
		$useCustomField=false;
	}
}
if($useCustomField2){
	if($_REQUEST["mm_member_custom_field2"] != $_REQUEST["mm_member_custom_field"]){
		$field = new MM_CustomField($_REQUEST["mm_member_custom_field2"]);
		if($field->isValid()){
			$headers["mm_custom_field2"] = array('content' => $field->getFieldLabel());
		}
		else{
			$useCustomField2=false;
		}
	}
	else{
			$useCustomField2=false;
	}
}

if(isset($_REQUEST["csv"])){
	$fields = MM_CustomField::getCustomFieldsList();
	foreach($fields as $id=>$val){
		$customField = new MM_CustomField($id);
		if($customField->isValid()){
			$csvHeader[] = $customField->getFieldLabel();
 		}
	}
}

$headers["mm_registered"]= array('content' => '<a onclick="mmjs.sort(\'mm_registered\');" href="#">Date Added</a>');
$headers["mm_status"] = array('content' => '<a onclick="mmjs.sort(\'mm_status\');" href="#">Status</a>');
$headers['actions'] = array('content' => '');
$csvHeader[] = 'Status';

$csvRows = array($csvHeader);
$affiliateId = MM_OptionUtils::getOption(MM_OPTION_TERMS_AFFILIATE);
$subAffiliateId = MM_OptionUtils::getOption(MM_OPTION_TERMS_SUB_AFFILIATE);

foreach($data as $key => $item)
{
	$user = new MM_User();
	$user->setData($item);
	
	if($user->isAdmin()) 
	{
		continue;
	}
	
	$accessTags = str_replace(",", ", ", $item->access_tags);
	
	if($accessTags == "") {
		$accessTags = MM_NO_DATA;
	}
	
	$name = $user->getFullName();
	
	if($name == "") {
		$name = MM_NO_DATA;
	}
	
	$phone = $user->getPhone();
	
	if($phone == "") {
		$phone = MM_NO_DATA;
	}
	
	// Status
	$status = MM_MemberStatus::getImage($user->getStatus());
	
	$csvStatus = "";
	if($user->isActive()) 
	{
		if(!$user->isFree())
		{
			$csvStatus .= "Paid Member, ";
			$status .= ' <img src="'.MM_Utils::getImageUrl("money").'" style="vertical-align:middle" title="Paid Member" />';
		}
		else {
			$csvStatus .= "Free Member, ";
			$status .= ' <img src="'.MM_Utils::getImageUrl("no_money").'" style="vertical-align:middle" title="Free Member" />';
		}
	}
	else {
		$csvStatus .= "Not a Member, ";
		$status .= ' <img src="'.MM_Utils::getImageUrl("bullet_white").'" style="vertical-align:middle" title="Not a Member" />';
	}
	
	if($user->hasCardOnFile())
	{
		$csvStatus .= "Card on File, ";
		$status .= ' <img src="'.MM_Utils::getImageUrl("creditcards").'" style="vertical-align:middle" title="Card on File" />';
	}
	else {
		$csvStatus .= "No Card on File, ";
		$status .= ' <img src="'.MM_Utils::getImageUrl("bullet_white").'" style="vertical-align:middle" title="No Card on File" />';
	}
	
	$hasAssoc = false;
	if($item->affiliate_product!=""){
		
		$assoc = $user->getAffiliateAssociations();
		if(count($assoc)>0){
			$str = "";
			$index=0;
			foreach($assoc as $row){
				$aff = "";
				if(isset($row["affiliate_id"])){
					$aff.= "{$affiliateId}: ".$row["affiliate_id"];
				}
				if(isset($row["sub_affiliate_id"])){
					if(!empty($aff)){
						$aff .=", ";
					}
					$aff.= "{$subAffiliateId}: ".$row["sub_affiliate_id"];
				}
				if(!empty($row["product"])){
					$str.= $row["product"]." (".$aff.")";
				}
				else{
					$str.=$row["access_type_name"]." (".$aff.")";
				}
				if($index!=count($assoc)-1){
					$str.="&#10;";
				}
				$index++;
			}
			$status.= ' <img src="'.MM_Utils::getImageUrl("user_suit").'" style="vertical-align:middle" title="'.$str.'" />';
			$hasAssoc = true;
		}
	}
	if(!$hasAssoc){
		$status.= ' <img src="'.MM_Utils::getImageUrl("bullet_white").'" style="vertical-align:middle" title="No affiliate associations" />';
	}
	
    // Actions
    $actions = '<a href="'.MM_ModuleUtils::getUrl(MM_MODULE_MANAGE_MEMBERS, MM_MODULE_DETAILS_GENERAL).'&user_id='.$user->getId().'" title="Edit Member" style="cursor:pointer;"><img src="'.MM_Utils::getImageUrl("edit").'" /></a>';
    
    if(!$user->hasActiveSubscriptions()) {
   		$actions .= '<a title="Delete Member" onclick="mmjs.remove(\''.$user->getId().'\')" style="margin-left: 5px; cursor:pointer;"><img src="'.MM_Utils::getImageUrl("delete").'" /></div></a>';
    }
    
	if(isset($_REQUEST["csv"])){
    	$csvAt = ($accessTags==MM_NO_DATA)?"":$accessTags;	
	    $csvRow = array(
	    	$user->getId(),
	    	$name,
	    	$user->getEmail(),
	    	$phone,
	    	$user->getUsername(),
	    	$user->getMemberTypeName(),
			$user->getMainOrderId(),
			"",
	    	//$user->getLastOrderId(),
	    	$csvAt,
	    	$user->getRegistrationDate(true)
	    );
	}
	$accessTagsSub = "<span title='".$accessTags."'>".$accessTags."</span>";
	if(strlen($accessTags)>33){
		$accessTagsSub = "<span title='".$accessTags."'>".substr($accessTags, 0, 33)."...</span>";
	} 
	$row = array(
	    	array('content' => $user->getId()),
	    	array('content' => $name),
	    	array('content' => "<a href='mailto:".$user->getEmail()."'>".$user->getEmail()."</a>"),
	    	array('content' => $phone),
	    	array('content' => "<a href='".MM_ModuleUtils::getUrl(MM_MODULE_MANAGE_MEMBERS, MM_MODULE_DETAILS_GENERAL)."&user_id={$user->getId()}'>".$user->getUsername()."</a>"),
	    	array('content' => $user->getMemberTypeName()),
	    	array('content' => $accessTagsSub),
	);
	
	if(isset($_REQUEST["csv"])){
	    $csvRow[] = $user->getBillingAddress();
	    $csvRow[] = $user->getBillingCity();
	    $csvRow[] = $user->getBillingState();
	    $csvRow[] = $user->getBillingZipCode();
	    $csvRow[] = $user->getBillingCountryName();
	    
	    $csvRow[] = $user->getShippingAddress();
	    $csvRow[] = $user->getShippingCity();
	    $csvRow[] = $user->getShippingState();
	    $csvRow[] = $user->getShippingZipCode();
	    $csvRow[] = $user->getShippingCountryName();
	    $csvRow[] = $item->affiliate_product;
	    $csvRow[] = $item->sub_affiliate_product;
	    
	}
	
    if($useCustomField){
	    if(isset($_REQUEST["csv"])){
		    $csvRow[] = $item->custom_field_value;
	    }
	    $row[] = array('content' => $item->custom_field_value);
    }
    
    if($useCustomField2){
	    if(isset($_REQUEST["csv"])){
		    $csvRow[] = $item->custom_field_value2;
	    }
	    $row[] = array('content' => $item->custom_field_value2);
    }
    
    $row[] = array('content' => $user->getRegistrationDate(true));
    $row[] = array('content' => $status);
    $row[] = array('content' => $actions);
	$rows[] = $row;
		
	if(isset($_REQUEST["csv"])){
		$fields = MM_CustomField::getCustomFieldsList();
		foreach($fields as $id=>$val){
			$customField = new MM_CustomField($id);
			if($customField->isValid()){
				$csvRow[] = $user->getCustomDataByName($customField->getFieldName());
	 		}
		}
		
	    $csvRow[] = $user->getStatusName();
		$csvRows[] = $csvRow;
	}
}

if(isset($_REQUEST["csv"])){
	$csv = "";
	foreach($csvRows as $row){
		$csvRow = "";
		foreach($row as $elem){
			$csvRow.= "\"".preg_replace("/[\"]+/", "", $elem)."\",";
		}
		$csv .= preg_replace("/(\,)$/", "", $csvRow)."\n";
	}
	MM_Session::value(MM_Session::$KEY_CSV, $csv);
}

$timeStamp = (float) array_sum(explode(' ',microtime()));
$timeStr ="Processing time [".__LINE__."]: ". sprintf("%.4f", ($timeStamp-$startTime))." seconds"; 
LogMe::write($timeStr);

$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);
$dataGrid->showCsvControl = true;
$dgHtml = $dataGrid->generateHtml();

if($dgHtml == "") {
	$dgHtml = "<p><i>No members found.</i></p>";
}
?>
<div id='mm_members_csv'></div>
<div id="mm-grid-container">
	<?php echo $dgHtml; ?>
</div>