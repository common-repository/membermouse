<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_MembersView extends MM_View
{	
	public static $TIME_PER_ORDER = 1.5;
 	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{
				case self::$MM_JSACTION_RESET_FORM:
					return $this->generateSearchForm($post);
					
				case self::$MM_JSACTION_CSV_IMPORT:
					return $this->getCSVMembers($post);
					
				case self::$MM_JSACTION_SEARCH:
					return $this->generateDataGrid($post);
					
				case self::$MM_JSACTION_PLACE_NEW_ORDER:
					
					return MM_TransactionEngine::placeNewOrder($post);
					
				case self::$MM_JSACTION_GET_MEMBER_TYPE:
					return $this->getMemberTypeInfo($post);
					
				case self::$MM_JSACTION_IMPORT_DIALOG:
					return $this->getImportDialog($post);	
					
				case self::$MM_JSACTION_IMPORT_FIND:
					return $this->findMembers($post);	
					
				case self::$MM_JSACTION_IMPORT_MEMBERS:
					return $this->importMembers($post);	
					
				case self::$MM_JSACTION_IMPORT_DISPLAY_MEMBERS:
					return $this->getImportMemberDetails($post);	
					
				case self::$MM_JSACTION_REMOVE:
					return $this->removeMember($post);
					
				default:
					return new MM_Response($response);
			}
		}
		else 
		{
			return $response;
		}
	}
	
 	public function search($post, MM_DataGrid $dg, $csv=false)
	{
		global $wpdb;
	
		$accessTagSql = "";
		$affiliateSql = "";
		
		$customFieldJoinSql = "";
		$customFieldColumnSql = "";
		$customFieldWhereSql = "";
		
		$customFieldJoinSql2 = "";
		$customFieldColumnSql2 = "";
		$customFieldWhereSql2 = "";
		
		
		if(isset($post["mm_member_custom_field"]) && $post["mm_member_custom_field"]!=""){
			$customFieldJoinSql = ",  ".MM_TABLE_CUSTOM_FIELD_DATA." cfd ";
			$customFieldColumnSql = " cfd.value as custom_field_value, ";
			$customFieldWhereSql = " (cfd.custom_field_id='{$post["mm_member_custom_field"]}' and cfd.user_id=u.id and cfd.value LIKE '%".$post["mm_member_custom_field_value"]."%' ) ";
		}
		
		if(isset($post["mm_member_custom_field2"]) && $post["mm_member_custom_field2"]!=""){
			$customFieldJoinSql2 = ",  ".MM_TABLE_CUSTOM_FIELD_DATA." cfd2 ";
			$customFieldColumnSql2 = " cfd2.value as custom_field_value2, ";
			$customFieldWhereSql2 = " (cfd2.custom_field_id='{$post["mm_member_custom_field2"]}' and cfd2.user_id=u.id and cfd2.value LIKE '%".$post["mm_member_custom_field2_value"]."%' ) ";
		}
		
		if(!empty($post['mm_access_tags'])) {
			$accessTagSql = "(select group_concat(DISTINCT at2.name) as access_tags " .
				" FROM ".$wpdb->users." usr " .
				" LEFT JOIN ".MM_TABLE_APPLIED_ACCESS_TAGS." applied2 ON applied2.access_type='user' and applied2.ref_id=usr.id  and applied2.status='1' and applied2.is_refunded='0' " .
				" LEFT JOIN ".MM_TABLE_ACCESS_TAGS." at2 on applied2.access_tag_id=at2.id where usr.id=u.id " .
				" GROUP BY usr.id) as access_tags, ";
		} 
		else {
			$accessTagSql = "group_concat(DISTINCT at.name) as access_tags, ";
		}
		
		$affiliateSql = "(
							select 
								group_concat(DISTINCT rr.affiliate_id) as aff 
							from 
								".MM_TABLE_RETENTION_REPORTS." rr LEFT JOIN ".MM_TABLE_PRODUCTS." p on rr.product_id=p.id  
							where 
								(rr.affiliate_id !='' OR rr.sub_affiliate_id !='') AND 
								rr.user_id=u.id  
								
							group by rr.user_id
											 
					) as affiliate_product ";
		
		$sqlFrom = " FROM ".$wpdb->users." u ".
			" LEFT JOIN ".MM_TABLE_APPLIED_ACCESS_TAGS." applied on applied.access_type='user' and applied.ref_id=u.id and applied.status='1' and applied.is_refunded='0' " .
			" LEFT JOIN ".MM_TABLE_ACCESS_TAGS." at on applied.access_tag_id=at.id " .$customFieldJoinSql.$customFieldJoinSql2.
			" ";
		
		$filters = "";
		$newFilter = "";
	
		$chosenAffiliatesWhere = "";
		if(!empty($post["mm_affiliate_id"])){
			$chosenAffiliatesWhere = " u.id IN (select ret.user_id from ".MM_TABLE_RETENTION_REPORTS." ret where affiliate_id  LIKE '%".$post["mm_affiliate_id"]."%' OR sub_affiliate_id  LIKE '%".$post["mm_affiliate_id"]."%') ";
			$filters = $this->addFilter($filters, $chosenAffiliatesWhere);
		}
		
		// Registered Date
		if(!empty($post['mm_from_date'])) {
			$post['mm_from_date'] = $post['mm_from_date']." 0:00:00";
			$newFilter = "u.mm_registered >= '".date("Y-m-d G:i:s", strtotime($post['mm_from_date']))."'";
			$filters = $this->addFilter($filters, $newFilter);
		}
		
		if(!empty($customFieldWhereSql)){
			$filters = $this->addFilter($filters, $customFieldWhereSql);
		}
		
		if(!empty($customFieldWhereSql2)){
			$filters = $this->addFilter($filters, $customFieldWhereSql2);
		}
		
		if(!empty($post['mm_to_date'])) {
			$post['mm_to_date'] = $post['mm_to_date']." 23:59:59";
			$newFilter = "u.mm_registered <= '".date("Y-m-d G:i:s", strtotime($post['mm_to_date']))."'";
			$filters = $this->addFilter($filters, $newFilter);
		}
		
		// Member ID
		if(!empty($post['mm_member_id'])) {
			$filters = $this->addFilter($filters, "u.id = '".$post["mm_member_id"]."'");
		}
		
		// First Name
		if(!empty($post['mm_first_name'])) {
			$filters = $this->addFilter($filters, "u.mm_first_name = '".$post["mm_first_name"]."'");
		}
		
		// Last Name
		if(!empty($post['mm_last_name'])) {
			$filters = $this->addFilter($filters, "u.mm_last_name = '".$post["mm_last_name"]."'");
		}
		
		// Username
		if(!empty($post['mm_username'])) {
			$filters = $this->addFilter($filters, "u.user_login = '".$post["mm_username"]."'");
		}
		
		// Email
		if(!empty($post['mm_email'])) {
			$filters = $this->addFilter($filters, "u.user_email = '".$post["mm_email"]."'");
		}
		
		// Member Type Ids
		if(!empty($post['mm_member_types'])) {
			$newFilter = "u.mm_member_type_id IN (".join(',' , $post["mm_member_types"]).")";
			
			if($newFilter != "") {
				$filters = $this->addFilter($filters, $newFilter);
			}
		}
		
		// Access Tag Ids
		if(!empty($post['mm_access_tags'])) {
			$newFilter = "at.id IN (".join(',' , $post["mm_access_tags"]).")";
			
			if($newFilter != "") {
				$filters = $this->addFilter($filters, $newFilter);
			}
		}
		
		// Member Status
		if(!empty($post['mm_member_status_types'])) {
			$newFilter = "u.mm_status IN (".join(',' , $post["mm_member_status_types"]).")";
			
			if($newFilter != "") {
				$filters = $this->addFilter($filters, $newFilter);
			}
		}
		
		// Hide administrators and invalid MM members
		$newFilter = "u.mm_registered != ''";
		$filters = $this->addFilter($filters, $newFilter);
		
		$sqlWhere = "";
		
		if($filters != "") {
			$sqlWhere = " WHERE ".$filters;
		}
		
		$sqlGroupBy = " GROUP BY u.id";
		
		if(isset($dg->sortBy) && !is_null($dg->sortBy) && !empty($dg->sortBy)) {
			$sqlGroupBy.= " ORDER BY u.{$dg->sortBy} {$dg->sortDir} ";
		}
		
		$sqlResultCount = "select count(distinct u.ID) as total ";
		$sqlResultCount .= $sqlFrom;
		$sqlResultCount .= $sqlWhere;
		
		$countRow = $wpdb->get_row($sqlResultCount);
		
		if($countRow) {
			$sql = "select {$customFieldColumnSql} {$customFieldColumnSql2} '{$countRow->total}' as total, u.*, ";
		}
		else {
			$sql = "select {$customFieldColumnSql} {$customFieldColumnSql2} u.*, ";
		}
		
		$sql .= $accessTagSql;
		$sql .= $affiliateSql;
		$sql .= $sqlFrom;
		$sql .= $sqlWhere;
		$sql .= $sqlGroupBy;
		if(!$csv){
			$sql .= $dg->getLimitSql();
		}
//		echo $sql;
		$rows = $wpdb->get_results($sql);
		
		if(!$rows || is_null($rows))
			return array();
		
		return $rows;
	}
	
	private function addFilter($filters, $newFilter) 
	{
		if($filters == "") {
			$filters = " ".$newFilter;
		}
		else if($newFilter != "") {
			$filters .= " AND ".$newFilter;
		}
		
		return $filters;
	}
	
	private function removeMember($post) 
	{
		$user = new MM_User($post["id"]);
		
		if($user->isValid()) 
		{
			$result = $user->delete();
			
			return $result;
		}
		
		return new MM_Response("Unable to delete member. No ID specified.", MM_Response::$ERROR);
	}
	
	private function downloadFile($url){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST      ,0);
		curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
		curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
		$contents = curl_exec($ch);
		curl_close($ch);

		if($contents===false){
			return new MM_Response("Invalid file URL.", MM_Response::$ERROR);
		}
		return $contents;	
	}
	
	public function parseCsvToDB($filePath){
		$row = 1;
		if (($handle = fopen($filePath, "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		        $num = count($data);
		        echo "<p> $num fields in line $row: <br /></p>\n";
		        $row++;
		        for ($c=0; $c < $num; $c++) {
		            echo $data[$c] . "<br />\n";
		        }
		    }
		    fclose($handle);
		}
	}
	
	private function getCSVRowCount($filePath){
//		$fsize = filesize($filePath);
//		$count = 0;
		$fp = fopen($filePath, "r") or die("Couldn't get handle");
//		if ($handle) {
//		  while (!feof($handle)) {
//		    $buffer = fgets($handle, 190);
//		    echo $buffer."\n\n";
//		    $count++;
//		  }
//		  fclose($handle);
//		}
//		return $count;
		ini_set('auto_detect_line_endings',true);
//stream_get_line($fp, 4096, "\r");
			while(!feof($fp)){ 
			 $line=fgetcsv($fp,4096,','); 
			 foreach($line as $key=>$value){ 
			  echo  " '{$value}' | ";
			 } 
			 echo "\n\n";
			} 
			return 0;
	}
	
	public function getCSVMembers($post){
		$req = array('csv_file','member_type','mm_delim');
		foreach($req as $field){
			if(!isset($post[$field])){
				return new MM_Response("{$field} is required.", MM_Response::$ERROR);
			}
		}
		
		if(!preg_match("/(\.csv)$/", strtolower($post["csv_file"]))){
			return new MM_Response("Invalid file extension. Must be end in csv.", MM_Response::$ERROR);
		}
		$delim = "";
		switch($post["mm_delim"]){
			case "newline":
				$delim = "\n";
				break;
			case "creturn":
				$delim="\r";
				break;
			default: 
				$delim="\n\r";
		}
		ini_set('memory_limit', '50M');
		$contents = file_get_contents($post["csv_file"]);
		$rowCount = count(explode($delim, $contents));
		
		$cachedObjects = array();
		
		/*
		 * email, phone, username, billing address, billing city, billing state, billing zip, billing country,
		 * shipping address, shipping city, shipping state, shipping zip, shipping country,first name, last name, registration date
		 */
		$skippedHeader = true;
		if(isset($post["mm_firstrow_header"]) && $post["mm_firstrow_header"]=="1"){
			$skippedHeader=false;
		}
		$csvContents = str_getcsv($contents, $delim); //parse the rows 
		foreach($csvContents as &$row){
			if(!$skippedHeader){
				$skippedHeader = true;
				continue;
			}
			$result = str_getcsv($row, ",");
			if(count($result)<15){
				return new MM_Response("Invalid row count.", MM_Response::$ERROR);
			}
			$_cacheObj = new stdClass();
			$_cacheObj->accessTagIds = array();
			$_cacheObj->user = null;  
			
			$user = new MM_User();
			$user->setEmail(array_shift($result));
			$user->setPhone(array_shift($result));
			$user->setUsername(preg_replace("/[^a-zA-Z0-9]+/", "", array_shift($result)));
			$user->setBillingAddress(array_shift($result));	
			$user->setBillingCity(array_shift($result));	
			$user->setBillingState(array_shift($result));	
			$user->setBillingZipCode(array_shift($result));	
			$user->setBillingCountry(array_shift($result));
			$user->setShippingAddress(array_shift($result));	
			$user->setShippingCity(array_shift($result));	
			$user->setShippingState(array_shift($result));	
			$user->setShippingZipCode(array_shift($result));		
			$user->setShippingCountry(array_shift($result));
			$user->setFirstName(array_shift($result));	
			$user->setLastName(array_shift($result));		
			
			$regDate = array_shift($result);
			$user->setRegistrationDate($regDate);
			$user->setMemberTypeId($post["member_type"]);
			$_cacheObj->user = $user;
			$cachedObjects[] = $_cacheObj;
		}
		
		if($rowCount>0){
			MM_Session::value("import", serialize($cachedObjects));
		}
		$info = new stdClass();
		$info->list ="<span style='font-size: 14px;'>{$rowCount} total members set to be imported.</span><div style='height: 15px; clear:both;'></div>";
		if($rowCount<=0){
			$info->list = "No members found to import.";
		}
		$info->display = "3";
		$content = MM_TEMPLATE::generate(MM_MODULES."/import_from_ll.php", $info);
		return new MM_Response($content);
	}
	
	public function getImportMemberDetails($post){
		$campaignId = $post["campaign_id"];
		
		$order_ids = explode(",", $post["order_ids"]);
		$data = array();
		$data[0] = new stdClass();
		$data[0]->total = count($order_ids);
		
		/// initialize data grid
		$dataGrid = new MM_DataGrid($post, "last_name", "asc", count($order_ids));
		$dataGrid->setTotalRecords($data);
		$dataGrid->showPagingControls = false;
		$dataGrid->recordName = "members";
		
		MM_Session::clear("import");
		
		$rows = array();
		$headers = array
		(	    
			'warning'				=> array('content' => '&nbsp;', 'attr' => 'width=\'15px\''),
			'include'			=> array('content' => '&nbsp;', 'attr' => 'width=\'20px\''),
		   	'name'				=> array('content' => 'Name'),
		   	'email'		=> array('content' => 'Email'),
		   	'purchase_date'		=> array('content' => 'Purchase Date'),
		   	'member_type'	=> array('content' => 'Member Type'),
		   	'access_tag'		=> array('content' => 'Access Tag')
		);
				
		
		$cacheObject = array();
		$list = "";
		if(!empty($order_ids)){
			foreach($order_ids as $id){
				if(intval($id)<=0 || empty($id)){
					continue;
				}
				
				$result = MM_LimeLightService::getOrder($id);
				$result["email"] = (!isset($result["email_address"]))?"user@email{$id}.com":$result["email_address"];
				
				$table = new stdClass();
				$table->warning = "";
				$table->id = $id;
				$table->name = $result["first_name"]." ".$result["last_name"];
				$table->email = $result["email"];
				$table->purchase_date = Date("m/d/Y", strtotime($result["time_stamp"]));
				$table->warning_title = "";
				
				$productUpsell = $result["upsell_product_id"];
				$productsArr = array();
				$productsArr[] = $result["main_product_id"];
				if(preg_match("/[0-9]+/", $productUpsell) && !empty($productUpsell)){
					$arr = explode(",",$productUpsell);
					for($j=0; $j<count($arr); $j++){
						if(!empty($arr[$j])){
							$productsArr[] = $arr[$j];
						}
					}
				}
				
				$_cacheObj = new stdClass();
				$_cacheObj->accessTagIds = array();
				$_cacheObj->user = null;  
				$table->member_type = "<img src='".MM_Utils::getImageUrl("user")."'  style='float:left; margin-right: 5px;'/> ";
				$table->access_tag = MM_NO_DATA;
				
				foreach($productsArr as $productId){
					$productObj = new MM_Product();
					$productObj->getProductByCampaign($productId, $campaignId);
					
					$isValidMemberType = false;
					if($productObj->isValid()){
						//get associations, by member type
						$memberType = $productObj->getAssociatedMemberType();
						$memberTypeId = (isset($memberType->id) && intval($memberType->id)>0)?$memberType->id:0;
						$memberTypeObj = new MM_MemberType($memberTypeId);
					
						//get associations, by access tag
						$accessTag = $productObj->getAssociatedAccessTag();
						$accessTagId = (isset($accessTag->id) && intval($accessTag->id)>0)?$accessTag->id:0;
						$accessTagObj = new MM_AccessTag($accessTagId);
						if($accessTagObj->isValid()){
							if($table->access_tag==MM_NO_DATA){
								$table->access_tag = "<img src='".MM_Utils::getImageUrl("tag")."' style='float:left; margin-right: 5px;' /> ". $accessTagObj->getName();
							}
							else{
								$table->access_tag .=", ".$accessTagObj->getName();
							}
							$table->warning_title = "The product associated with this order is associated with an access tag. If imported, this member will be associated with the default member type and then the access tag will be applied to their account.";
							
							$table->warning = MM_Utils::getImageUrl("error");
							$_cacheObj->accessTagIds[] = $accessTagId; 
						}
						else if($memberTypeObj->isValid()){
							$table->member_type .= $memberTypeObj->getName();
							if(!($_cacheObj->user instanceof MM_User)){
								$_cacheObj->user = $this->setUserObject($result, $id, $memberTypeObj->getId());
							}
							else{
								$_cacheObj->user->setMemberTypeId($memberTypeObj->getId());
							}
						}
						// could not find associated member type for product
						else{
							
							if(!($_cacheObj->user instanceof MM_User)){
								$defaultMemberType = new MM_MemberType();
								$defaultMemberType->getDefault();
								$table->member_type .= $defaultMemberType->getName();
								$table->warning_title = "The product associated with this order is not associated with a member type or an access tag. If imported, this member will be associated with the default member type.";
								$table->warning = MM_Utils::getImageUrl("error");
								$_cacheObj->user = $this->setUserObject($result, $id);
							}
						}
					}
					/// could not find a valid product id.
					else{
						if(!($_cacheObj->user instanceof MM_User)){
							$defaultMemberType = new MM_MemberType();
							$defaultMemberType->getDefault();
							$table->member_type .= $defaultMemberType->getName();
							$table->warning_title = "The product is not found within your site configurations.  If imported, this member will be imported using the default member type.";
							$table->warning = MM_Utils::getImageUrl("error");
							$_cacheObj->user = $this->setUserObject($result, $id);
						}
					}
				}	
				if($_cacheObj->user instanceof MM_User){
					$memberTypeId = $_cacheObj->user->getMemberTypeId();
					$mt = new MM_MemberType($memberTypeId);
					if($mt->isValid() && !$mt->isDefault()){
						$table->warning = "";
					}
					$lastOrderId = $_cacheObj->user->getLastOrderId();
					if(intval($lastOrderId)<=0){
						$lastOrderId = $_cacheObj->user->getMainOrderId();
					}
					$checkbox = "<input type='checkbox' name='order_ids[]' id='order_ids[]' value='".$lastOrderId."' />";
					$cacheObject[] = $_cacheObj;
					if(!empty($table->warning)){
						$table->warning = "<img src='".$table->warning."' title='".$table->warning_title."' />";
					}
				    $rows[] = array
				    (
				    	array('content' => $table->warning, 'attr'=>'style=\'vertical-align: middle\''),
				    	array('content' => $checkbox),
				    	array('content' => $table->name),
				    	array('content' => $table->email),
				    	array('content' => $table->purchase_date),
				    	array('content' => $table->member_type, 'attr'=>'style=\'vertical-align: middle\''),
				    	array('content' => $table->access_tag, 'attr'=>'style=\'vertical-align: middle\'')
				    );
				}
			}
		}
	
		$dataGrid->setHeaders($headers);
		$dataGrid->setRows($rows);
		
		$dgHtml = $dataGrid->generateHtml();
		
		if($dgHtml == "") {
			$dgHtml = "<p><i>No members to import.</i></p>";
		}
		MM_Session::value("import", serialize($cacheObject));
		
		$info = new stdClass();
		$info->list = $dgHtml;
		$info->display = "1";
		$content = MM_TEMPLATE::generate(MM_MODULES."/import_from_ll.php", $info);
		return new MM_Response($content);
	}
	
	private function cacheUserExists($cacheObject, $user){
		if(count($cacheObject)<=0){
			return false;
		}
		
		for($i=0; $i<count($cacheObject); $i++){
			$savedUser = $cacheObject[$i];
			if($savedUser instanceof MM_User){
				if($savedUser->getEmail() == $user->getEmail() || $savedUser->getUsername() == $user->getUsername()){
					$indexOfCache = $i;
					return $indexOfCache;
					//TODO futher decide to update by date?
				}
			}
		}
		return false;
	}
	
	private function setUserObject($result, $orderId, $memberTypeId=0){
		$user = new MM_User();
		$user->setEmail($result["email"]);
		$user->setUsername($result["email"]);
		$user->setBillingAddress($result["billing_street_address"]);	
		$user->setBillingCity($result["billing_city"]);	
		$user->setBillingState($result["billing_state"]);	
		$user->setBillingZipCode($result["billing_postcode"]);	
		$user->setBillingCountry($result["billing_country"]);
		$user->setShippingAddress($result["shipping_street_address"]);	
		$user->setShippingCity($result["shipping_city"]);	
		$user->setShippingState($result["shipping_state"]);	
		$user->setShippingZipCode($result["shipping_postcode"]);		
		$user->setShippingCountry($result["shipping_country"]);
		$user->setFirstName($result["first_name"]);	
		$user->setLastName($result["last_name"]);	
		$user->setPhone($result["customers_telephone"]);	
		$user->setCustomerId($result["customer_id"]);
		$user->setRegistrationDate($result["time_stamp"]);
		
		$lastOrder = 0;
		$lastOrderArr = explode(",", $result["child_id"]);
		if(count($lastOrderArr)>0){
			asort($lastOrderArr,SORT_NUMERIC);
			$lastOrder = array_pop($lastOrderArr);
		}
		else{
			$lastOrder = $orderId;
		}
		$user->setLastOrder($lastOrder);
		
		if(intval($result["ancestor_id"])>0){
			$user->setMainOrderId($result["ancestor_id"]);	
		}
		else{
			$user->setMainOrderId($orderId);	
		}
		
		if($memberTypeId>0){
			$user->setMemberTypeId($memberTypeId);
		}
		
		return $user;
	}
	
	private function getMostRecentMemberType($users,MM_User $existingUser){
		$breadWinner = null;
		if(is_array($users)){
			foreach($users as $userObj){
				$user = $userObj->user;
				$accessTagId = $userObj->accessTagIds;
				
				if($user instanceof MM_User){
					if($existingUser->getEmail() == $user->getEmail() || $existingUser->getUsername() == $user->getEmail()){
						$newMemberTypeId = $user->getMemberTypeId();
						if(intval($newMemberTypeId)>0){
							if(!($breadWinner instanceof MM_User)){
								$breadWinner = $user;
							}
							else{
								$breadWinnerDate = strtotime($breadWinner->getRegistrationDate());
								$userDate = strtotime($user->getRegistrationDate());
								if($breadWinnerDate<$userDate){
									$breadWinner = $user;
								}
							}
						}
					}
				}	
			}
		}
		
		if(is_null($breadWinner)){
			$breadWinner = $existingUser;
			$mt = new MM_MemberType();
			$mt->getDefault();
			$breadWinner->setMemberTypeId($mt->getId());
		}
		return $breadWinner;
	}
	
	public function importMembers($post){
		$orderIds = explode(",",preg_replace("/(\,)$/", "", $post["order_ids"]));
		if(count($orderIds)<=0 || empty($orderIds)){
			return new MM_Response("Please check at least one member to import.", MM_Response::$ERROR);
		}
		$info = new stdClass();
		$info->imported = "1";
		$info->errors = "";
		$info->new_members = "";
		$info->updated_members = "";
		LogMe::write("importMembers() : ORDER IDS : ".json_encode($post));
		$users = unserialize(MM_Session::value("import"));
		
		$sendWelcomeEmail = (isset($post["send_welcome_email"]) && $post["send_welcome_email"]=='1')?true:false;
		$sendNotifications  = (isset($post["send_notifications"]) && $post["send_notifications"]=='1')?true:false;
		if($users===false){
			$info->errors = "Unable to obtain import object, try again.";
		}
		else{
			$updatedMembers =0;
			$newMembers =0;
			if(is_array($users)){
				foreach($users as $userObj){
					$user = $userObj->user;
					$accessTagArr = $userObj->accessTagIds;
					if($user instanceof MM_User){
						$userOrderId = $user->getLastOrderId();
						if(intval($userOrderId)<=0){
							$userOrderId = $user->getMainOrderId();
						}
						
						LogMe::write("importMembers() : ".$userOrderId);
						if(array_search($userOrderId, $orderIds) === false){
							LogMe::write("importMembers() : ".$userOrderId." skipped, not in Order IDS [".json_encode($post["order_ids"])."]! ");
							unset($user);
							continue;
						}
						$newMemberTypeId = 0;
						if(!$user->exists()){
							LogMe::write("importMembers() user does not exist....");
							$userMt = $this->getMostRecentMemberType($users, $user);
							$userMt->setPassword(MM_Utils::createRandomString(7));
							if($post["use_purchase_date"]!="1" && !empty($post["custom_date"])){
								$date = Date("Y-m-d", strtotime($post["custom_date"]));
								$userMt->setRegistrationDate($date);
							}
							
							$response = $userMt->commitData();
							if($response->type==MM_Response::$ERROR){
								$info->errors = $response->message;
								return $this->showImportMembersResult($info);
							}
							
							if($sendWelcomeEmail){
								$newMemberType = new MM_MemberType($userMt->getMemberTypeId());
								if($newMemberType->isValid()){
LogMe::write("membersview.importmembers() : send welcome email ");
									$newMemberType->sendWelcomeEmail($userMt->getId());
								}
								unset($newMemberType);
							}
							
							$newMembers++;
							$user->setId($userMt->getId());
						}
						else{
							$updatedMembers++;
							$newMemberTypeId = $user->getMemberTypeId();
						}	
						
						// get fresh copy of current user and update mt or at.
						$existingUser = new MM_User($user->getId());
						$existingUser->doSendNotification =$sendNotifications;
						if(intval($newMemberTypeId)>0){
							$existingMemberType = new MM_MemberType($existingUser->getMemberTypeId());
							if($existingMemberType->isDefault() || !$existingUser->isValid()){
								$existingUser->setMemberTypeId($newMemberTypeId);
								$existingUser->commitData();
								if($sendWelcomeEmail){
									$newMemberType = new MM_MemberType($existingUser->getMemberTypeId());
									if($newMemberType->isValid()){
		LogMe::write("membersview.importmembers() : send welcome email ");
										$newMemberType->sendWelcomeEmail($existingUser->getId());
									}
								}
							}
						}
						// update access tags
					    if(count($accessTagArr)>0){
							$existingAccessTags = $existingUser->getAccessTags();
							if(is_array($existingAccessTags) && count($existingAccessTags)>0){
								foreach($existingAccessTags as $tag){
									if(isset($tag->id)){
										$key = array_search($tag->id, $accessTagArr);
										if($key>=0){
											unset($accessTagArr[$key]);
										}
									}
								}
							}
							if(count($accessTagArr)>0){
								for($i=0; $i<count($accessTagArr); $i++){
									$existingUser->addAccessTag($accessTagArr[$i],$user->getLastOrderId());
								}
							}	
						}
					}
					unset($user);
				}
			}
		}
		
		$info->updated_members = $updatedMembers;
		$info->new_members = $newMembers;
		return $this->showImportMembersResult($info);
	}
	
	private function showImportMembersResult($info){
		$content = MM_TEMPLATE::generate(MM_MODULES."/import_from_ll.php", $info);
		return new MM_Response($content);
	}
	
	public function findMembers($post){
		
		if(!isset($post["to_date"]) || !isset($post["to_date"]) || !isset($post["campaign_id"]) ){
			return new MM_Response("Could not find correct parameters.", MM_Response::$ERROR);
		}
		$results = MM_LimeLightService::findOrder($post["campaign_id"], $post["from_date"], $post["to_date"]);
		if($results instanceof MM_Response){
			return $results;
		}
		
		$time =0;
		if($results["total_orders"]>0){
			$time =  (intval($results["total_orders"])*self::$TIME_PER_ORDER)/60;
			$time = number_format($time, 2);
		}
		
		$info = new stdClass();
		$info->import_ids = $results["order_ids"];
		$info->find = "1";
		$info->time = intval(ceil($time));
		$info->total_orders = $results["total_orders"];
		$info->campaign_id = $post["campaign_id"];
		$content = MM_TEMPLATE::generate(MM_MODULES."/import_from_ll.php", $info);
		return new MM_Response($content);
	}
	
	public function getImportDialog($post){
		$content = MM_TEMPLATE::generate(MM_MODULES."/import.php", $post);
		return new MM_Response($content);
	}
	
	public function generateSearchForm($post=null)
	{
		return MM_TEMPLATE::generate(MM_MODULES."/members.form.php", $post);
	}
	
	public function generateDataGrid($post=null)
	{
		return MM_TEMPLATE::generate(MM_MODULES."/members.datagrid.php", $post);
	}
	
	private function getMemberTypeInfo($post)
	{	
		$error = new MM_Response("Could not get member type info. Invalid member type ID '".$post["mm_order_member_type"]."'", MM_Response::$ERROR);
		
		if(isset($post["mm_order_member_type"])) 
		{
			$memberType = new MM_MemberType($post["mm_order_member_type"]);
			
			$isFree = "";
			if($memberType->isValid())
			{
				if($memberType->isFree()) {
					$isFree = "yes";
				}
				else {
					$isFree = "no";
				}
			}
			$countryList = "";
			$paymentsList = "";
			$shippingList = "";
			$productId = $memberType->getRegistrationProduct();
			$product = new MM_Product($productId);
			if($product->isValid()){
				$campaignId = $product->getCampaignId();
				$countryList = MM_HtmlUtils::getCampaignCountryList($campaignId);
				$paymentsList = MM_HtmlUtils::getCampaignPaymentList($campaignId);
				$shippingList = MM_HtmlUtils::getCampaignShippingList($campaignId);
			}
			
			$onsiteBilling = "0";
			if(MM_Utils::isLimeLightInstall()){
				$onsiteBilling = "1";	
			}
			
			return new MM_Response(array(
									'is_onsite_billing'=>$onsiteBilling,
									'is_free'=>$isFree, 
									'country_list'=>$countryList,
									'payments_list'=>$paymentsList,
									'shipping_list'=>$shippingList,
				));
		}
		
		return $error;
	}
 }
?>
