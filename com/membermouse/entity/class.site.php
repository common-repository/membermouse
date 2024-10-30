<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_Site extends MM_Entity
{
	public static $INSTALL_TYPE_LIMELIGHT = "1";
	public static $INSTALL_TYPE_STANDARD = "0";
	private $name = "";
	private $isDev = "0";
	private $location = "";
	private $campaignsInUse = "";
	private $campaignIds = "";
	private $llUrl = "";
	private $llUsername = "";
	private $llPassword = "";
	private $isMMInstall = false;
	private $status = "0";
	private $paidMembers = "0";
	private $totalMembers = "0";
	
	public function __construct($id="", $getData=true) 
 	{	
 		$this->id = $id; 
 		
 		if($getData) {
 			$this->getData();
 		}
 	}
	
	public function getData()
	{
		if(class_exists("MM_MembermouseService")){
			if(isset($this->id) && intval($this->id) > 0) {
				$this->setData(MM_MemberMouseService::getSite($this->id));
			} 
			else {
				$this->setData(MM_MemberMouseService::getSiteData());
			}
		}
	}
	
	public function setData($data)
	{
		try 
		{
			if($data === false) {
				parent::invalidate();
			}
			else
			{
				foreach($data as $key=>$v) {
					$data->$key = stripslashes($v);	
				}
				
				$this->id = $data->id;
				$this->name = $data->name;
				$this->isDev = (isset($data->is_dev))?$data->is_dev:"";
				$this->location = $data->location;
				$this->campaignIds = $data->campaign_ids;
				$this->campaignsInUse = $data->campaigns_in_use;
				$this->llUrl = $data->limelight_url;
				$this->llUsername = $data->limelight_username;
				$this->llPassword = MM_Utils::decryptPassword($data->limelight_password);
				$this->isMMInstall = $data->is_membermouse;
				$this->status = $data->status;
				$this->paidMembers = intval($data->paid_members);
				$this->totalMembers = intval($data->total_members);
				$this->validate();
			}
		}
		catch (Exception $ex) {
			parent::invalidate();
		}
	}
	
	public static function getNextVersion($currentVersion){
		
		$versions = array(
//			0=>'1.1.15',
//			1=>'1.1.16',
		);
		
		$index = array_search($currentVersion, $versions);
		if($index==false){
			return false;
		}
		$index++;
		if(isset($versions[$index])){
			return $versions[$index];
		}
		return false;
	}
	
	public static function getPluginVersion()
	{
		$contents = file_get_contents(ABSPATH."wp-content/plugins/".MM_PLUGIN_NAME."/index.php");
		preg_match("/(\@version)[0-9\.\s]+/", $contents, $match);
		return ((isset($match[0]))?preg_replace("/(\@version)[\s]+/","", trim($match[0])):"");
	}
	
	public function authenticate()
	{
		if($this->getLLUrl() != '' && $this->getLLUsername() != '' && $this->getLLPassword() != '') {
			return true;
		}
		else {
			return false;
		}
	}

	public function setCampaignsInUse($str){
		$this->campaignsInUse = $str;
	}
	
	public function getCampaignsInUse(){
		return $this->campaignsInUse;
	}
	
	public function commitData()
	{
		$memberId = MM_OptionUtils::getOption("mm-member_id");
		$contents = MM_MemberMouseService::commitSiteData($memberId, $this);
		if(MM_MemberMouseService::isSuccessfulRequest($contents)){
			return new MM_Response();
		}
		return new MM_Response("Could not update site data on Central Server. [{$contents->response_code}] ".$contents->response_message, MM_Response::$ERROR);
	}
	
	public function setName($str) 
	{
		$this->name = $str;
	}
	
	public function getName() 
	{
		return $this->name;
	}
	
	public function setIsDev($str){
		$this->isDev = $str;
	}
	
	public function isDev(){
		return $this->isDev;
	}
	
	public function setLocation($str) 
	{
		$this->location = $str;
	}
	
	public function getLocation() 
	{
		return $this->location;
	}
	
	public function setCampaignIds($str) 
	{
		$this->campaignIds = $str;
	}
	
	public function getCampaignIds() 
	{
		return $this->campaignIds;
	}
	
	public function setLLUsername($str) 
	{
		$this->llUsername = $str;
	}
	
	public function getLLUsername()
	{
		return !empty($this->llUsername) ? $this->llUsername : "";
	}
	
	public function setLLPassword($str) 
	{
		$this->llPassword = $str;
	}
	
	public function getLLPassword()
	{
		return !empty($this->llPassword) ? $this->llPassword : "";
	}
	
	public function getLLPasswordEncrypted()
	{
		return !empty($this->llPassword) ? MM_Utils::encryptPassword($this->llPassword) : "";
	}
	
	public function setLLUrl($str) 
	{
		$this->llUrl = $str;
	}
	
	public function getLLUrl()
	{
		if(!empty($this->llUrl))
		{
			if(substr($this->llUrl, strlen($this->llUrl)-1, 1) == "/") {
				return $this->llUrl;
			}
			else {
				return $this->llUrl."/";
			}
		
		}
		else 
		{
			return "";
		}
	}

	public function setStatus($status)
	{
		$this->status = $status;
	}
	
	public function getStatus()
	{
		return $this->status;
	}
	
	public function getPaidMembers()
	{
		return $this->paidMembers;
	}
	
	public function getTotalMembers()
	{
		return $this->totalMembers;
	}
	
	public function isMM()
	{
		return $this->isMMInstall;
	}
	
	public function setIsMM($str)
	{
		$this->isMMInstall = $str;
	}
}
?>
