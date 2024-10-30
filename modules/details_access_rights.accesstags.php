<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

if(isset($_REQUEST[MM_Session::$PARAM_USER_ID])) {
	MM_Session::value(MM_Session::$KEY_LAST_USER_ID,$_REQUEST[MM_Session::$PARAM_USER_ID]);	
}

$affiliateId = MM_OptionUtils::getOption(MM_OPTION_TERMS_AFFILIATE);
$subAffiliateId = MM_OptionUtils::getOption(MM_OPTION_TERMS_SUB_AFFILIATE);

$view = new MM_MemberDetailsView();
$data = $view->getAccessTags($user->getId());
$dataGrid = new MM_DataGrid();
$dataGrid->showPagingControls = false;
$dataGrid->recordName = "access tag";

$rows = array();

$headers = array
(	    
   	'actions'			=> array('content' => ''),
   	'assocOrder'		=> array('content' => ''),
   	'subType'			=> array('content' => ''),
   	'access_tag'		=> array('content' => 'Access Tag'),
   	'activation_date'	=> array('content' => 'Activation Date'),
   	'edit_calc'	=> array('content' => '')
);

foreach($data as $key => $item)
{	
	// Access Tag
	$tag = new MM_AccessTag($item->id);
	$tagHtml = "";
	
	if($tag->getBadgeUrl() != "") {
		$tagHtml = "<img src='".$tag->getBadgeUrl()."' style='vertical-align: middle;' /> ";
	}
	
	$tagHtml .= $tag->getName();
	
	// Associated Order
	if(intval($user->getAccessTagOrderId($tag->getId())) != MM_TransactionEngine::$MM_DFLT_ORDER_ID)
	{
		$assocOrder = '<a href="'.MM_LimeLightUtils::getLLOrderUrl($user->getAccessTagOrderId($tag->getId())).'" target="_blank" class="button-secondary" title="View order '.$user->getAccessTagOrderId($tag->getId()).' in Lime Light"><img src="'.MM_Utils::getImageUrl('cart').'" /></a>';
	}
	else {
		$assocOrder = '';
	}
	
	// Subscription Type
	if(!$tag->isFree())
	{
		$subType = ' <img src="'.MM_Utils::getImageUrl("money").'" style="vertical-align:middle" title="Paid Access Tag" />';
	}
	else {
		$subType = ' <img src="'.MM_Utils::getImageUrl("no_money").'" style="vertical-align:middle" title="Free Access Tag" />';
	}
	
	$affiliates = "";
	if(!$tag->isFree()){
		$tagProducts = $tag->getAssociatedProducts();
		if(count($tagProducts)>0){
			foreach($tagProducts as $productId=>$name){
				$assoc = $user->getAffiliateAssociations($productId);
			
				if(count($assoc)>0){
					$obj = $assoc[0];
					$affiliateData = "";
					
					if(isset($obj["affiliate_id"])){
						$affiliateData = $affiliateId.": ".$obj["affiliate_id"];
					}
					if(isset($obj["sub_affiliate_id"])){
						if(!empty($affiliateData)){
							$affiliateData.=", ";
						}
						$affiliateData .= $subAffiliateId.": ".$obj["sub_affiliate_id"];
					}
					if(!empty($affiliateData)){
						$affiliates .= $name." (".$affiliateData.")&#10;";
					}
				}
			}
		}
	}
	if(strlen($affiliates)>4){
		$affiliates = substr($affiliates,0,strlen($affiliates)-5);
	}
	
	$affImg = ' <img src="'.MM_Utils::getImageUrl("bullet_white").'" style="vertical-align:middle" title="No affiliate associated with this tag." />';
	if(!empty($affiliates)){
		$affImg = ' <img src="'.MM_Utils::getImageUrl("user_suit").'" style="vertical-align:middle" title="'.$affiliates.'" />';
	}
	
	// Activation Date
	if($item->isActive == "1") {
		$actDate = date("M d, Y g:i a", strtotime($item->activationDate));
	}
	else {
		$actDate = MM_NO_DATA;
	}
	
	$calcStr = "";
	
	// Action
	if($item->isActive == "1") {
		$action = '<a onclick="mmjs.deactivateAccessTag(\''.$user->getId().'\', \''.$tag->getId().'\', \''.$tag->isFree().'\', \''.$user->hasCardOnFile().'\')" class="button-secondary" title="Deactivate '.$tag->getName().'"><img src="'.MM_Utils::getImageUrl('stop').'" /> Deactivate</a>';
		$action .= '<a onclick="mmjs.deactivateAccessTag(\''.$user->getId().'\', \''.$tag->getId().'\', \''.$tag->isFree().'\', \''.$user->hasCardOnFile().'\',\'1\')" class="button-secondary" title="Pause '.$tag->getName().'"><img src="'.MM_Utils::getImageUrl('pause').'" /> Pause</a>';
		$calcStr = '<a style="cursor: pointer" onclick="mmjs.editCalcMethod(\''.$item->id.'\')" title="Edit calculation method for '.$tag->getName().'"><img src="'.MM_Utils::getImageUrl('edit').'" /></a>';
	}
	else {
		$hasMultipleProducts = count($tag->getAssociatedProducts()) > 1;
		$action = '<a onclick="mmjs.activateAccessTag(\''.$user->getId().'\', \''.$tag->getId().'\', \''.$tag->isFree().'\', \''.$user->hasCardOnFile().'\', \''.$hasMultipleProducts.'\',\''.$flagNoPay.'\')" class="button-secondary" title="Activate '.$tag->getName().'"><img src="'.MM_Utils::getImageUrl('accept').'" /> Activate</a>';
	}
	
	
	
    $rows[] = array
    (
    	array('content' => $action),
    	array('content' => $assocOrder),
    	array('content' => $subType.$affImg),
    	array('content' => $tagHtml),
    	array('content' => $actDate),
    	array('content' => $calcStr)
    );
}

$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);

$dgHtml = $dataGrid->generateHtml();

if($dgHtml == "") {
	$dgHtml = "<p><i>No access tags found.</i></p>";
}
?>
<div id='mm-edit-calc-method-dialog'></div>
<div id="mm-grid-container">
	<?php echo $dgHtml; ?>
</div>