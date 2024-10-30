<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
global $current_user;
if(!isset($_SESSION)){
	session_start();
}
$user = new MM_User($current_user->ID);
if(isset($userId) && $userId>0){
	$user = new MM_User($userId);
}
$startDate = Date("Y-m-d", strtotime("-7 days",strtotime(Date("Y-m-d"))));
if(isset($_GET["period"])){
	switch($_GET["period"]){
		case "7d":
			$startDate = Date("Y-m-d", strtotime("-7 days",strtotime(Date("Y-m-d"))));
			break;
		case "30d":
			$startDate = Date("Y-m-d", strtotime("-30 days",strtotime(Date("Y-m-d"))));
			break;
		case "3m":
			$startDate = Date("Y-m-d", strtotime("-90 days",strtotime(Date("Y-m-d"))));
			break;
		case "6m":
			$startDate = Date("Y-m-d", strtotime("-180 days",strtotime(Date("Y-m-d"))));
			break;
		case "1y":
			$startDate = Date("Y-m-d", strtotime("-365 days",strtotime(Date("Y-m-d"))));
			break;
		case "all":
			$startDate = "";
			break;
			
	}
}
$where = "";
if(!empty($startDate)){
	$where = " ( Date(order_date) >= Date('".$startDate."') AND Date(order_date)<=Date(NOW()) ) ";
}

$history = MM_OrderHistory::getHistory($user->getId(),0,$where,$startDate);
$orderHistoryStr = "<i>No order history available</i>";
$period = (isset($_GET["period"]))?$_GET["period"]:"7d";

$url = "?";
$moduleKey = "module";
if(is_admin()){
	$moduleKey = "admin_module";
	$userId = (isset($userId))?$userId:"";
	$url = "admin.php?page=mm_manage_members&module=details_order_history&user_id=".$userId."&";
}

$dropDown = "<span class='mm-subpage-labels'>Filter Order History</span> <select id='period' onchange=\"document.location.href='{$url}{$moduleKey}=order_history&period='+mmJQuery('#period').val();\">
<option value='7d' ".(($period=="7d")?"selected":"").">Past 7 Days</option>
<option value='30d' ".(($period=="30d")?"selected":"").">Past 30 Days</option>
<option value='3m' ".(($period=="3m")?"selected":"").">Past 3 Months</option>
<option value='6m' ".(($period=="6m")?"selected":"").">Past 6 Months</option>
<option value='1y' ".(($period=="1y")?"selected":"").">Past Year</option>
<option value='all' ".(($period=="all")?"selected":"").">All</option>
</select>";
if(is_array($history)) {
	if(!is_admin()){
		$countText = "You have ".count($history)." order(s)";
		$orderHistoryStr = "<tr><td colspan='1'>".$countText."</td><td colspan='3' align='right'>{$dropDown}</td>
	<tr class='mm-myaccount-history-header'>
		<td width='20%'><b>Order Date</b></td>
		<td width='20%'><b>Order #</b></td>
		<td><b>Product Name</b></td>
		<td width='20%'><b>Price</b></td>";
	
			$orderHistoryStr.="
	</tr>";
		
			
		foreach($history as $order) {
		
			if(is_array($order)){
				$order = MM_Utils::convertArrayToObject($order);
			}
			$orderId = $order->id;
			$orderHistoryStr .= "<tr>
		<td>".Date("m/d/Y", strtotime($order->time_stamp))."</td>	
		<td>{$orderId}</td>	
		<td>{$order->product_name}</td>	
		<td>\${$order->product_price}</td>	";
			
			
			$orderHistoryStr.="
	</tr>";			
		}
		
?>
<table width='650px' id='mm-subpage-order-history' class='mm-myaccount-details-table'>
<?php echo $orderHistoryStr; ?>

</table><?php 
	}
	else{
		
			$view = new MM_OrderHistoryView();
			$dataGrid = new MM_DataGrid($_REQUEST, "name", "asc", 10);
			$data = $view->getData($userId, $dataGrid);
			$dataGrid->setTotalRecords($data);
			$dataGrid->recordName = "order";
			
			$rows = array();
			
			$headers = array
			(	    
				'order_date'	=> array('content' => 'Date'),
			   	'order_number'		=> array('content' => 'Order Number'),
			   	'product_name'		=> array('content' => 'Product'),
			   	'price'		=> array('content' => 'Price'),
			   	'status'		=> array('content' => 'Status'),
			   	'actions'	=> array('content' => 'Actions')
			);
			
			foreach($data as $key => $item)
			{
				if(isset($_GET["debug"])){
					echo "<pre>";
					var_dump($item);
					echo "</pre>";	
				}
					
				LogMe::write("my_account_order_history.php : ".json_encode($item));
			
				$status = "";
				$user = new MM_User($item->user_id);
				$paymentId = MM_PaymentService::getDefaultPaymentMethodId($user);
				
				$productName = MM_NO_DATA;
				$productPrice =MM_NO_DATA;
				$order = new MM_Response();
				if(isset($item->order)){
					$order = $item->order;
				}
				else if(!($paymentId instanceof MM_Response)){
	            	$paymentEngine = new MM_PaymentEngine($paymentId);
					$order = $paymentEngine->viewOrder($item->id);
				}
				
				$product = new MM_Product();
				if(MM_Utils::isLimeLightInstall()){
					$product->getDataByProductId($item->product_id, "product_id", $order["campaign_id"]);
				}
				else{
					$product = new MM_Product($item->product_id);
				}
				$actions = "";
				
				$llProduct = new stdClass();
				$llProduct->product_price = $product->getPrice(true);
				$llProduct->product_name = 	$product->getName();
				$productName = $product->getName();
				$productPrice = $product->getPrice(true);
				
				if(!($order instanceof MM_Response)){
					if($order["order_status"]!=MM_Order::$STATUS_VOID_REFUND && floatval($order["amount_refunded_to_date"])<=0){
						if(floatval($llProduct->product_price)>0){
							$price = number_format(floatval($llProduct->product_price),2);
						}
						if((isset($item->is_refundable) && $item->is_refundable) || !isset($item->is_refundable)){
							$actions.= "<input type='button' name='refund' value='Issue Refund' class='button' style='width: 70px;' onclick=\"mmjs.refundOrderConfirm('{$item->id}','{$item->user_id}','".$price."','".$product->getId()."');\" />";
						}	
					}
					else{
						$status = "<img src='".MM_Utils::getImageUrl("refunded")."' alt='Order has been refunded' />";
					}
					
					if($order["is_chargeback"] == "1"){
						$status.= "<img src='".MM_Utils::getImageUrl("chargeback")."' alt='There was a chargeback on this order' />";
					}
				}
				else{
					$actions.= $order->message;	
					$actions = MM_NO_DATA;
				}
				
				if(empty($status)){
					$status = MM_NO_DATA;	
				}
				
				$orderButton = '<input type="button" class="button" value="View Order" onclick="window.open(\''.MM_LimeLightUtils::getLLOrderUrl($item->id).'\');" />';
				if(!MM_Utils::isLimeLightInstall()){
					$orderButton = "";
				}
				$actions.=$orderButton;
					
				if(empty($actions)){
					$actions = MM_NO_DATA;	
				}
				
				$assocOrder= $item->id;
			    $rows[] = array
			    (
			    	array( 'content' => Date("m/d/Y", strtotime($item->order_date))),
			    	array( 'content' => $assocOrder),
			    	array( 'content' => $productName),
			    	array( 'content' => "\$".$productPrice),
			    	array( 'content' => $status),
			    	array( 'content' => $actions),
			    );
			}
			
			$dataGrid->setHeaders($headers);
			$dataGrid->setRows($rows);
			
			$dgHtml = $dataGrid->generateHtml();
			
			if($dgHtml == "") {
				$dgHtml = "<p><i>No history.</i></p>";
			}
			?>
			<div id='mm-view-container'>
			<div id='mm-choose-refund-options'></div>
			<div class="wrap">
			  
			    <h2 class="mm-header-text">Order History</h2>
				
				<div class="mm-button-container">
					
				</div>
			
				<div class="clear"></div>
				<div style='width: 650px;'>
				<?php echo $dgHtml; ?>
				</div>
			</div></div><?php 
	}
}