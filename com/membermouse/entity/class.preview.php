<?php
class MM_Preview
{
	private $accessTags =array();
	private $user;
	private $days =0;
	private $memberTypeId=0;
	private $accessTagApplied=array();
	
	
 	public function __construct(){}
 	
	public function setData($userId, $days=0, $memberTypeId=null, $accessTags=null)
	{
		$userObj = new MM_User($userId);
		if(is_null($memberTypeId) || intval($memberTypeId)<=0){
			$mts = MM_MemberType::getMemberTypesList(true);
			$memberTypeId = key($mts);
		}
		$userObj->setId($userId);
		$userObj->setMemberTypeId($memberTypeId);
		$userObj->setFirstName("John");
		$userObj->setLastName("Doe");
		$userObj->setPhone("(123) 123-1234");
		$userObj->setEmail("john@doe.com");
		$userObj->setPassword("password");
		$userObj->setUsername("john123");
		$userObj->setStatus("1");
		$userObj->setBillingAddress("1 membermouse ave.");
		$userObj->setBillingCity("New York");
		$userObj->setBillingState("NY");
		$userObj->setBillingZipCode("12345");
		$userObj->setBillingCountry(MM_LimeLightUtils::$COUNTRY_ID_US);
		$userObj->setShippingAddress("1 membermouse ave.");
		$userObj->setShippingCity("New York");
		$userObj->setShippingState("NY");
		$userObj->setShippingZipCode("12345");
		$userObj->setShippingCountry(MM_LimeLightUtils::$COUNTRY_ID_US);
		$this->user = $userObj;
		
		$this->accessTags = (is_array($accessTags))?array_keys($accessTags):array();
		$this->accessTagApplied = $accessTags;
		$this->days = $days;
		MM_Session::value(MM_Session::$KEY_PREVIEW_MODE, serialize($this));
	}
	
	public static function getData()
	{
		global $current_user;
		$previewObj = unserialize(MM_Session::value(MM_SESSION::$KEY_PREVIEW_MODE));
		$memberTypeList = MM_MemberType::getMemberTypesPostAccess();
	
		if(is_object($previewObj)){	
			if($previewObj->getMemberTypeId()>0 && isset($memberTypeList[$previewObj->getMemberTypeId()])){
				return $previewObj;
			}
		}
		if(is_array($memberTypeList)){
			$memberTypeId = key($memberTypeList);
		} 
		
		$obj = new MM_Preview();
		$obj->setData($current_user->ID, 0, $memberTypeId);
		$previewObj = unserialize(MM_Session::value(MM_SESSION::$KEY_PREVIEW_MODE));
		if(is_object($previewObj)){
			return $previewObj;
		}
		
		return false;
	}
	
	public static function clearPreviewMode()
	{
		MM_Session::clear(MM_SESSION::$KEY_PREVIEW_MODE);
	}
	
	public function getAppliedDays($accessTagId)
	{
		if(isset($this->accessTagApplied[$accessTagId]))
			return $this->accessTagApplied[$accessTagId];
		
		return 0;
	}
	
	public function getUser()
	{
		return $this->user;
	}
	
	public function getDays()
	{
		return $this->days;
	}
	
	public function getAccessTags()
	{
		return $this->accessTags;
	}
	
	public function getMemberTypeId()
	{
		$user = $this->user;
		return $user->getMemberTypeId();
	}
}
?>