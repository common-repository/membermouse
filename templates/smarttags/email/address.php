<h2>[MM_Email_Address]</h2>

<a onclick="stl_js.insertContent('[MM_Email_Address emailId=\'\']');" class="button-secondary">Insert Tag</a>

<h3>Description:</h3>
<p>This tag outputs either the default email address or the email address associated with the email account associated with the ID passed.</p>

<h3>Attributes:</h3>
<p><span class="mm-code">isDefault</span> - Set to <span class="mm-code">1</span> if you want to output data associated with the default email account.</p>
<p><span class="mm-code">emailId</span> - Takes a single integer that is associated with the email account to retrieve data from.</p>

<h3>Usage:</h3>
<p><span class="mm-code">&lt;a href=&quot;mailto:[MM_Email_Address emailId=&quot;2&quot;]&quot;&gt;[MM_Email_Name emailId=&quot;2&quot;]&lt;/a&gt;</span></p>

<p>In this example, utilizing the <span class="mm-code">[MM_Email_Address]</span> and <span class="mm-code">[MM_Email_Name]</span> tags, we created a dynamic email link that will always be in sync with the email configuration we define in the MemberMouse Control Center associated with the ID passed.</p>
