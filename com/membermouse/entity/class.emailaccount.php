<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_EmailAccount extends MM_Entity
{	
	private $displayName = "";
	
	private $name = "";
	private $username = "";
	private $password = "";
	private $phone = "";
	private $roleId = 0;
	private $userId = 0;
	
	
	private $address = "";
	
	private $isDefaultInd = "0";
	private $status = "0";
	
	public function getData() 
	{
		global $wpdb;
		
		$sql = "SELECT acc.*, u.user_login as username, u.user_pass as password, u.ID as user_id FROM ".MM_TABLE_EMAIL_ACCOUNTS." acc 
						LEFT JOIN ".$wpdb->users." u on acc.user_id = u.ID 
					WHERE acc.id='".$this->id."';";
		$result = $wpdb->get_row($sql);
		
		if($result) {
			$this->setData($result);
		}
		else {
			parent::invalidate();
			LogMe::write("MM_EmailAccount.getData(): error retrieving data for email account with id of {$this->id}. Query run is ".$sql);
		}
	}
	
	public function getDefault()
	{
		global $wpdb;
		
		$sql = "SELECT acc.*, u.user_login as username, u.user_pass as password, u.ID as user_id
				FROM 
					".MM_TABLE_EMAIL_ACCOUNTS." acc 
						LEFT JOIN ".$wpdb->users." u on acc.user_id = u.ID
				WHERE acc.is_default='1' LIMIT 1;";
		$result = $wpdb->get_row($sql);
		
		if($result) {
			$this->setData($result);
		}
		else {
			parent::invalidate();
		}
	}
	
	public function setData($data)
	{
		try 
		{
			$this->id = $data->id;
			$this->displayName = $data->name;
			$this->status = $data->status;
			$this->address = $data->email;
			
			$this->name = (isset($data->fullname))?$data->fullname:"";
			$this->phone = (isset($data->phone))?$data->phone:"";
			$this->username = (isset($data->username))?$data->username:"";
			$this->password = (isset($data->password))?$data->password:"";
			$this->roleId = $data->role_id;
			$this->userId = $data->user_id;
			
			$this->isDefaultInd = $data->is_default;
			LogMe::write("EmailAccount.setData() : valid : ".json_encode($data));
			parent::validate();
		}
		catch (Exception $ex) {
			LogMe::write("EmailAccount.setData() : invalid : ".json_encode($data));
			parent::invalidate();
		}
	}
	
	public function commitData()
	{	
		global $wpdb;
		
		$doUpdate = isset($this->id) && $this->id != "" && intval($this->id) > 0;
		 
		$emailExists = $this->emailExists($doUpdate);
		
		if($emailExists->type == MM_Response::$ERROR) {
			return $emailExists;
		}
		
		MM_Transaction::begin();
		try
		{	
			if(intval($this->userId)<=0){
				$user = new MM_User($this->userId);
				if(!$user->isValid()){
					$user->setEmail($this->address);
					$user->getDataByEmail();
				}
				$this->userId = $user->getId();
			}
			
		 	if($this->userId<=0){
				$nameArr = explode(" ", $this->name);
				$userData = array(
					  'user_pass' =>$this->password,
					  'user_login' =>$this->username,
					  'user_email'  =>$this->address,
					  'display_name'  =>$this->displayName,
					  'first_name'  =>array_shift($nameArr),
					  'last_name'  =>array_pop($nameArr),
					  'description'  =>'From Employee Accounts',
					  'user_registered'  =>Date("Y-m-d h:i:s"),
					  'role'  =>'administrator',
				);
			//	var_dump($userData);
				$this->userId = wp_insert_user($userData);
				if($this->userId instanceof WP_Error){
				 		MM_Transaction::rollback();
				 		return new MM_Response("ERROR: unable to create email account", MM_Response::$ERROR);
				}
				add_user_meta($this->userId,"wp_user_level","10");
				
		 	}
		 	else{
				delete_user_meta($this->userId, "wp_capabilities");
				delete_user_meta($this->userId, "wp_user_level");
				$nameArr = explode(" ", $this->name);
				$userData = array(
					  'ID'=>$this->userId,
					  'user_login' =>$this->username,
					  'user_email'  =>$this->address,
					  'display_name'  =>$this->displayName,
					  'first_name'  =>array_shift($nameArr),
					  'last_name'  =>array_pop($nameArr),
					  'user_pass' =>$this->password,
					  'role'  =>'administrator',
				);
		 		$this->userId = wp_update_user($userData);
				if($this->userId instanceof WP_Error){
				 		MM_Transaction::rollback();
				 		return new MM_Response("ERROR: unable to create email account", MM_Response::$ERROR);
				}
				update_user_meta($this->userId,"wp_user_level","10");
		 	}
		 	
			if(!$doUpdate) 
			{
				$sql = "insert into ".MM_TABLE_EMAIL_ACCOUNTS." set " .
		 			"	name='%s'," .
		 			"	fullname='%s'," .
		 			"	role_id='%d'," .
		 			"	user_id='%d'," .
		 			"	email='%s'," .
		 			"	phone='%s'," .
		 			"	is_default='%d'," .
		 			"	status='%d'" .
		 			"";
				
			}
			else 
			{	
				$sql = "update ".MM_TABLE_EMAIL_ACCOUNTS." set " .
		 				"	name='%s'," .
		 			"	fullname='%s'," .
		 			"	role_id='%d'," .
		 			"	user_id='%d'," .
		 				"	email='%s'," .
		 			"	phone='%s'," .
		 				"	is_default='%d'," .
			 			"	status='%d' where id='{$this->id}'" .
			 			"";
		 	}
		 	
		 	$preparedSql = $wpdb->prepare($sql, $this->displayName, $this->name, $this->roleId, $this->userId, $this->address, $this->phone,$this->isDefaultInd, $this->status);
		 	
		 	$result = $wpdb->query($preparedSql);
		 	
			if($result === false)
		 	{
		 		MM_Transaction::rollback();
		 		return new MM_Response("ERROR: unable to create email account (".$preparedSql.")", MM_Response::$ERROR);
		 	}
		 	
		 	
		 	
		 	if(!$doUpdate) {
		 		
		 		$this->id = $wpdb->insert_id;
		 	}
		 	
		}
		catch(Exception $ex)
		{
		 	MM_Transaction::rollback();
		 		return new MM_Response("ERROR: unable to create email account", MM_Response::$ERROR);
		}
		 
		MM_Transaction::commit();
	
 		return new MM_Response();
	}
	
	public static function getDefaultAccount()
	{
		global $wpdb;
		
		$emailAccount = new MM_EmailAccount();
		
		$sql = "SELECT * FROM ".MM_TABLE_EMAIL_ACCOUNTS." WHERE is_default='1';";
		$result = $wpdb->get_row($sql);
		
		LogMe::write("getDefaultAccount() : ".json_encode($result));
		if($result) {
			$emailAccount->setId($result->id);
			$emailAccount->setData($result);
		}
		return $emailAccount;
	}
	
	public function confirmEmailAccount()
	{
		$this->getData();
		
		if($this->isValid()) {
			$this->status = "1";
			$result = $this->commitData();
			
			if($result->type == MM_Response::$ERROR) {
				return $result;
			}
			
			return new MM_Response("Email account '".$this->address."' has been confirmed");
		}
		else {
			return new MM_Response("Could not confirm email account. Invalid email account specified.", MM_Response::$ERROR);
		}
	}
 	
 	public function emailExists($doUpdate=false) 
 	{
 		global $wpdb;
 		
 		// check if email name already exists
		if(isset($this->displayName))
		{
			$sql = "SELECT * FROM ".MM_TABLE_EMAIL_ACCOUNTS." WHERE name='".$this->displayName."' LIMIT 1";
			
			$result = $wpdb->get_row($sql);
			
			if($result)
			{
				if($doUpdate == false || ($result->id != $this->id))
				{
					MM_Transaction::rollback();
			 		return new MM_Response("A email account with the name '".$this->displayName."' already exists.", MM_Response::$ERROR);
				}
			}
		}
 		
		// check if email address already exists
		if(isset($this->address))
		{
			$sql = "SELECT * FROM ".MM_TABLE_EMAIL_ACCOUNTS." WHERE email='".$this->address."' LIMIT 1";
			
			$result = $wpdb->get_row($sql);
			
			if($result)
			{
				if($doUpdate == false || ($result->id != $this->id))
				{
					MM_Transaction::rollback();
			 		return new MM_Response("A email account with the address '".$this->address."' already exists.", MM_Response::$ERROR);
				}
			}
		}
		
		return new MM_Response();
 	}
	
	public function delete()
	{	
		global $wpdb;
		
		if(!$this->hasAssociations())
		{
			$sql = "DELETE FROM ".MM_TABLE_EMAIL_ACCOUNTS." WHERE id='%d' limit 1";
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
		
		// check if email account is the default
		$sql = "select * from ".MM_TABLE_EMAIL_ACCOUNTS." where id='{$this->id}'";
		$row = $wpdb->get_row($sql);
		
		if($row->is_default == "1") {
			return true;
		}
		
		// check if email account is associated with a member type
		$sql = "select count(*) as total from ".MM_TABLE_MEMBER_TYPES." where email_from_id='{$this->id}'";
		$row = $wpdb->get_row($sql);
		
		if($row && $row->total > 0) {
			return true;
		}
		
		return false;
	}
	
	public static function getEmailAccount($id)
	{
		global $wpdb;
		
		$emailAccount = new MM_EmailAccount();
		
		$sql = "SELECT * FROM ".MM_TABLE_EMAIL_ACCOUNTS." WHERE id='{$id}'";
		$result = $wpdb->get_row($sql);
		
		if($result) {
			$emailAccount->setId($result->id);
			$emailAccount->setData($result);
		}
		return $emailAccount;
	}
	
	public static function getEmailAccountsList($activeStatusOnly=false)
 	{
 		global $wpdb;
 		
 		$list = array();
 		$default = "";
 		
 		if($activeStatusOnly) {
 			$sql = "select * from ".MM_TABLE_EMAIL_ACCOUNTS." where status ='1'";
 		}
 		else {
 			$sql = "select * from ".MM_TABLE_EMAIL_ACCOUNTS;
 		}
 		
 		$rows = $wpdb->get_results($sql);
 		
 		if($rows)
 		{
	 		foreach($rows as $row)
			{
				if($row->is_default == "1") {
					$default = $row->id;
				}
				
				$list[$row->id] = $row->name . " (" . $row->email . ")";	
			}
 		}
 		
 		$emails = new stdClass();
 		$emails->default = $default;
 		$emails->list = $list;
 			
 		return $emails;
 	}
 	
 	public function getUserId(){
 		return $this->userId;
 	}

	public function setPassword($str) 
	{
		$this->password = $str;
	}
	
	public function getPassword()
	{
		return $this->password;
	}
	
	public function setUsername($str) 
	{
		$this->username = $str;
	}
	
	public function getUsername()
	{
		return $this->username;
	}
 	
	public function setRoleId($str) 
	{
		$this->roleId = $str;
	}
	
	public function getRoleId()
	{
		return $this->roleId;
	}
 	
	public function setFullName($str) 
	{
		$this->name = $str;
	}
	
	public function getFullName()
	{
		return $this->name;
	}
 	
	public function setPhone($str) 
	{
		$this->phone = $str;
	}
	
	public function getPhone()
	{
		return $this->phone;
	}
 	
	public function setName($str) 
	{
		$this->displayName = $str;
	}
	
	public function getName()
	{
		return $this->displayName;
	}
 	
	public function setAddress($str) 
	{
		$this->address = $str;
	}
	
	public function getAddress()
	{
		return $this->address;
	}
	
	public function setStatus($str) 
	{
		$this->status = $str;
	}
	
	public function getStatus()
	{
		return $this->status;
	}
 	
	public function setIsDefault($str) 
	{
		$this->isDefaultInd = $str;
	}
	
	public function isDefault()
	{
		return $this->isDefaultInd;
	}
	
}
?>
