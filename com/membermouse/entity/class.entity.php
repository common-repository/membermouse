<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
abstract class MM_Entity
{
	protected $id = 0;
	private $valid = false;
	
	public function __construct($id="", $getData=true) 
 	{
 		if(isset($id) && intval($id) > 0)
 		{
 			$this->id = $id;
 			
 			if($getData == true) {
 				$this->getData();
 			}
 		}
 		else {
 			$id = "";
 		}
 	}
	
 	/*
 	 * @param $tag should be smarttag name i.e. mm_member_firstname
 	 */
 	public function getSmartTagFunc($tag)
 	{
 		$tag = "get".array_pop(explode("_", $tag));
 		
 		$class_methods = get_class_methods($this);
 		foreach($class_methods as $method)
 		{
 			if(strtolower($tag)==strtolower($method))
 			{
 				return $method;	
 			}	
 		}
 		return false;
 	}
 	
	abstract protected function getData();
	abstract public function setData($data);
	abstract protected function commitData();
 	
	public function setId($str)
 	{
 		$this->id = $str;
 	}
 	
	public function getId()
 	{
 		return $this->id;
 	}
	
	public function validate()
	{
		$this->valid = true;
	}
	
	public function invalidate() 
	{
		$this->valid = false;	
	}
	
	public function isValid()
	{
		return $this->valid;
	}
}
?>
