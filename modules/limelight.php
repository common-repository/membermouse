<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
global $mmSite;

$ids = explode(",",$mmSite->getCampaignIds());

$idArr = array();
$firstElement = 0;
foreach($ids as $id){
	if($firstElement==0){
		$firstElement = $id;
	}
	$tmpCampaign = new MM_Campaign($id);
	$idArr[$id] = $tmpCampaign->getName();
}
$campaignId = 0;
if(isset($_GET["campaign_id"])){
	$campaignTest = new MM_Campaign($_GET["campaign_id"]);
	if($campaignTest->isValid()){
		MM_Session::value("list_campaign_id", $_GET["campaign_id"]);
	}
	else{
		MM_Session::value("list_campaign_id", $firstElement);
	}
}
$campaignId = MM_Session::value("list_campaign_id");
if(intval($campaignId)<=0){
	$campaignId = array_shift($ids);
}

if(intval($campaignId)<=0){
   $campaignId = array_shift($ids);
}

$tmpCampaign = new MM_Campaign($campaignId);
$campaignSelect = MM_HtmlUtils::generateSelectionsList($idArr,$campaignId); 
?>
<div id="products-container" class="wrap">
	<div class="mm-button-container" style="margin-top: 20px">
		
	</div>
	
<b>Campaign: </b> <select id='campaign_id' onchange="mmjs.showCampaignInfo();">
<?php echo $campaignSelect; ?>
</select>
<?php 

if($campaignId>0){
$view = new MM_LimeLightView();
$dataGrid = new MM_DataGrid($_REQUEST, "name", "asc", 10);
$data = $view->getData($dataGrid, $campaignId);
$dataGrid->setTotalRecords($data);
$dataGrid->recordName = "product";

$rows = array();

$headers = array
(
   	'status'		=> array('content' => ''),
	'id'			=> array('content' => 'ID'),
	'product_id'			=> array('content' => 'Lime Light ID'),
   	'name'			=> array('content' => '<a onclick="mmjs.sort(\'name\');" href="#">Name</a>'),
   	'category_name'	=> array('content' => '<a onclick="mmjs.sort(\'category_name\');" href="#">Category</a>'),
   	'sku'			=> array('content' => '<a onclick="mmjs.sort(\'sku\');" href="#">SKU</a>'),
   	'price'			=> array('content' => '<a onclick="mmjs.sort(\'price\');" href="#">Price</a>'),
   	'rebill'		=> array('content' => 'Rebill Settings'),
   	'attributes'	=> array('content' => 'Attributes'),
   	'access_type'	=> array('content' => 'Access Type')
);
    
$body = array();

foreach($data as $key => $item)
{
	$product = new MM_Product($item->id, false);
	$product->setData($item);
	
	// Price
	if($product->getPrice() != "") {
		$item->price = "$".$product->getPrice();
	} 
	else {
		$item->price = MM_NO_DATA;	
	}
	
	// SKU
	if($product->getSku() == "") {
		$item->sku = MM_NO_DATA;	
	}
	
	// Rebill Information
	$item->rebill_desc = $product->getRebillDescription();
	
	if($item->rebill_desc == "")
	{
		$item->rebill_desc = MM_NO_DATA;	
	}
	
	// Access Types
	$accessTypes = "";
	
	$assocMemberType = $product->getAssociatedMemberType();
	
    if($assocMemberType) {
    	$accessTypes .= "<img style='vertical-align: middle;' src=".MM_Utils::getImageUrl("user")." /> <a href='".MM_ModuleUtils::getUrl(MM_MODULE_CONFIGURE_SITE, MM_MODULE_MEMBER_TYPES)."'>".$assocMemberType->name."</a>";
    }
    
    $assocAccessTag = $product->getAssociatedAccessTag();
    
    if($assocAccessTag) {
		$accessTypes .= "<img style='vertical-align: middle;' src=".MM_Utils::getImageUrl("tag")." /> <a href='".MM_ModuleUtils::getUrl(MM_MODULE_CONFIGURE_SITE, MM_MODULE_ACCESS_TAGS)."'>".$assocAccessTag->name."</a>";
    }
    
    if($accessTypes == "")
    {
    	$accessTypes = MM_NO_DATA;
    }
    
    // Attributes
    $attributes = "";
    
    if($product->isTrial()) {
    	$attributes .= "<img style='vertical-align: middle;' title='Free Trial' src=".MM_Utils::getImageUrl("time")." /> ";
    }
    
    if($product->isShippable()) {
    	$attributes .= "<img style='vertical-align: middle;' title='Shippable' src=".MM_Utils::getImageUrl("lorry")." /> ";
    }
    
    if($item->status == "0") {
    	$status = "<img style='vertical-align: middle;' title='WARNING!! In the last update from Lime Light, this product was no included. This may mean that the product has been removed from Lime Light. If any member types or access tags are associated with this product, please verify that the product is still in Lime Light.' src=".MM_Utils::getImageUrl("error")." /> ";
    }
    else {
    	$status = "";
    }
    	
    $rows[] = array
    (
    	array( 'content' => $status),
    	array( 'content' => $item->id),
    	array( 'content' => $item->product_id),
    	array( 'content' => $item->name),
    	array( 'content' => $item->category_name),
    	array( 'content' => $item->sku),
    	array( 'content' => $item->price),
    	array( 'content' => $item->rebill_desc),
    	array( 'content' => $attributes),
    	array( 'content' => $accessTypes),
    );
}
	
$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);

$dgHtml = $dataGrid->generateHtml();

if($dgHtml == "") {
	$dgHtml = "<p><i>No products.</i></p>";
}
$campaign = new MM_Campaign($campaignId);
?>
	<div style='clear:both; height: 10px;'></div>
	<img src="<?php echo MM_Utils::getImageUrl('lrg_briefcase'); ?>" class="mm-header-icon" /> 
    <h2 class="mm-header-text">Campaign Information</h2>
	
	<div style="margin-bottom: 15px; line-height: 26px;">
		
		<img src="<?php echo MM_Utils::getImageUrl('us'); ?>" style="vertical-align: middle" />
		Countries: 
		<select><?php echo MM_HtmlUtils::getCampaignCountryList($campaignId); ?></select>
		<br />
		
		<img src="<?php echo MM_Utils::getImageUrl('lorry'); ?>" style="vertical-align: middle" /> 
		Shipping Methods: 
		<select><?php echo MM_HtmlUtils::getCampaignShippingList($campaignId); ?></select>
		<br />
		
		<img src="<?php echo MM_Utils::getImageUrl('creditcards'); ?>" style="vertical-align: middle" /> 
		Payment Methods: 
		<select><?php echo MM_HtmlUtils::getCampaignPaymentList($campaignId); ?></select>
	</div>
	
	<img src="<?php echo MM_Utils::getImageUrl('lrg_cart'); ?>" class="mm-header-icon" /> 
    <h2 class="mm-header-text">Products</h2>
	
	<?php echo $dgHtml; ?>
<?php } ?>
</div>