<h2>[MM_1ClickBuy]</h2>

<a onclick="stl_js.insertContent('[MM_1ClickBuy productId=\'0\']');" class="button-secondary">Insert Tag</a>

<h3>Description:</h3>
<p>This tag outputs the URL to buy the product associated with the product ID and campaign ID passed. When the link is clicked, if MemberMouse doesn't have the guest/member billing and shipping information on file, they are directed to the checkout process where they can fill out their billing information and/or shipping information and submit it. In this case, if it is a guest, the default member type will be assigned to them. If MemberMouse has their billing and shipping info, they'll be shown a confirmation window asking them to confirm their purchase.</p>

<h3>Attributes:</h3>
<p><span class="mm-code">productId</span> - The ID associated with the product to buy.</p>
<p><span class="mm-code">paymentMethod</span> - Payment method to be used for one-click buy.  Values include 'paypal', 'clickbank', or 'limelight'</p>

<h3>Usage:</h3>
<p><span class="mm-code">[MM_1ClickBuy productId="5"]</span></p>
<p><span class="mm-code">[MM_1ClickBuy productId="5" paymentMethod="paypal"]</span></p>