<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_AccountType extends MM_Entity
{	
	public static $MM_UNLIMITED = "[unlimited]";
	
	private $name = "";
	private $numSites = "";
	private $numPaidMembers = "";
	private $numTotalMembers = "";
	private $unlimitedPaidMembers = "0";
	private $unlimitedTotalMembers = "0";
	private $status = "1";
	
	public function getData() 
	{
		global $wpdb;
		
		$sql = "SELECT * FROM ".MM_TABLE_ACCOUNT_TYPES." WHERE id='".$this->id."';";
		$result = $wpdb->get_row($sql);
		
		if($result) {
			$this->setData($result);
		}
		else {
			parent::invalidate();
			LogMe::write("MM_AccountType.getData(): error retrieving data for account type with id of {$this->id}. Query run is ".$sql);
		}
	}
	
	public function setData($data)
	{
		try 
		{
			$this->name = $data->name;
			$this->status = $data->status;
			$this->numSites = $data->num_sites;
			$this->numPaidMembers = $data->num_paid_members;
			$this->numTotalMembers = $data->num_total_members;
			$this->unlimitedPaidMembers = $data->unlimited_paid_members;
			$this->unlimitedTotalMembers = $data->unlimited_total_members;
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
				$sql = "insert into ".MM_TABLE_ACCOUNT_TYPES." set " .
		 			"	name='%s'," .
		 			"	status='%d'," .
		 			"	num_sites='%d'," .
		 			"	num_paid_members='%d'," .
		 			"	unlimited_paid_members='%d'," .
		 			"	num_total_members='%d'," .
		 			"	unlimited_total_members='%d'" .
		 			"";
			}
			else 
			{	
				$sql = "update ".MM_TABLE_ACCOUNT_TYPES." set " .
	 				"	name='%s'," .
		 			"	status='%d'," .
		 			"	num_sites='%d'," .
		 			"	num_paid_members='%d'," .
		 			"	unlimited_paid_members='%d'," .
		 			"	num_total_members='%d'," .
		 			"	unlimited_total_members='%d' where id='{$this->id}'" .
		 			"";
		 	}
		 	
		 	$preparedSql = $wpdb->prepare($sql, $this->name, $this->status, $this->numSites, $this->numPaidMembers, $this->unlimitedPaidMembers,
												$this->numTotalMembers, $this->unlimitedTotalMembers);
		 	
		 	$result = $wpdb->query($preparedSql);

			if($result === false) {
				MM_Transaction::rollback();
		 		return new MM_Response("ERROR: unable to create account type (".$preparedSql.")", MM_Response::$ERROR);
			}
		 	
		 	if(!$doUpdate) {
		 		$this->id = $wpdb->insert_id;
		 	}
		}
		catch(Exception $ex)
		{
		 	MM_Transaction::rollback();
	 		return new MM_Response("ERROR: unable to create account type", MM_Response::$ERROR);
		}
		 
		MM_Transaction::commit();
	
		return new MM_Response();
	}
	
	public function delete()
	{	
		global $wpdb;
		
		if(!$this->hasAssociations())
		{
			$sql = "DELETE FROM ".MM_TABLE_ACCOUNT_TYPES." WHERE id='%d' LIMIT 1";
			$results = $wpdb->query($wpdb->prepare($sql, $this->id));
			
			if($results) {
				return true;
			}
		}
		
		return false;
	}
	
	public function hasAssociations()
	{
		global $wpdb;
		
		// check if account type is associated with one or more member types
		$sql = "select count(*) as total from ".MM_TABLE_ACCOUNT_MEMBER_TYPES." where account_type_id='{$this->id}'";
		$row = $wpdb->get_row($sql);
		
		if($row->total > 0) {
			return true;
		}
		
		return false;
	}
	
	public static function getAccountTypesList()
 	{
 		global $wpdb;
 		
 		$types = array();
 		
 		$sql = "select * from ".MM_TABLE_ACCOUNT_TYPES;
 		$rows = $wpdb->get_results($sql);
 		
 		if($rows)
 		{
	 		foreach($rows as $row)
			{
				$paid = "";
				$total = "";
				
				if($row->num_paid_members == "0") {
					$paid = "unlimited";
				}
				else {
					$paid = number_format($row->num_paid_members);
				}
				
				if($row->num_total_members == "0") {
					$total = "unlimited";
				}
				else {
					$total = number_format($row->num_total_members);
				}
				$types[$row->id] = $row->name." [".$row->num_sites." sites/".$paid." paid/".$total." total]";
			}
 		}
 			
 		return $types;
 	}
 	
	public function setName($str) 
	{
		$this->name = $str;
	}
	
	public function getName()
	{
		return $this->name;
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
	
	public function setNumSites($str) 
	{
		$this->numSites = $str;
	}
	
	public function getNumSites()
	{
		return $this->numSites;
	}
	
	public function setNumPaidMembers($str) 
	{
		$this->numPaidMembers = $str;
	}
	
	public function getNumPaidMembers()
	{
		return $this->numPaidMembers;
	}
	
	public function getNumPaidMembersStr()
	{
		if($this->unlimitedPaidMembers == "1") {
	    	return self::$MM_UNLIMITED;
	    }
	    else {
	    	return number_format($this->numPaidMembers);
	    }
	}
	
	public function setNumTotalMembers($str) 
	{
		$this->numTotalMembers = $str;
	}
	
	public function getNumTotalMembers()
	{
		return $this->numTotalMembers;
	}
	
	public function getNumTotalMembersStr()
	{
		if($this->unlimitedTotalMembers == "1") {
	    	return self::$MM_UNLIMITED;
	    }
	    else {
	    	return number_format($this->numTotalMembers);
	    }
	}
	
	public function setUnlimitedPaidMembers($str) 
	{
		$this->unlimitedPaidMembers = $str;
	}
	
	public function getUnlimitedPaidMembers()
	{
		return $this->unlimitedPaidMembers;
	}
	
	public function setUnlimitedTotalMembers($str) 
	{
		$this->unlimitedTotalMembers = $str;
	}
	
	public function getUnlimitedTotalMembers()
	{
		return $this->unlimitedTotalMembers;
	}
}
?>
