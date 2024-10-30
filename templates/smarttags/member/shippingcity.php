<h2>[MM_Member_ShippingCity]</h2>

<a onclick="stl_js.insertContent('[MM_Member_ShippingCity]');" class="button-secondary">Insert Tag</a>

<h3>Description:</h3>
<p>This tag outputs the shipping city of the member associated with the ID passed, the member currently logged in or the member who's the recipient of an email being sent. Possible applications for this and other member data access tags are to display data on the screen, send as parameters to an external script to update a 3rd party database or to personalize email templates.</p>

<h3>Attributes:</h3>
<p><span class="mm-code">memberId</span> (optional) - Indicates the ID of the member to output the shipping city for.</p>
<p><span class="mm-code">urlEncode</span> (optional) - Indicates whether or not the data should be URL encoded. Acceptable values are <span class="mm-code">true</span> or <span class="mm-code">false</span>. The default value is <span class="mm-code">false</span>. If the tag is being used in a URL it is recommended that <span class="mm-code">urlEncode</span> be set to <span class="mm-code">true</span>.</p>

<h3>Usage:</h3>

<p><span class="mm-code">[MM_Member_ShippingCity memberId="1"]</span></p>
