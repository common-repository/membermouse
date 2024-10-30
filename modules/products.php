<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
$view = new MM_ProductView();
$dataGrid = new MM_DataGrid($_REQUEST, "id", "desc", 10);
$data = $view->getData($dataGrid);
$dataGrid->setTotalRecords($data);
$dataGrid->recordName = "product";

$rows = array();

$headers = array
(	    
	'id'				=> array('content' => '<a onclick="mmjs.sort(\'id\');" href="#">ID</a>'),
   	'name'				=> array('content' => '<a onclick="mmjs.sort(\'name\');" href="#">Name</a>'),
   	'sku'				=> array('content' => '<a onclick="mmjs.sort(\'sku\');" href="#">SKU</a>'),
   	'price'				=> array('content' => '<a onclick="mmjs.sort(\'price\');" href="#">Price</a>'),
   	'is_trial'				=> array('content' => '<a onclick="mmjs.sort(\'is_trial\');" href="#">Trial</a>'),
   	'is_shippable'				=> array('content' => '<a onclick="mmjs.sort(\'is_shippable\');" href="#">Shippable</a>'),
   	'rebill_period'				=> array('content' => '<a onclick="mmjs.sort(\'rebill_period\');" href="#">Recurring</a>'),
   	'actions'			=> array('content' => 'Actions')
);

foreach($data as $key=>$item)
{	
	
    // Actions
	$actions = '<a title="Edit Product" onclick="mmjs.edit(\'mm-products-dialog\', \''.$item->id.'\', 500,495)" style="margin-left: 5px; cursor:pointer"><img src="'.MM_Utils::getImageUrl("edit").'" /></a>';
	
	if(!MM_Product::hasAssociations($item->id)){
		$actions .= '<a title="Delete Product" onclick="mmjs.remove(\''.$item->id.'\')" style="margin-left: 5px; cursor:pointer;"><img src="'.MM_Utils::getImageUrl("delete").'" /></div></a>';
	}
	
	$trial = MM_NO_DATA;
	if($item->is_trial=="1"){
    	$trial = "<img src='".MM_Utils::getImageUrl("tick")."' />";
	}
	
	$shippable = MM_NO_DATA;
	if($item->is_shippable=="1"){
    	$shippable = "<img src='".MM_Utils::getImageUrl("tick")."' />";
	}
	
	$rebillPeriod = MM_NO_DATA;
	if(intval($item->rebill_period)>0){
    	$rebillPeriod = "<img src='".MM_Utils::getImageUrl("tick")."' />";
	}
	
    $rows[] = array
    (
    	array('content' => $item->id),
    	array('content' => $item->name, 'attr' => 'class="name"'),
    	array('content' => $item->sku, 'attr' => 'class="name"'),
    	array('content' => "\$".$item->price, 'attr' => 'class="name"'),
    	array('content' => $trial, 'attr' => 'class="name"'),
    	array('content' => $shippable, 'attr' => 'class="name"'),
    	array('content' => $rebillPeriod, 'attr' => 'class="name"'),
    	array('content' => $actions),
    );
}

$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);

$dgHtml = $dataGrid->generateHtml();

if($dgHtml == "") {
	$dgHtml = "<p><i>No custom fields.</i></p>";
}
?>
<div class="wrap">
    <h2 class="mm-header-text">Products</h2>
	<div class="mm-button-container">
		<a onclick="mmjs.create('mm-products-dialog', 560,495)" class="button-secondary"><img src="<?php echo MM_Utils::getImageUrl('add'); ?>" /> Create Product</a>
	</div>
	
	<div class="clear"></div>
	
	<?php echo $dgHtml; ?>
</div>