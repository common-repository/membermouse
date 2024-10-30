<h2>[MM_DefaultContent]</h2>

<a onclick="stl_js.insertContent('[MM_DefaultContent][/MM_DefaultContent]');" class="button-secondary">Insert Tag</a>

<h3>Description:</h3>

<p>This tag is used in conjunction with the <span class="mm-code">[MM_SmartContent]</span> tag. It allows you define the default content to be displayed to a member when they don't match any of the alternate content group requirements.</p>



<h3>Attributes:</h3>

<p>&nbsp;</p>



<h3>Usage:</h3>

<p><span class="mm-code">

[MM_SmartContent]<br/>

<br/>

[MM_DefaultContent]<br/>

Sorry, this content is only available to Gold members. If you'd like to view this content you can &lt;a href="[MM_UpgradeDowngrade_Page]"&gt;Click Here&lt;/a&gt; to upgrade.<br/>

[/MM_DefaultContent]<br/>

<br/>

[MM_AlternateContent memberTypeId="1"]<br/>

This content contains very valuable information so I don't want everyone to be able to see it. I've set it up so only members with a memberTypeId of 1 can see it.<br/>

[/MM_AlternateContent]<br/>

<br/>

[/MM_SmartContent]<br/>

</span></p>

<p>In this example, the text starting with "This content is very valuable..." inside the <span class="mm-code">[MM_AlternateContent]</span> tag is only visible to members whose member type ID is 1. If the member viewing the page doesn't have a member type of 1, the content in the <span class="mm-code">[MM_DefaultContent]</span> tag will be displayed.</p>