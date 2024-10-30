<?php
class MM_SmartTag extends MM_Entity
{
	private $name;
	private $groupId;
	
	protected function getData() {
		// do nothing
	}
	
	protected function commitData() {
		// do nothing
	}
	
 	public function setData($data)
 	{
 		try 
 		{
	 		$this->name = $data->name;
	 		$this->groupId = $data->group_id;
	 		
	 		parent::validate();
		}
		catch (Exception $ex) {
			parent::invalidate();
		}
 	}
 	
 	public function generateHtml()
 	{
 		$fileName = MM_TEMPLATES_URL."smarttags/";
 		
 		$arr = explode("_", $this->name);
 		
 		for($i = 1; $i < count($arr); $i++) 
 		{
 			$fileName .= strtolower($arr[$i]);
 			
 			if($i == count($arr) - 1) {
 				$fileName .= ".php?q=".MM_Utils::createRandomString(10);
 			}
 			else {
 				$fileName .= "/";
 			}
 		}
 		
 		$html = "<div id=\"mm-smarttag".$this->id."\" style=\"margin-bottom:6px; font-size: 11px; cursor:pointer;\" onclick=\"stl_js.smartTagClickHandler('".$fileName."');\">";
 		$html .= "[".$this->name."]";
 		$html .= "</div>";
		
 		return $html;
 	}
 	
	public function getName()
	{
		return $this->name;
	}
 	
	public function getGroupId()
	{
		return $this->groupId;
	}
 	
}
?>