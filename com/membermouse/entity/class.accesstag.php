<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_AccessTag extends MM_Entity
{	
	private $name = "";
	private $isFreeInd = "1";
	private $description = "";
	private $status = "1";
	private $badgeUrl = "";
	private $products = "";
	
	public function getData() 
	{
		global $wpdb;
		
		$sql = "SELECT * FROM ".MM_TABLE_ACCESS_TAGS." WHERE id='".$this->id."';";
		$result = $wpdb->get_row($sql);
		
		if($result) {
			$this->setData($result);
		}
		else {
			parent::invalidate();
			LogMe::write("MM_AccessTag.getData(): error retrieving data for access tag with id of {$this->id}. Query run is ".$sql);
		}
	}
	
	public function setData($data)
	{
		try 
		{
			$this->name = $data->name;
			$this->isFreeInd = $data->is_free;
			$this->description = $data->description;
			$this->status = $data->status;
			$this->badgeUrl = $data->badge_url;
			parent::validate();
		}
		catch (Exception $ex) {
			parent::invalidate();
		}
	}
	
	public function commitData()
	{	
		global $wpdb;
		
		$doUpdate = isset($this->id) && $this->id != "" && intval($this->id) > 0;
		 
		MM_Transaction::begin();
		try
		{	
			if(!$doUpdate) 
			{
				$sql = "insert into ".MM_TABLE_ACCESS_TAGS." set " .
			 			"	name='%s'," .
			 			"	status='%d'," .
			 			"	is_free='%d'," .
			 			"	description='%s'," .
			 			"	badge_url='%s'" .
			 			"";
			}
			else 
			{	
				$sql = "update ".MM_TABLE_ACCESS_TAGS." set " .
			 			"	name = '%s'," .
			 			"	status='%d'," .
		 				"	is_free='%d'," .
		 				"	description='%s'," .
			 			"	badge_url='%s' where id='{$this->id}'" .
			 			"";
		 	}
		 	
		 	$preparedSql = $wpdb->prepare($sql, $this->name, $this->status, $this->isFreeInd, $this->description, $this->badgeUrl);
		 	
		 	$result = $wpdb->query($preparedSql);
		 	
			if($result === false)
		 	{
		 		MM_Transaction::rollback();
		 		$response->type = MM_Response::$ERROR;
		 		return new MM_Response("ERROR: unable to create access tag (".$preparedSql.")", MM_Response::$ERROR);
		 	}
		 	
		 	if(!$doUpdate) {
		 		$this->id = $wpdb->insert_id;
		 	}
		 	
		 	if($this->isFree()) {
		 		$this->removeProducts();
		 	} 
		 	else {
		 		$this->addProducts();
		 	}
		 	global $mmSite;
			$campaignsInUse = MM_Campaign::getCampaignsInUse();
			MM_MemberMouseService::updateCampaignUsage($mmSite->getId(),$campaignsInUse);
		}
		catch(Exception $ex)
		{
		 	MM_Transaction::rollback();
	 		return new MM_Response("ERROR: unable to create access tag", MM_Response::$ERROR);
		}
		 
		MM_Transaction::commit();
		
		return new MM_Response();
	}

	public static function IsRefundedForUser($accessTagId, $userId){
		global $wpdb;
		
		$sql = "select count(*) as total from ".MM_TABLE_APPLIED_ACCESS_TAGS." where is_refunded='1' and access_tag_id='{$accessTagId}' and access_type='user' and ref_id='{$userId}'";
		$ret = $wpdb->get_row($sql);
		if($ret->total>0){
			return true;
		}
		return false;
	}
	
	public static function setRefundedForUser($orderId, $userId){
		global $wpdb;
		
		$sql = "update ".MM_TABLE_APPLIED_ACCESS_TAGS." set is_refunded='1', status='0' where order_id='{$orderId}' and access_type='user' and ref_id='{$userId}' limit 1";
		$wpdb->query($sql);
	}
	
	public static function setNotRefundedForUser($orderId, $userId){
		global $wpdb;
		
		$sql = "update ".MM_TABLE_APPLIED_ACCESS_TAGS." set is_refunded='0', status='1' where order_id='{$orderId}' and access_type='user' and ref_id='{$userId}' limit 1";
		$wpdb->query($sql);
	}
	
	public function getAppliedTagOrderId($userId){
		global $wpdb;
		
		$sql = "select order_id from ".MM_TABLE_APPLIED_ACCESS_TAGS." where  access_tag_id='{$this->id}' and access_type='user' and ref_id='{$userId}'";
		$ret = $wpdb->get_row($sql);
		return (isset($ret->order_id))?$ret->order_id:"0";
	}
	
	public static function getAppliedTagByOrderId($orderId){
		global $wpdb;
		$sql = "select access_tag_id from ".MM_TABLE_APPLIED_ACCESS_TAGS." where  order_id='{$orderId}'";
		$rows = $wpdb->get_results($sql);
		if(is_array($rows)){
			return $rows;
		}
		return array();
	}
	
	private function addProducts()
	{
		global $wpdb;
		
		$this->products = (!is_array($this->products)) ? array($this->products) : $this->products;
		
		$this->removeProducts();
		
		if(!empty($this->products) && count($this->products) > 0) 
		{
			foreach($this->products as $productId)
			{
				if($productId != "null") 
				{
					$sql = "insert into ".MM_TABLE_ACCESS_TAG_PRODUCTS." set " .
							"	access_tag_id='{$this->id}'," .
							"	product_id='{$productId}'  ";
							
					$wpdb->query($sql);
				}
			}
		}
	}
	
	private function removeProducts() 
	{
		global $wpdb;
		
		if(isset($this->id)) 
		{
			$sql = "delete from ".MM_TABLE_ACCESS_TAG_PRODUCTS." where access_tag_id='{$this->id}'";
			$wpdb->query($sql);
		}
		
		return true;
	}
	
	public function delete()
	{	
		global $wpdb;
		
		if(!$this->hasAssociations())
		{
			$sql = "DELETE FROM ".MM_TABLE_ACCESS_TAGS." WHERE id='%d' LIMIT 1";
			$results = $wpdb->query($wpdb->prepare($sql, $this->id));
			
			// remove product relationships
			$this->removeProducts();
			
			if($results) {
				return true;
			}
		}
		
		return false;
	}
	
	public function hasAssociations()
	{
		global $wpdb;
		
		// check if access tag is associated with one or more member types
		$sql = "select count(*) as total from ".MM_TABLE_APPLIED_ACCESS_TAGS." where access_type='member_type' and access_tag_id='{$this->id}'";
		$row = $wpdb->get_row($sql);
		
		if($row->total > 0) {
			return true;
		}
		
		if($this->hasSubscribers()) {
			return true;
		}
		
		// check if access tag is associated with one or more posts
		$sql = "select count(*) as total from ".MM_TABLE_POSTS_ACCESS." where access_type='access_tag' and access_id='{$this->id}'";
		$row = $wpdb->get_row($sql);
		
		if($row->total > 0) {
			return true;
		}
		
		return false;
	}
	
	public function hasSubscribers()
	{
		global $wpdb;
		
		// check if access tag is associated with one or more users
		if($this->isValid())
		{
			$sql = "select count(*) as total from ".MM_TABLE_APPLIED_ACCESS_TAGS." where access_type='user' and access_tag_id='{$this->id}' and status='1' and is_refunded='0'";
			$row = $wpdb->get_row($sql);
			
			if($row->total > 0) {
				return true;
			}
		}
		
		return false;
	}
	
	public function getAssociatedProducts()
	{	
		if($this->products == "")
		{
			global $wpdb;
			
			$sql = "select p.* from ".MM_TABLE_ACCESS_TAG_PRODUCTS." aat, ".MM_TABLE_PRODUCTS." p where aat.access_tag_id='{$this->id}' and aat.product_id=p.id";
			$rows = $wpdb->get_results($sql);
			
			$products = array();
			
			if($rows)
			{
				foreach($rows as $row) {
					$products[$row->id] = $row->name;
				}
			}
			
			$this->products = $products;
		}
		
		return $this->products;
	}

	public static function getAccessTagsPostAccess()
	{
		global $wpdb;
 		$sql = "SELECT at.id, at.name FROM ".MM_TABLE_ACCESS_TAGS." at, ".MM_TABLE_POSTS_ACCESS." pa 
 					WHERE 
 						at.status ='1' and 
 						at.id = pa.access_id and 
 						pa.access_type='access_tag'
 						";
 		$rows = $wpdb->get_results($sql);
 		
 		$tags = array();
 		if($rows===false)
 		{
 			return $tags;
 		}
 		if($rows) 
 		{
	 		foreach($rows as $row)
			{
				$tags[$row->id] = $row->name;
			}
 		}
 		
 		return $tags;
	}
	
	public static function getAccessTagsList($activeStatusOnly=false)
	{
		global $wpdb;
 		
 		$tags = array();
 		
 		if($activeStatusOnly) {
 			$sql = "select * from ".MM_TABLE_ACCESS_TAGS." where status ='1'";
 		}
 		else {
 			$sql = "select * from ".MM_TABLE_ACCESS_TAGS;
 		}
 		
 		$rows = $wpdb->get_results($sql);
 		
 		if($rows) 
 		{
	 		foreach($rows as $row)
			{
				$tags[$row->id] = $row->name;
			}
 		}
 			
 		return $tags;
	}
	
	public function setName($str) 
	{
		$this->name = $str;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function setIsFree($str) 
	{
		$this->isFreeInd = $str;
	}
	
	public function isFree()
	{
		if($this->isFreeInd == "1") {
			return true;
		}
		else {
			return false;
		}
	}
	
	public function setDescription($str) 
	{
		$this->description = $str;
	}
	
	public function getDescription()
	{
		return $this->description;
	}
	
	public function setStatus($str) 
	{
		$this->status = $str;
	}
	
	public function getStatus()
	{
		return $this->status;
	}
	
	public function getStatusName()
	{
		return MM_MemberStatus::getName($this->status);
	}
	
	public function setBadgeUrl($str) 
	{
		$this->badgeUrl = $str;
	}
	
	public function getBadgeUrl()
	{
		return $this->badgeUrl;
	}
	
	public function setProducts($str) 
	{
		$this->products = $str;
	}
}
?>
