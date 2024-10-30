<h2>[MM_Member_Phone]</h2>

<a onclick="stl_js.insertContent('[MM_Member_Phone]');" class="button-secondary">Insert Tag</a>

<h3>Description:</h3>
<p>This tag outputs the phone number of the member associated with the ID passed, the member currently logged in or the member who's the recipient of an email being sent. Possible applications for this and other member data access tags are to display data on the screen, send as parameters to an external script to update a 3rd party database or to personalize email templates.</p>

<h3>Attributes:</h3>
<p><span class="mm-code">memberId</span> (optional) - Indicates the ID of the member to output the phone number for.</p>

<h3>Usage:</h3>

<p><span class="mm-code">[MM_Member_Phone memberId="1"]</span></p>

<p><span class="mm-code">http://www.mydomain.com/scripts/updateMember.php?id=[MM_Member_ID]&amp;...&amp;phone=[MM_Member_Phone]...</span></p>
