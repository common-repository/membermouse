<h2>[MM_AlternateContent]</h2>

<a onclick="stl_js.insertContent('[MM_AlternateContent][/MM_AlternateContent]');" class="button-secondary">Insert Tag</a>

<h3>Description:</h3>

<p>This tag is used in conjunction with the <span class="mm-code">[MM_SmartContent]</span> tag. It provides a means for displaying alternate content based on the Member Type(s) and/or Access Tag(s) associated with the member who requested the content.</p>


<h3>Attributes:</h3>

<p><span class="mm-code">memberTypeId</span> - Indicates which Member Type(s) have access to the protected content.</p>
<p><span class="mm-code">accessTagId</span> - Indicates which Access Tag(s) have access to the protected content.</p>
<p><span class="mm-code">numDaysAsMember</span> - Defines the number of days the member needs to be a member to see this content.</p>
<p><span class="mm-code">status</span> -  Indicates which member status has access to the protected content.</p>

<p>When status is used as an attribute you can use the following values:</p>
<ol>
	<li>The status of active<br/><span class="mm-code">[MM_AlternateContent status="active"]</span></li>
	<li>The status of paused<br/><span class="mm-code">[MM_AlternateContent status="paused"]</span></li>
</ol>

<p><span class="mm-code">isContentAvailable</span> - Indicates whether a given post/page is accessible by the given user.</p>

<p>When the memberTypeId, accessTagId, or isContentAvailable attributes are used they can accept values in the following formats:</p>
<ol>
	<li>A single number to specify a single value:<br/><span class="mm-code">[MM_AlternateContent memberTypeId="1"]</span></li>
	<li>A comma-deliminated list of numbers to specify two or more values:<br/><span class="mm-code">[MM_AlternateContent memberTypeId="1,2,3"]</span></li>
	<li>The keyword all to indicate that all member types or access tags apply:<br/><span class="mm-code">[MM_AlternateContent memberTypeId="all"]</span></li>
	<li>Another comma-deliminated list of numbers to specify two or more values (where '-' preceding a number means content NOT available):<br/><span class="mm-code">[MM_AlternateContent isContentAvailable="200,-201"]</span></li>
</ol>

<p>Providing multiple IDs for either memberTypeId or accessTagId results in an OR relationship. For example if you write:</p>
<p><span class="mm-code">[MM_AlternateContent memberTypeId="1,2,3"]</span></p>
<p>This means, show this content if...</p>
<p><em>memberTypeId equals 1 or 2 or 3</em></p>

<br/>
<p>If you provide multiple parameters, it result in an AND relationship. For example, if you write:</p>
<p><span class="mm-code">[MM_AlternateContent memberTypeId="1" accessTagId="4"]</span></p>
<p>This means, show this content if...</p>
<p><em>memberTypeId equals 1<br/>
AND<br/>
accessTagId equals 4</em></p>

<br/>
<p>Here's another example:</p>
<p><span class="mm-code">[MM_AlternateContent memberTypeId="1,2" accessTagId="3" numDaysAsMember="4"]</span></p>
<p>This means, show this content if...</p>
<p><em>memberTypeId equals 1 or 2<br/>
AND<br/>
accessTagId equals 3<br/>
AND<br/>
numDaysAsMember is greater than or equal to 4</em></p>


<h3>Usage:</h3>

<p><span class="mm-code">
[MM_SmartContent displayPolicy="showFirst"]<br/>
<br/>
[MM_DefaultContent]<br/>
Sorry, this content is only available to Silver, Gold and Platinum members. If you'd like to view this content you can &lt;a href="[MM_UpgradeDowngrade_Page]"&gt;Click Here&lt;/a&gt; to upgrade.<br/>
[/MM_DefaultContent]<br/>
<br/>
[MM_AlternateContent memberTypeId="1" accessTagId="2,6"]<br/>
This content is directed specifically toward members with a member type ID of 1 who also have an access tag ID of either 2 or 6.<br/>
[/MM_AlternateContent]<br/>
<br/>
[MM_AlternateContent memberTypeId="1"]<br/>
This content is directed specifically toward members with a member type ID of 1.<br/>
[/MM_AlternateContent]<br/>
<br/>
[MM_AlternateContent memberTypeId="2,3,5"]<br/>
This content is directed specifically toward members with a member type ID of 2, 3 or 5.<br/>
[/MM_AlternateContent]<br/>
<br/>
[/MM_SmartContent]
</span></p>

<p>In this example, I've authored specific content for 3 different groups:</p>
<ul>
	<li>members with a member type ID of 1,</li>
	<li>members with a member type ID of 2, 3 or 5</li>
	<li>members with a member type ID of 1 and an access tag ID of either 2 or 6.</li>
</ul>
<p>If a member visits this pages and does not fall into one of this groups, the default content will be displayed. If a member visits this page who has a <span class="mm-code">memberTypeId</span> or 1 and an <span class="mm-code">accessTagId</span> of 2, you'll notice that two alternate content blocks apply to them. Since I've set the display policy of the SmartContent block to <span class="mm-code">showFirst</span>, only the first alternate content block will be displayed.</p>