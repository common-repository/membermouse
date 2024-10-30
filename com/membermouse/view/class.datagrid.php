<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_DataGrid
{
	private $headers = array();
	private $rows = array();
	public $totalRecords = 0;
	public $sortBy = "id";
	public $sortDir = "desc";
	public $crntPage = 0;
	public $resultSize = 20;
	public $recordName = "records";
	public $showCsvControl = false;
	public $showPagingControls = true;
	
	public function __construct($post=null, $dfltSortBy="", $dfltSortDir="", $dfltResultSize="") 
	{
		if($dfltSortBy != "") {
			$this->sortBy = $dfltSortBy;
		}
		
		if($dfltSortDir != "") {
			$this->sortDir = $dfltSortDir;
		}
		
		if($dfltResultSize != "") {
			$this->resultSize = $dfltResultSize;
		}
		
		if(isset($post)) {
			$this->setProperties($post);
		}
	}
	
	public function setHeaders($arr)
	{
		$this->headers = $arr;
	}
	
	public function setRows($arr)
	{
		$this->rows = $arr;
	}
	
	public function setTotalRecords($data)
	{
		// TODO pass in the numerical value versus complete data set.
		if($data && is_array($data) && count($data) > 0) 
		{
			$this->totalRecords = $data[0]->total;
		}
	}
	
	public function setProperties($post)
	{
		$this->sortBy = (isset($post["sortBy"])) ? $post["sortBy"] : $this->sortBy;
		$this->sortDir = (isset($post["sortDir"])) ? $post["sortDir"] : $this->sortDir;
		$this->crntPage = (isset($post["crntPage"])) ? $post["crntPage"] : $this->crntPage;
		$this->resultSize = (isset($post["resultSize"])) ? $post["resultSize"] : $this->resultSize;
	}
	
	public function getLimitSql()
	{
		$sql = "";
		
		if(isset($this->crntPage) && isset($this->resultSize)) {
			$sql .= " LIMIT ".intval($this->crntPage)*intval($this->resultSize).", {$this->resultSize}";
		}
		
		return $sql;
	}
	
	public function generateHtml()
	{
		$html = "";
		
		if(!empty($this->headers) && !empty($this->rows) && count($this->rows) > 0) {
			// handle the case where sorting a result set from the wp_users table
			if(!isset($this->headers[$this->sortBy]) && $this->sortBy == "id") {
				$this->sortBy = "ID";
			}
			
			if(isset($this->headers[$this->sortBy]) && !empty($this->sortBy) && !is_null($this->sortBy))
			{
				$this->headers[$this->sortBy]['content'] =  $this->headers[$this->sortBy]['content'] . '<img src="'.MM_Utils::getImageUrl('sort_'.$this->sortDir).'"/>';
				$this->headers[$this->sortBy]['content'] = str_replace('<a', '<a class="selected"', $this->headers[$this->sortBy]['content'] );
			}
			
			$dg = new stdClass();
			$dg->datagrid = new stdClass();
			$dg->datagrid->attr = 'id="mm-data-grid" class="widefat"';
			$dg->datagrid->headers = $this->headers;
			$dg->datagrid->rows = $this->rows;
			
			$dg->showCsvControl = $this->showCsvControl;
			$dg->sortBy = $this->sortBy;
			$dg->sortDir = $this->sortDir;
			$dg->crntPage = $this->crntPage;
			$dg->resultSize = $this->resultSize;
			$dg->totalRecords = $this->totalRecords;
			$dg->totalPages = ceil(intval($this->totalRecords)/intval($this->resultSize));
			$dg->recordName = (intval($this->totalRecords) > 1) ? $this->recordName."s" : $this->recordName;
			$dg->showPagingControls = $this->showPagingControls;
			
			$html = MM_Template::generate(MM_MODULES."/datagrid.php", $dg);
		}
					
		return $html;
	}
}
?>
