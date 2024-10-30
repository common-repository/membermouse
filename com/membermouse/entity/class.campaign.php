<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_Campaign extends MM_Entity
{	
	public static $SETTING_TYPE_SHIPPING = "shipping";
	public static $SETTING_TYPE_COUNTRY = "country";
	public static $SETTING_TYPE_PAYMENT = "payment";
	
	private $name = "";
	private $description = "";
	private $lastModifiedDate = "";
	private $products = "";
	public $rawData = "";
	
	public function getData() 
	{
		global $wpdb;
			
		$sql = "SELECT * FROM ".MM_TABLE_CAMPAIGNS." WHERE id='".$this->id."';";
		$result = $wpdb->get_row($sql);
			
		if($result) {
			$this->setData($result);
		}
		else {
			parent::invalidate();
			LogMe::write("MM_Campaign.getData(): error retrieving data for campaign with id of {$this->id}. Query run is ".$sql);
		}
	}
	
	public function setData($data)
	{
		try 
		{
			$this->name = $data->name;
			$this->description = $data->description;
			$this->lastModifiedDate = $data->last_modified;
			if(isset($data->product_ids)){
				$this->setProducts($data->product_ids);
			}
			
			parent::validate();
		}
		catch (Exception $ex) {
			parent::invalidate();
		}
	}
	
	private function setLocalProducts(){
		global $wpdb;
		
		$sql = "select id from ".MM_TABLE_PRODUCTS." where campaign_id='{$this->id}'";
		$rows = $wpdb->get_results($sql);
		if(is_array($rows)){
			$products = array();
			foreach($rows as $row){
				$products[$row->id] = $row->id;
			}
			$this->products =  $products;
		}
	}
	
	public function commitData()
	{	
		global $wpdb;
		
		if($this->campaignExists($this->id)) {
			$doUpdate = true;
		} else {
			$doUpdate = false;
		}
		
		MM_Transaction::begin();
		try
		{	
			if(!$doUpdate) 
			{
				$sql = "insert into ".MM_TABLE_CAMPAIGNS." set " .
			 			"	id='%d'," .
			 			"	name='%s'," .
			 			"	description='%s'" .
			 			"";
			}
			else 
			{	
				$sql = "update ".MM_TABLE_CAMPAIGNS." set " .
			 			"	id='%d'," .
		 				"	name='%s'," .
			 			"	description='%s', " .
			 			"	last_modified=NOW() where id='{$this->id}'" .
			 			"";
		 	}
		 	
		 	$preparedSql = $wpdb->prepare($sql, $this->id, $this->name, $this->description);
				LogMe::write("MM_Campaign.commitData() : ".$preparedSql);
		 	
			$result = $wpdb->query($preparedSql);
		 	
		 	if($result === false)
		 	{
		 		MM_Transaction::rollback();
		 		return new MM_Response("ERROR: unable to create campaign (".$preparedSql.")", MM_Response::$ERROR);
		 	}
		 	
		 	// set campaign settings
		 	$this->clearCampaignSettings();
		 	$this->addCampaignSettings(self::$SETTING_TYPE_SHIPPING);
		 	$this->addCampaignSettings(self::$SETTING_TYPE_COUNTRY);
		 	$this->addCampaignSettings(self::$SETTING_TYPE_PAYMENT);
		 	
LogMe::write("Campaign.commitData() syncing products ".$this->id." : ".json_encode($this->products));	
		 	MM_LimeLightService::syncProducts($this->id, $this->products);
		}
		catch(Exception $ex)
		{
LogMe::write("Campaign.commitData() rolling back : ".json_encode($ex));
		 	MM_Transaction::rollback();
	 		return new MM_Response("ERROR: unable to create campaign", MM_Response::$ERROR);
		}
		 
		MM_Transaction::commit();
	
		return new MM_Response();
	}
	
	public function activateProducts(){
		global $wpdb;
		
		$sql = "update ".MM_TABLE_PRODUCTS." set status='1' where campaign_id='{$this->id}'";
		if($wpdb->query($sql)===false){
			return false;
		}
		return true;
	}
	
	public function deactivateProducts(){
		global $wpdb;
		
		$sql = "update ".MM_TABLE_PRODUCTS." set status='0' where campaign_id='{$this->id}'";
		if($wpdb->query($sql)===false){
			return false;
		}
		return true;
	}
	
	public function getCampaignsInUse(){
		global $wpdb;
		$campaigns= "";
		$sql = "select id, campaign_id from ".MM_TABLE_PRODUCTS;
		$rows = $wpdb->get_results($sql);
		if(is_array($rows)){
			foreach($rows as $row){
				$product = new MM_Product($row->id);
				if($product->hasAssociation()){
					$campaigns.=$row->campaign_id.",";
				}
			}
		}
		return $campaigns;
	}
	
	private function addCampaignSettings($settingType)
	{
		global $wpdb;
		
		$sql = "insert into ".MM_TABLE_CAMPAIGN_SETTINGS." set " .
	 			"	campaign_id='%d'," .
	 			"	setting_type='%s'," .
	 			"	id='%s', " .
	 			"	name='%s'" .
				"";
		
		$idField = "";
		$nameField = "";
		
		switch($settingType) 
		{
			case self::$SETTING_TYPE_SHIPPING:
				$idField = "shipping_id";
				$nameField = "shipping_name";
				break;

			case self::$SETTING_TYPE_COUNTRY:
				$idField = "countries";
				$nameField = "";
				break;
				
			case self::$SETTING_TYPE_PAYMENT:
				$idField = "payment_name";
				$nameField = "";
				break;
		}
		
		$ids = array();
		$names = array();
		
		// this temporary code handles an extra comma added to the end of shipping 
		// data lists and because of this the list seems 1 item longer then it actually is
		// TODO LIMELIGHT remove this once Lime Light fixes the campaign_view call
		if($settingType == self::$SETTING_TYPE_SHIPPING) 
		{
			if(substr($this->rawData[$nameField], strlen($this->rawData[$nameField])-1, 1) == ",") {
				$this->rawData[$nameField] = substr($this->rawData[$nameField], 0, strlen($this->rawData[$nameField])-1);	
			}
		}
		
		if($idField != "") {
			$ids = explode(",", $this->rawData[$idField]);
		}
		
		if($nameField != "") {
			$names = explode(",", $this->rawData[$nameField]);
		}
		
		$listLength = (count($ids) > count($names)) ? count($ids) : count($names);
		
		for($i=0; $i<$listLength; $i++) 
		{
			$settingId = "";
			$settingName = "";
			
			switch($settingType) 
			{
				case self::$SETTING_TYPE_SHIPPING:
					$settingId = $ids[$i];
					$settingName = $names[$i];
					break;
	
				case self::$SETTING_TYPE_COUNTRY:
					$settingId = $ids[$i];
					$settingName = MM_LimeLightUtils::getCountryName($ids[$i]);
					break;
					
				case self::$SETTING_TYPE_PAYMENT:
					$settingId = $ids[$i];
					$settingName = MM_LimeLightUtils::getPaymentMethodName($ids[$i]);
					break;
			}
			
			$result = $wpdb->query($wpdb->prepare($sql, $this->id, $settingType, $settingId, $settingName));
			
			if($result === false) {
		 		MM_Transaction::rollback();
			}
		}
	}
	
	private function clearCampaignSettings()
	{
		global $wpdb;
		
		$sql = "delete from ".MM_TABLE_CAMPAIGN_SETTINGS." where campaign_id='%d'";
		$wpdb->query($wpdb->prepare($sql, $this->id));
	}
	
	private function campaignExists()
	{
		global $wpdb;
		
		$sql = "select count(*) as total from ".MM_TABLE_CAMPAIGNS." where id='%d' limit 1";
		$result = $wpdb->get_row($wpdb->prepare($sql, $this->id));
		
		return !$result || $result->total > 0;
	}
	
	public function getSettingsList($settingType)
 	{
 		global $wpdb;
 		
 		$sql = "select id, name from ".MM_TABLE_CAMPAIGN_SETTINGS." where setting_type='%s' AND campaign_id='%d'";
		$rows = $wpdb->get_results($wpdb->prepare($sql, $settingType, $this->id));
		
		$list = array();
		
		foreach($rows as $row)
		{
			$list[$row->id] = $row->name;
		}
		
		return $list;
 	}
	
	public function setName($str) 
	{
		$this->name = $str;
	}
	
	public function getName()
	{
		return $this->name;
	}
 	
	public function setDescription($str) 
	{
		$this->description = $str;
	}
	
	public function getDescription()
	{
		return $this->description;
	}
	
	public function getLastModifiedDate($doFormat=true)
	{
		if($doFormat) {
			return date("M d, Y g:i a", strtotime($this->lastModifiedDate));
		} 
		else {
			return $this->lastModifiedDate;
		}
	}
 	
	public function setProducts($str) 
	{
		$this->products = $str;
	}
	
	public function getProducts()
	{
		return $this->products;
	}
	
}
?>
