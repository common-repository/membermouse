<h2>[MM_Order_ProductSKU]</h2>

<a onclick="stl_js.insertContent('[MM_Order_ProductSKU]');" class="button-secondary">Insert Tag</a>

<h3>Description:</h3>
<p>This tag outputs the product SKU associated with an order. It can only be used in the context of a Confirmation page associated with a product that was purchased. Possible applications for this and other order data access tags are to display data on the screen or send as parameters to an external script to update a 3rd party database.</p>

<h3>Attributes:</h3>
<p><span class="mm-code">urlEncode</span> (optional) - Indicates whether or not the data should be URL encoded. Acceptable values are <span class="mm-code">true</span> or <span class="mm-code">false</span>. The default value is <span class="mm-code">false</span>. If the tag is being used in a URL it is recommended that <span class="mm-code">urlEncode</span> be set to <span class="mm-code">true</span>.</p>

<h3>Usage:</h3>

<p><span class="mm-code">http://www.fulfillmenthouse.com/scripts/newOrder.php?id=[MM_Order_ID]&amp;...&amp;productSku=[MM_Order_ProductSKU urlEncode=&quot;true&quot;]...</span></p>
