<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_CorePage extends MM_Entity
{	
	public static $EVENT_DELETE_BY_TYPE = 1;
	public static $EVENT_DELETE_TYPE_BY_PAGE = 2;
	public static $EVENT_DELETE_ALL_BY_PAGE = 3; 
	
	private $pageId;
	private $corePageType;
	private $refType;
	private $refId;
	
	public function getData() 
	{	
		global $wpdb;
		
		$sql = "SELECT * FROM ".MM_TABLE_CORE_PAGES." cp WHERE id='".$this->id."';";
		$result = $wpdb->get_row($sql);
		
		if($result) {
			$this->setData($result);
		}
		else {
			parent::invalidate();
			LogMe::write("MM_CorePage.getData(): error retrieving data for email account with id of {$this->id}. Query run is ".$sql);
		}
	}
	
	public function setData($data)
	{
		try
		{
			$this->pageId = $data->page_id;
			$this->corePageType = $data->core_page_type;
			$this->refType = $data->ref_type;
			$this->refId = $data->ref_id;
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
				$sql = "insert into ".MM_TABLE_CORE_PAGES." set " .
		 			"	page_id='%d'," .
		 			"	core_page_type_id='%d'," .
		 			"	ref_type='%s'," .
		 			"	ref_id='%d'" .
		 			"";
			}
			else 
			{	
				$sql = "update ".MM_TABLE_CORE_PAGES." set " .
		 			"	page_id='%d'," .
		 			"	core_page_type_id='%d'," .
		 			"	ref_type='%s'," .
		 			"	ref_id='%d'" .
					" where id='{$this->id}'" .
			 			"";
		 	}
		 	
		 	$preparedSql = $wpdb->prepare($sql, $this->pageId, $this->corePageType->getId(), $this->refType, $this->refId);
		 	
		 	$result = $wpdb->query($preparedSql);
		 	
			if($result === false)
		 	{
		 		MM_Transaction::rollback();
		 		return new MM_Response("ERROR: unable to create core page (".$preparedSql.")", MM_Response::$ERROR);
		 	}
		 	
		 	if(!$doUpdate) {
		 		$this->id = $wpdb->insert_id;
		 	}
		}
		catch(Exception $ex)
		{
		 	MM_Transaction::rollback();
	 		return new MM_Response("ERROR: unable to create core page", MM_Response::$ERROR);
		}
		 
		MM_Transaction::commit();
	
		return new MM_Response();
	}
	
	public function delete()
	{	
		global $wpdb;
		
		$sql = "DELETE FROM ".MM_TABLE_CORE_PAGES." WHERE id='%d' limit 1";
		$results = $wpdb->query($wpdb->prepare($sql, $this->id));
			
		if($results) {
			return true;
		}
		
		return false;
	}
	
	public static function hasDefaultCorePage($corePageTypeId, $postId)
	{
		global $wpdb;
		$sql = "select count(*) as total from ".MM_TABLE_CORE_PAGES." where core_page_type_id='{$corePageTypeId}' and page_id!='{$postId}' and ref_id IS NULL and ref_type IS NULL";
		$row = $wpdb->get_row($sql);
		if($row->total>0)
			return true;
		
		return false;
	}
	
	public static function isDefaultCorePage($pageId)
	{
		global $wpdb;
		$sql = "select count(*) as total from ".MM_TABLE_CORE_PAGES." where page_id='{$pageId}' and ref_type IS NULL and ref_id IS NULL";

		$row = $wpdb->get_row($sql);
		if($row->total>0)
			return true;
		
		return false;
		
	}
	
	public function setReferencePages($referenceIds)
	{
		$this->deleteExistingReferences(self::$EVENT_DELETE_ALL_BY_PAGE);
		
		for($i=0; $i<count($referenceIds); $i++)
		{
			$corePage = new MM_CorePage();
			$corePage->setPageId($this->pageId);
			
			$id = $referenceIds[$i];
			$refType = $this->refType;
			if(preg_match("/(\-)/", $referenceIds[$i]))
			{
				$id_arr = explode("-",$referenceIds[$i]);
				$id = $id_arr[0];
				$refType = $id_arr[1];
			}
			
			$corePage->setRefId($id);
			$corePage->setRefType($refType);
			$corePage->setCorePageTypeId($this->corePageType->getId());
			if(!$corePage->commitData())
			{
				return false;
			}
		}
		return true;
	}
	
	public function setAsDefaultPage()
	{
		global $wpdb;
		
		$this->deleteExistingReferences(MM_CorePage::$EVENT_DELETE_TYPE_BY_PAGE); //$postId, $corePageTypeId
		
		$sql = "update ".MM_TABLE_CORE_PAGES." set page_id='{$this->pageId}' where core_page_type_id='".$this->corePageType->getId()."' and ref_type IS NULL and ref_id IS NULL limit 1";

		$ret = $wpdb->query($sql);
		if($ret===false)
			return false;
			
		return true;
	}
	
	public static function getAvailableWPPages()
	{
		global $wpdb;
		$sql = "select id, post_title from {$wpdb->posts} p where p.post_type='page' and p.post_status IN ('publish','draft') and p.id NOT IN (select page_id from ".MM_TABLE_CORE_PAGES." c where page_id IS NOT NULL)";
		return $wpdb->get_results($sql);
	}
	
	public function removeCorePage()
	{
		global $wpdb;
		
		///delete specific pages
		$this->deleteExistingReferences(MM_CorePage::$EVENT_DELETE_ALL_BY_PAGE);
		
		if(intval($this->pageId)>0)
		{
			$sql = "update ".MM_TABLE_CORE_PAGES." set page_id = NULL where page_id='{$this->pageId}' limit 1";
			$wpdb->query($sql);
		}
	}
	
	public static function getDefaultCorePageSettingsById($page_id)
	{
		global $wpdb;
		$sql = "select * from ".MM_TABLE_CORE_PAGES. " cp " .
				"		where " .
				"			cp.page_id='{$page_id}' and ref_type IS NULL and ref_id IS NULL limit 1" ;
		return $wpdb->get_row($sql);
	}

	public static function getCorePageInfo($page_id)
	{
		global $wpdb;
		$sql = "select cp.*, group_concat(cp.ref_id SEPARATOR ',') as refs, group_concat(cp.ref_type SEPARATOR ',') as ref_types,  cpt.name as core_page_type_name from ".MM_TABLE_CORE_PAGES. " cp, ".MM_TABLE_CORE_PAGE_TYPES." cpt " .
				"		where " .
				"			cp.page_id='{$page_id}' 	 
							and cp.core_page_type_id=cpt.id
				group by cp.core_page_type_id" ;
	
		return $wpdb->get_row($sql);
	}
	
	public static function getCorePageSettingsByPageID($page_id)
	{
		global $wpdb;
		$sql = "select * from ".MM_TABLE_CORE_PAGES. " cp " .
				"		where " .
				"			cp.page_id='{$page_id}' " ;
		return $wpdb->get_results($sql);
	}
	
	public static function getCorePagesByPageID($page_id)
	{
		global $wpdb;
		$sql = "select cp.*, mt.name as mt_name, at.name as at_name
					 from ".MM_TABLE_CORE_PAGES. " cp 
					 		LEFT JOIN ".MM_TABLE_MEMBER_TYPES." mt on cp.ref_type='member_type' and cp.ref_id=mt.id
					 		LEFT JOIN ".MM_TABLE_ACCESS_TAGS." at on cp.ref_type='access_tag' and cp.ref_id=at.id
					 " .
				"		where " .
				"			cp.page_id='{$page_id}' " ;
		return $wpdb->get_results($sql);
	}
	
	public static function isAvailable($corePageTypeId, $pageId)
	{
		switch($corePageTypeId)
		{
			case MM_CorePageType::$LOGIN_PAGE:
			case MM_CorePageType::$FORGOT_PASSWORD:
			case MM_CorePageType::$REGISTRATION:
			case MM_CorePageType::$LIMELIGHT_SUCCESS:
			case MM_CorePageType::$MY_ACCOUNT:
				return false;
				
			case MM_CorePageType::$LOGOUT_PAGE:
			case MM_CorePageType::$MEMBER_HOME_PAGE:
				$member_obj = new MM_MemberType();
				$rows = $member_obj->getAvailableTypes($corePageTypeId, $pageId);
				if(!$rows)
					return false;
				return true;
				
			case MM_CorePageType::$PAID_CONFIRMATION:
			case MM_CorePageType::$CANCELLATION:
				$products = new MM_Product();
				$rows = $products->getProductsAndAssociations($corePageTypeId, $pageId);
				if(!$rows)
				{
					return false;
				}
				return true;
				
			case MM_CorePageType::$ERROR:
				$rows = MM_ErrorType::getAvailableErrorsForPage($pageId);
				if(!$rows)
					return false;
					
				return true;
		}
		return true;
	}
	
	public static function getDefaultPages()
	{
		global $wpdb, $post;
		$sql = "select " .
				"	* " .
				"	from " .
				"		".MM_TABLE_CORE_PAGE_TYPES." cp " .
				"	where " .
				"		visible='1' and " .
				"		cp.id IN (select core_page_type_id from ".MM_TABLE_CORE_PAGES." c )";
		$rows = $wpdb->get_results($sql);
		if(!$rows)
			return array();
			
		return $rows;
	}
	
	public static function getDefaultCorePageByType($type_id)
	{
		global $wpdb;
		$sql = "select page_id from ".MM_TABLE_CORE_PAGES. " cp, ".MM_TABLE_CORE_PAGE_TYPES." t " .
				"		where " .
				"			cp.ref_type IS NULL and " .
				"			cp.ref_id IS NULL and " .
				"			cp.core_page_type_id='{$type_id}' group by page_id" ;
		
		return $wpdb->get_row($sql);
	}
	
	public static function getAllReferencesToCorePageType($corePageTypeId)
	{
		global $wpdb;
		
		$sql = "select page_id,ref_type,ref_id from ".MM_TABLE_CORE_PAGES." where core_page_type_id='{$corePageTypeId}' and page_id>0 and page_id IN (select ID from {$wpdb->posts} where post_status IN ('publish','draft') and post_type='page') order by ref_id desc";  //and ref_type='{$refType}' and ref_id='{$refId}'
		
		$rows = $wpdb->get_results($sql);
		if($rows === false)
			return 0;
		
		return $rows;
	}
	
	public static function getAssociatedWPCorePages($page_id, $corePageTypeId=null)
	{
		global $wpdb;
		$ext_sql ="";
		if(!is_null($corePageTypeId))
		{
			$ext_sql = " AND c.core_page_type_id='{$corePageTypeId}' ";
		}
		$sql= "select c.* from {$wpdb->posts} p, ".MM_TABLE_CORE_PAGES." c where p.ID='{$page_id}' and p.ID=c.page_id {$ext_sql}";
		return $wpdb->get_results($sql);
	}
	
	public static function getCorePage($page_id)
	{
		global $wpdb;
		$sql = "select core_page_type_id from ".MM_TABLE_CORE_PAGES." where page_id='{$page_id}'";	
		return $wpdb->get_row($sql);
	}
	
	private function deleteExistingReferences($deleteEventType)
	{
		global $wpdb;
		
		$results =  false;
		switch($deleteEventType)
		{
			case self::$EVENT_DELETE_BY_TYPE:
				$sql = "DELETE " .
					"	FROM ".MM_TABLE_CORE_PAGES." " .
					"	WHERE " .
					"		core_page_type_id='%d' AND " .
					"		ref_id='%d' AND " .
					"		ref_type='%s' ";
				$results = $wpdb->query($wpdb->prepare($sql, $this->corePageType->getId(),$this->refId, $this->refType));
			break;
			
			case self::$EVENT_DELETE_TYPE_BY_PAGE:
			
				$sql = "delete from ".MM_TABLE_CORE_PAGES." where core_page_type_id='%d' and page_id='%d' and ref_type IS NOT NULL and ref_id IS NOT NULL";
				$results = $wpdb->query($wpdb->prepare($sql,  $this->corePageType->id,$this->pageId));
				
			break;
			
			case self::$EVENT_DELETE_ALL_BY_PAGE:
			
				$sql = "delete from ".MM_TABLE_CORE_PAGES." where page_id='{$this->pageId}' and ref_type IN ('member_type','error_type','access_tag','product')";
				$wpdb->query($sql);
				
			break;
		}
		
		if($results) {
			return true;
		}
		
		return false;
	}
	
	public function setPageId($str) 
	{
		$this->pageId = $str;
	}
	
	public function getPageId()
	{
		return $this->pageId;
	}
	
	public function setCorePageTypeId($id)
	{
		$this->corePageType = new MM_CorePageType($id);
	}
	
	public function setCorePageType(MM_CorePageType $cpt) 
	{
		$this->corePageType = $cpt;
	}
	
	public function getCorePageType()
	{
		return $this->corePageType;
	}
	
	public function setRefId($str) 
	{
		$this->refId = $str;
	}
	
	public function getRefId()
	{
		return $this->refId;
	}
	
	public function setRefType($str) 
	{
		$this->refType = $str;
	}
	
	public function getRefType()
	{
		return $this->refType;
	}
	
}
?>