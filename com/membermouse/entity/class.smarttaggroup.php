<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_SmartTagGroup extends MM_Entity
{	
	private $name = "";
	private $parentId = 0;
	private $children = array();
	
	protected function getData() {
		// do nothing
	}
	
	protected function commitData() {
		// do nothing
	}
	
	public function setData($data)
	{
		global $wpdb;
		
		try 
		{
			$this->id = $data->id;
			$this->name = $data->name;
			$this->parentId = $data->parent_id;
			
			// get child groups
			$sql = "SELECT * FROM ".MM_TABLE_SMARTTAG_GROUPS." WHERE parent_id='{$this->id}' AND visible='1' ORDER BY name asc";
			$rows = $wpdb->get_results($sql);
			
			if($rows) 
			{
				foreach($rows as $row)
				{
					$group = new MM_SmartTagGroup();
					$group->setData($row);
					
					if($group->isValid()) {
						array_push($this->children, $group);
					}
				}
			}
			
			// get child SmartTags
			$sql = "SELECT * FROM ".MM_TABLE_SMARTTAGS." WHERE group_id='{$this->id}' AND visible='1' ORDER BY name asc";
			$rows = $wpdb->get_results($sql);
			
			if($rows) 
			{
				foreach($rows as $row)
				{
					$smartTag = new MM_SmartTag();
					$smartTag->setData($row);
					
					if($smartTag->isValid()) {
						array_push($this->children, $smartTag);
					}
				}
			}
			
			parent::validate();
		}
		catch (Exception $ex) {
			parent::invalidate();
		}
	}
	
	public function generateHtml() 
	{
		$html = "<div id=\"mm-smarttag-group".$this->id."\" style=\"margin-bottom:6px; font-size: 12px; cursor:pointer;\" onclick=\"stl_js.toggleSmartTagGroup('".$this->id."');\">";
		$html .= "<img id=\"mm-smarttag-group".$this->id."-closed-img\" src=\"".MM_Utils::getImageUrl("smarttag_group_closed")."\" style=\"vertical-align: middle;\" /> ";
		$html .= "<img id=\"mm-smarttag-group".$this->id."-open-img\" src=\"".MM_Utils::getImageUrl("smarttag_group_open")."\" style=\"vertical-align: middle; display:none;\" /> ";
		$html .= $this->name;
		$html .= "</div>";
		
		if(count($this->children) > 0) 
		{
			$html .= "<div id=\"mm-smarttag-group".$this->id."-children\" style=\"display:none; margin-left:10px; margin-bottom:6px;\">";
					
			foreach($this->children as $child)
			{
				if($child instanceof MM_SmartTagGroup) {
					$html .= $child->generateHtml();
				}
				else {
					$html .= $child->generateHtml();
				}
			}	
				
			$html .= "</div>";
		}
		
		return $html;
	}
 	
	public function getName()
	{
		return $this->name;
	}
 	
	public function getParentId()
	{
		return $this->parentId;
	}
 	
	public function getChildren()
	{
		return $this->children;
	}
	
}
?>
