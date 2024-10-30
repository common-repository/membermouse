<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_Product extends MM_Entity
{	
	public $isLL = true;
	private $productId = "";
	private $productDisplayname = "";
	private $campaignId = "";
	private $name = "";
	private $sku = "";
	private $description = "";
	private $price = "";
	private $categoryName = "";
	private $isShippableInd = "";
	private $isTrialInd = "";
	private $rebillPeriod = "";
	private $trialAmount = "";
	private $trialDuration = "";
	private $rebillProductId = "";
	private $paymentId = "";
	private $rebillFrequency = "";
	private $trialFrequency = "";
	private $status = "1";
	
	public function getData() 
	{
		global $wpdb;
		
		$sql = "SELECT 
					p.*, c.name as campaign_name
				FROM 
					".MM_TABLE_PRODUCTS." p
						LEFT JOIN ".MM_TABLE_CAMPAIGNS." c on p.campaign_id=c.id 
				WHERE 
					p.id='".$this->id."'
					
		";
		$result = $wpdb->get_row($sql);
		
		if($result) {
			$this->setData($result);
		}
		else {
			parent::invalidate();
			LogMe::write("MM_Product.getData(): error retrieving data for product with id of {$this->id}. Query run is ".$sql);
		}
	}
	
	public static function hasAssociations($productId){
		global $wpdb;
	
		$sql = "select count(*) as total from ".MM_TABLE_MEMBER_TYPE_PRODUCTS." mtp where mtp.product_id='{$productId}'  ";
		$row = $wpdb->get_row($sql);
		if($row->total>0){
			return true;
		}
	
		$sql = "select count(*) as total from ".MM_TABLE_ACCESS_TAG_PRODUCTS." mtp where mtp.product_id='{$productId}'  ";
		$row = $wpdb->get_row($sql);
		if($row->total>0){
			return true;
		}
		return false;
	}
	
	public function setData($data)
	{
		try 
		{
			if(isset($data->campaign_name)){
				$this->productDisplayname = $data->name." [".$data->campaign_name."]";
			}
			else{
				global $wpdb;
				$sql = "select name from ".MM_TABLE_CAMPAIGNS." where id='{$data->campaign_id}'";
				$row = $wpdb->get_row($sql);
				if(isset($row->name)){
					$this->productDisplayname = $data->name." [".$row->name."]";
				}
				else{
					$this->productDisplayname = $data->name;
				}
			}
			
			$this->productId = $data->product_id;
			$this->campaignId = $data->campaign_id;
			$this->name = $data->name;
			$this->sku = $data->sku;
			$this->description = $data->description;
			$this->price = $data->price;
			$this->categoryName = $data->category_name;
			$this->isShippableInd = $data->is_shippable;
			$this->isTrialInd = $data->is_trial;
			$this->rebillPeriod = $data->rebill_period;
			$this->rebillProductId = $data->rebill_product_id;
			$this->trialAmount = $data->trial_amount;
			$this->trialDuration = $data->trial_duration;
			$this->trialFrequency = $data->trial_frequency;
			$this->rebillFrequency = $data->rebill_frequency;
			$this->paymentId = $data->payment_id;
			$this->status = $data->status;
			parent::validate();
		}
		catch (Exception $ex) {
			parent::invalidate();
		}
	}
	
	public function remove(){
		global $wpdb;
		
		if(!$this->hasAssociation()){
			$sql = "delete from ".MM_TABLE_PRODUCTS." where id='{$this->id}' limit 1";
			if($wpdb->query($sql)){
				return true;
			}
		}
		return false;
	}
	
	public function commitData()
	{	
		global $wpdb;
		
		if($this->isLL){
			if($this->productExists()) {
				$doUpdate = true;
			} else {
				$doUpdate = false;
			}
		}
		else{
			if($this->id>0){
				$doUpdate= true;
			}
			else{
				$doUpdate = false;
			}
		}
		 
		MM_Transaction::begin();
		try
		{	
			if(!$doUpdate) 
			{
				$sql = "insert into ".MM_TABLE_PRODUCTS." set " .
		 			"	product_id='%d'," .
		 			"	campaign_id='%d'," .
		 			"	name='%s'," .
		 			"	sku='%s'," .
		 			"	description='%s'," .
		 			"	price='%s'," .
		 			"	category_name='%s'," .
		 			"	rebill_frequency='%s'," .
		 			"	trial_frequency='%s'," .
		 			"	is_shippable='%d'," .
		 			"	is_trial='%d'," .
		 			"	trial_amount='%d'," .
		 			"	trial_duration='%d'," .
		 			"	rebill_period='%d'," .
		 			"	rebill_product_id='%d', " .
		 			"	payment_id='%d', " .
		 			"	status='{$this->status}'" .
		 			"";
			
			}
			else 
			{	
				$sql = "update ".MM_TABLE_PRODUCTS." set " .
		 			"	product_id='%d'," .
		 			"	campaign_id='%d'," .
		 			"	name='%s'," .
		 			"	sku='%s'," .
		 			"	description='%s'," .
		 			"	price='%s'," .
		 			"	category_name='%s'," .
		 			"	rebill_frequency='%s'," .
		 			"	trial_frequency='%s'," .
		 			"	is_shippable='%d'," .
		 			"	is_trial='%d'," .
		 			"	trial_amount='%d'," .
		 			"	trial_duration='%d'," .
		 			"	rebill_period='%d'," .
		 			"	rebill_product_id='%d'," .
		 			"	payment_id='%d', " .
		 			"	status='{$this->status}' where id='{$this->id}'" .
		 			"";
		 	}
		 	
		 	$preparedSql = $wpdb->prepare($sql, $this->productId, $this->campaignId, $this->name, $this->sku, $this->description, $this->price, 
								$this->categoryName, $this->rebillFrequency,$this->trialFrequency, $this->isShippableInd, $this->isTrialInd,$this->trialAmount, $this->trialDuration, $this->rebillPeriod, 
								$this->rebillProductId, $this->paymentId); 
							
		 	$result = $wpdb->query($preparedSql);
			LogMe::write("***********".$preparedSql. " : ".json_encode($result));					
			if($result === false)
		 	{
			LogMe::write("***********ROLLBACK: ".$preparedSql. " : ".json_encode($result));	
		 		MM_Transaction::rollback();
		 		return new MM_Response("ERROR: unable to create product (".$preparedSql.")", MM_Response::$ERROR);
		 	}
		}
		catch(Exception $ex)
		{
		 	MM_Transaction::rollback();
	 		return new MM_Response("ERROR: unable to create product", MM_Response::$ERROR);
		}
		 
		MM_Transaction::commit();
	
		return new MM_Response();
	}
	
	/*
	 * Function to be used with campaigns<=0
	 */
	public function getDataByProductId($productId, $columnWhere = "product_id"){
	global $wpdb;
		$sql = "select * from ".MM_TABLE_PRODUCTS." where {$columnWhere}='{$productId}' and (campaign_id<=0 OR campaign_id IS NULL) limit 1";
		LogMe::write("getDataByProductId() : ".$sql);
		$row = $wpdb->get_row($sql);
		if(isset($row->id) && intval($row->id)>0){
			$this->id = $row->id;
			$this->setData($row);
		}
		else{
			parent::invalidate();
		}
	}
	
	public function getProductByCampaign($productId, $campaignId){
		global $wpdb;
		$sql = "select * from ".MM_TABLE_PRODUCTS." where product_id='{$productId}' and campaign_id='{$campaignId}'";
		
		$row = $wpdb->get_row($sql);
		if(isset($row->id) && intval($row->id)>0){
			$this->id = $row->id;
			$this->setData($row);
		}
		else{
			parent::invalidate();
		}
	}
	
	public function getProductDisplayName(){
		return $this->productDisplayname;
	}
	
	private function productExists()
	{
		global $wpdb;
		
		$sql = "select id from ".MM_TABLE_PRODUCTS." where campaign_id='%d' and product_id='%d' limit 1";
		$result = $wpdb->get_row($wpdb->prepare($sql, $this->campaignId, $this->productId));
		if(isset($result->id) && intval($result->id)>0){
			$this->id = $result->id;
			return true;
		}
		return false;
	}
	
	public function getAssociatedMemberType()
	{
		global $wpdb;
		
		$sql = "SELECT member_type_id as id, (select name from ".MM_TABLE_MEMBER_TYPES." where id=mtp.member_type_id) as name FROM ".MM_TABLE_MEMBER_TYPE_PRODUCTS." mtp WHERE product_id='{$this->id}' LIMIT 1";
		
		return $wpdb->get_row($sql);
	}
	
	public function isAssociatedWithMemberType()
	{
		$mt = $this->getAssociatedMemberType();
		if(isset($mt->id) && $mt->id>0){
			return true;
		}
		return false;
	}
	
	public function isAssociatedWithAccessTag()
	{
		$at = $this->getAssociatedAccessTag();
		if(isset($at->id) && $at->id>0){
			return true;
		}
		return false;
	}
	
	public function hasAssociation(){
		$mt = $this->getAssociatedMemberType();
		if(isset($mt->id) && intval($mt->id)>0){
			return true;
		}
		$at = $this->getAssociatedAccessTag();
		if(isset($at->id) && intval($at->id)>0){
			return true;
		}
		return false;
	}
	
	public function getAssociatedAccessTag() 
	{
		global $wpdb;
		$sql = "select at.* from ".MM_TABLE_ACCESS_TAG_PRODUCTS." aat, ".MM_TABLE_ACCESS_TAGS." at where aat.product_id='{$this->id}' and aat.access_tag_id=at.id LIMIT 1";
		return $wpdb->get_row($sql);
	}
	
	public static function getMemberTypeProductsList($memberTypeId) 
	{
		global $wpdb;
		$sql = "select * from ".MM_TABLE_PRODUCTS." where id not in (select product_id from ".MM_TABLE_ACCESS_TAG_PRODUCTS.") order by campaign_id asc, product_id asc";
		$rows = $wpdb->get_results($sql);
		
		$products = array();
		foreach($rows as $row)
		{
			$product = new MM_Product($row->id, false);
			$product_row = $product->getAssociatedMemberType();
			
			
			// if product is associated with the member type passed, then show it
			if(isset($product_row->id) && isset($memberTypeId) && ($memberTypeId == $product_row->id)) {
				$campaign = new MM_Campaign($row->campaign_id);
				$campaignName = $campaign->getName();
				if(!empty($campaignName)){
					$products[$row->id] = $row->name." [".$campaignName."]";
				}
				else{
					$products[$row->id] = $row->name;
				}
			}
			
			// if product is associated with another member type then hide it
			if(!$product_row) {	
				$campaign = new MM_Campaign($row->campaign_id);
				$campaignName = $campaign->getName();
				if(!empty($campaignName)){
					$products[$row->id] = $row->name." [".$campaignName."]";
				}
				else{
					$products[$row->id] = $row->name;
				}
				
			}
		}
		
		return $products;
	}
	
	public static function getAccessTagProductsList($accessTagId)
	{
		global $wpdb;

		$sql = "select * from ".MM_TABLE_PRODUCTS." where id not in (select product_id from ".MM_TABLE_MEMBER_TYPE_PRODUCTS.")";
		$rows = $wpdb->get_results($sql);
		$products = array();
		
		foreach($rows as $row)
		{
			$product = new MM_Product($row->id, false);
			$accessTag = $product->getAssociatedAccessTag();
			
			if($accessTag) {
				// if product is associated with the access tag passed, then show it
				if(isset($accessTagId) && ($accessTagId == $accessTag->id)) {
					$campaign = new MM_Campaign($row->campaign_id);
					if($campaign->getName() != ""){
						$products[$row->id] = $row->name." [".$campaign->getName()."]";
					}
					else{
						$products[$row->id] = $row->name;
					}
				}
				
			}
			else {
				$campaign = new MM_Campaign($row->campaign_id);
				if($campaign->getName() != ""){
					$products[$row->id] = $row->name." [".$campaign->getName()."]";
				}
				else{
					$products[$row->id] = $row->name;
				}
			}
		}
		
		return $products;
	}
	
	public function getProductsAndAssociations($core_page_type_id, $page_id)
	{
		global $wpdb;
		
		$cpid_sql = "core_page_type_id='{$core_page_type_id}'";
		if($core_page_type_id==MM_CorePageType::$PAID_CONFIRMATION)
		{
			$cpid_sql = "core_page_type_id IN ('".MM_CorePageType::$FREE_CONFIRMATION."','".MM_CorePageType::$PAID_CONFIRMATION."')";	
		}
		$sql = "select 
					'member_type' as ref_type, mt.id as type_id,mt.name as type_name, p.id as product_id, p.name as product_name, mt.is_free as is_free, c.name as campaign_name 
				from 
					".MM_TABLE_MEMBER_TYPES." mt
						LEFT JOIN ".MM_TABLE_MEMBER_TYPE_PRODUCTS." mtp ON mtp.member_type_id=mt.id  
						LEFT JOIN ".MM_TABLE_PRODUCTS." p ON mtp.product_id=p.id AND p.id NOT IN 
								(select ref_id from ".MM_TABLE_CORE_PAGES." cp where {$cpid_sql} and ref_type='product' and page_id!='{$page_id}' and ref_id>0)
						LEFT JOIN ".MM_TABLE_CAMPAIGNS." c on p.campaign_id=c.id  
				where
					mt.id NOT IN (select ref_id from ".MM_TABLE_CORE_PAGES." cp where {$cpid_sql} and ref_type='member_type' and page_id!='{$page_id}' and ref_id>0)
				
				UNION
				 
				select 
					'access_tag' as ref_type, at.id as type_id, at.name as type_name, p.id as product_id, p.name as product_name, at.is_free as is_free, c.name as campaign_name  
				from 
					".MM_TABLE_ACCESS_TAGS." at 
   						LEFT JOIN ".MM_TABLE_ACCESS_TAG_PRODUCTS." atp on at.id=atp.access_tag_id
   						LEFT JOIN ".MM_TABLE_PRODUCTS." p on atp.product_id=p.id AND p.id NOT IN 
   								(select ref_id from ".MM_TABLE_CORE_PAGES." cp where {$cpid_sql} and ref_type='product' and page_id!='{$page_id}' and ref_id>0)
   						LEFT JOIN ".MM_TABLE_CAMPAIGNS." c on p.campaign_id=c.id  
				where
					at.id NOT IN (select ref_id from ".MM_TABLE_CORE_PAGES." cp where {$cpid_sql} and ref_type='access_tag' and page_id!='{$page_id}' and ref_id>0)
		 ";
		
		return $wpdb->get_results($sql);
		
	}
	
	public static function getCorePageProductsList($corePageTypeId, $pageId)
	{
		global $wpdb;
		
		$cpid_sql = "core_page_type_id='{$corePageTypeId}'";
		
		if($corePageTypeId == MM_CorePageType::$PAID_CONFIRMATION)
		{
			$cpid_sql = "core_page_type_id IN ('".MM_CorePageType::$FREE_CONFIRMATION."','".MM_CorePageType::$PAID_CONFIRMATION."')";	
		}
		
		$sql = "select" .
				"	 'member_type' as ref_type, mt.id as type_id,mt.name as type_name, p.id as product_id, p.name as product_name, mt.is_free as is_free " .
				" from " .
				"	".MM_TABLE_MEMBER_TYPES." mt, ".MM_TABLE_PRODUCTS." p " .
				" where " .
				"	mt.product_id=p.id AND " .
				"	p.id NOT IN (select ref_id from ".MM_TABLE_CORE_PAGES." cp where {$cpid_sql} and ref_type='product' and page_id!='{$pageId}' and ref_id>0) " .
				" UNION " .
				"select" .
				"	 'access_tag' as ref_type, at.id as type_id, at.name as type_name, p.id as product_id, p.name as product_name, at.is_free as is_free " .
				" from " .
				"	".MM_TABLE_ACCESS_TAGS." at, ".MM_TABLE_ACCESS_TAG_PRODUCTS." atp, ".MM_TABLE_PRODUCTS." p " .
				" where " .
				"	at.id=atp.access_tag_id and atp.product_id=p.id AND " .
				"	p.id NOT IN (select ref_id from ".MM_TABLE_CORE_PAGES." cp where {$cpid_sql} and ref_type='product' and page_id!='{$pageId}' and ref_id>0) ";
				
		return $wpdb->get_results($sql);
	}
 	
	public function setName($str) 
	{
		$this->name = $str;
	}
	
	public function getName()
	{
		return $this->name;
	}

	public function deactivateProduct(){
		global $wpdb;
		
		$sql  ="update ".MM_TABLE_PRODUCTS." set status='0' where id='{$this->id}' limit 1";
		if($wpdb->query($sql) ===false){
			return false;
		}
		return true;
	}
	
	public function setCampaignId($str) 
	{
		$this->campaignId = $str;
	}
	
	public function getCampaignId()
	{
		return $this->campaignId;
	}
	
	public function setProductId($str) 
	{
		$this->productId = $str;
	}
	
	public function getProductId()
	{
		return $this->productId;
	}
	
	public function setSku($str) 
	{
		$this->sku = $str;
	}
	
	public function getSku()
	{
		return $this->sku;
	}
	
	public function setDescription($str) 
	{
		$this->description = $str;
	}
	
	public function getDescription()
	{
		return $this->description;
	}
	
	public function setPrice($str) 
	{
		$this->price = $str;
	}
	
	public function getPrice($doFormat=true)
	{
		if($doFormat) {
			return number_format(floatval($this->price), 2);
		}
		else {
			return $this->price;
		}
	}
	
	public function setCategoryName($str) 
	{
		$this->categoryName = $str;
	}
	
	public function getCategoryName()
	{
		return $this->categoryName;
	}
	
	public function setIsShippable($str) 
	{
		$this->isShippableInd = $str;
	}
	
	public function isShippable()
	{
		return $this->isShippableInd;
	}
	
	public function setIsTrial($str) 
	{
		$this->isTrialInd = $str;
	}
	
	public function isTrial()
	{
		return $this->isTrialInd;
	}
	
	public function setTrialAmount($str){
		$this->trialAmount = $str;
	}
	
	public function getTrialAmount($doFormat=true){
		if($doFormat) {
			return number_format(floatval($this->trialAmount), 2);
		}
		else {
			return $this->trialAmount;
		}
	}
	
	public function setTrialDuration($str){
		$this->trialDuration = $str;
	}
	
	public function getTrialDuration(){
		return $this->trialDuration;
	}
	
	public function setRebillPeriod($str) 
	{
		$this->rebillPeriod = $str;
	}
	
	public function getRebillPeriod()
	{
		return $this->rebillPeriod;
	}
	
	public function setRebillFrequency($str) 
	{
		$this->rebillFrequency = $str;
	}
	
	public function getRebillFrequency()
	{
		return $this->rebillFrequency;
	}
	
	public function setTrialFrequency($str) 
	{
		$this->trialFrequency = $str;
	}
	
	public function getTrialFrequency()
	{
		return $this->trialFrequency;
	}
	
	public function setPaymentId($str) 
	{
		$this->paymentId = $str;
	}
	
	public function getPaymentId()
	{
		return $this->paymentId;
	}
	
	public function setStatus($str){
		$this->status = $str;
	}
	
	public function getStatus()
	{
		return $this->status;
	}
	
	public function setRebillProductId($str) 
	{
		$this->rebillProductId = $str;
	}
	
	public function getRebillProductId()
	{
		return $this->rebillProductId;
	}
	
	public function getPricePerDay()
	{
		return floatval($this->price)/intval($this->rebillPeriod);
	}
	
	public function isRecurring($isLimeLight = true) 
	{
		if($isLimeLight){
			if($this->productId>0){
				return intval($this->rebillProductId) > 0;
			}
			return intval($this->rebillPeriod)>0;
		}
		else{
			return intval($this->rebillPeriod)>0;
		}
	}
	
	public function getRebillDescription()
	{
		$str = "";
		
		if($this->isRecurring())
		{
			$rebillDays = $this->rebillPeriod;
			
			if(intval($this->rebillPeriod) == 1) {
				$rebillDays .= " day";
			}
			else {
				$rebillDays .= " days";
			}
			
			if($this->id == $this->rebillProductId) {
				$str .= "rebills every ".$rebillDays;
			}
			else {
				$product = new MM_Product();
				$product->getProductByCampaign($this->rebillProductId, $this->campaignId);
				
				if($product->isValid()) {
					$str .= "rebills to <i>".$product->getName()."</i> after ".$rebillDays;
				}
				else {
					$str .= "rebills to <i>Lime Light product id ".$this->rebillProductId."</i> after ".$rebillDays;
				}
			}
		}
		
		return $str;
	}
}
?>