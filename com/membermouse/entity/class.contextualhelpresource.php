<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_ContextualHelpResource extends MM_Entity
{	
	public static $TYPE_VIDEO = "video";
	public static $TYPE_ARTICLE = "article";
	public static $TYPE_MISC = "misc";
	
	private $sectionId = "";
	private $typeId = "";
	private $title = "";
	private $url = "";
	private $embedCode = "";
	private $width = 0;
	private $height = 0;
	private $description = "";
	private $duration = "";
	private $icon = "";
	private $dateAdded = "";
	
	public function getData() 
	{
		// do nothing
	}
	
	public function setData($data)
	{
		try 
		{
			$this->sectionId = $data->section_id;
			$this->typeId = $data->type_id;
			$this->title = $data->title;
			$this->url = $data->url;
			$this->embedCode = $data->embed_code;
			$this->width = $data->width;
			$this->height = $data->height;
			$this->description = $data->description;
			$this->duration = $data->duration;
			$this->icon = $data->icon;
			$this->dateAdded = $data->date_added;
			parent::validate();
		}
		catch (Exception $ex) {
			parent::invalidate();
		}
	}
	
	public function commitData()
	{	
		// do nothing
	}
	
	public function generateHtml()
	{
		$html = "<p>";
		
		if($this->isNew()) 
		{
			$html .= "<img src='".MM_Utils::getImageUrl('new')."' style='vertical-align: middle;' /> ";
		}
		
		switch($this->typeId) 
		{
			case self::$TYPE_VIDEO:
				$html .= $this->videoResourceHtml();
				break;
				
			case self::$TYPE_ARTICLE:
				$html .= $this->articleResourceHtml();
				break;
				
			case self::$TYPE_MISC:
				$html .= $this->miscResourceHtml();
				break;
		}
		
		$html .= "</p>";
		
		return $html;
	}
	
	private function videoResourceHtml()
	{
		$str = "<a onclick=\"mmjs.displayVideo('".$this->title."', '".urlencode($this->embedCode)."', '".$this->width."', '".$this->height."')\" style='cursor:pointer;'><img src='".MM_Utils::getImageUrl('film')."' title='Training Video' /> ".$this->title."</a>";
		
		if($this->duration != "") {
			$str .= " <span style='font-size: 10px;'>(".$this->duration.")</span>";
		}
		
		if($this->description != "") {
			$str .= " - ".$this->description;
		}
		
		return $str;
	}
	
	private function articleResourceHtml()
	{
		$str = "<a href='".$this->url."' target='_blank' style='cursor:pointer; text-decoration: none;'><img src='".MM_Utils::getImageUrl('page_white')."' title='Helpful Article' /> ".$this->title."</a>";

		if($this->description != "") {
			$str .= " - ".$this->description;
		}
		
		return $str;
	}
	
	private function miscResourceHtml()
	{
		$str = "<a href='".$this->url."' target='_blank' style='cursor:pointer; text-decoration: none;'><img src='".MM_Utils::getImageUrl($this->icon)."' title='".$this->title."' /> ".$this->title."</a>";

		if($this->description != "") {
			$str .= " - ".$this->description;
		}
		
		return $str;
	}
	
	private function isNew()
	{
		$diff = round(abs(time() - strtotime($this->dateAdded))/86400);
		return $diff <= 7 ? true : false;
	}
	
}
?>
