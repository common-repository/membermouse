<h2>[MM_SmartContent]</h2>

<a onclick="stl_js.insertContent('[MM_SmartContent displayPolicy=\'showAll\'][/MM_SmartContent]');" class="button-secondary">Insert Tag</a>

<h3>Description:</h3>
<p>Using this tag in conjunction with the <span class="mm-code">[MM_DefaultContent]</span> and <span class="mm-code">[MM_AlternateContent]</span> tags allow you to display different content based on the Member Type(s) and/or Access Tag(s) associated with the member that requested the content.</p>

<h3>Attributes:</h3>
<p><span class="mm-code">displayPolicy</span> (optional) - Indicates how MemberMouse should handle instances where more than 1 alternate content block applies to the current member. Possible values are <span class="mm-code">showAll</span>, <span class="mm-code">showFirst</span>, <span class="mm-code">showLast</span>. If set to <span class="mm-code">showAll</span>, then all alternate content blocks that apply to the current member will be displayed. If set to <span class="mm-code">showFirst</span>, then only the first alternate content block that applies to the current member will be displayed. If set to <span class="mm-code">showLast</span>, then only the last alternate content block that applies to the current member will be displayed. The default value is <span class="mm-code">showAll</span>.</p>

<h3>Usage:</h3>
<p><span class="mm-code">
[MM_SmartContent displayPolicy="showAll"]<br/>
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