
<div class="wrap">
    <img src="<?php echo MM_Utils::getImageUrl('lrg_tools'); ?>" class="mm-header-icon" /> 
    <h2 class="mm-header-text">PHP Functions</h2>
	
	<div id="mm-form-container" style="margin-top: 5px; margin-bottom: 15px; width: 650px">	
<div class="user_formatted header_section">
        <p>When you're authoring a post or a page, MemberMouse provides you with a wide range of SmartTags you can use to create a dynamic experience for the current member. We have SmartTags to display the current member's name, email and other information. We have SmartTags you can use to switch between blocks of content based on the current member's member type.</p>
<p>This is all very helpful when you're working within WordPress' standard content authoring environment, but what if you want be able to obtain this same level of control within a custom PHP WordPress template?</p>
<p>This is where MemberMouse PHP Functions come in.</p>
<p>We've developed a number of PHP functions that expose the same valuable functionality and data you get with SmartTags. The following is a list of the functions we've developed, what they're used for and how to use them.</p>
<p>&nbsp;</p>
<h1>mm_daysAsMember</h1>
<p>This function is used to retrieve the number of days a member has  been a member. In order to return a number of days, it must be executed  within the context of a member being logged in. If it's executed outside  of that context it will return <em>false</em>.</p>
<h3>Usage</h3>
<p><em>mm_daysAsMember();</em></p>
<p>&nbsp;</p>
<h1>mm_hasAccessTag</h1>
<p>This function is used to determine if the current member logged in has a certain access tag.</p>
<h3>Parameters</h3>
<p><em>accessTagId</em> - The ID of the access tag that you want to check and see if the member logged in has it.</p>
<h3>Usage</h3>
<p><em>mm_hasAccessTag(2);</em></p>
<p>In this example, we're testing if the user logged in has the access  tag with ID equal to 2. If the member has it, the function will return <em>true</em>, otherwise, it will return <em>false</em>.</p>
<p>&nbsp;</p>
<h1>mm_isMemberType</h1>
<p>This function is used to determine if the current member logged in is of a certain member type.</p>
<h3>Parameters</h3>
<p><em>memberTypeId</em> - The ID of the member type to check and see if the member logged in is of that type.</p>
<h3>Usage</h3>
<p><em>mm_isMemberType(4);</em></p>
<p>In this example, we're testing if the user logged in is a member type  with ID equal to 4. If the member is of member type 4, the function  will return <em> true</em>, otherwise, it will return <em>false</em>.</p>
<p>&nbsp;</p>
<h1>mm_smarttag</h1>
<p>This function is used to execute tags from the SmartTag Library.</p>
<h3>Parameters</h3>
<p><em>smartTagExpression</em> - A string representing the SmartTag and associated parameters to execute (i.e. [MM_<em>SmartTagName</em> parameter1='<em>value</em>' parameter2='<em>value</em>']).</p>
<p><em>output</em> - This indicates whether to output the result of the  SmartTag execution to the screen or to return it as a value to store in a  variable. By default this parameter is set to <em>true</em>, which outputs the result to the screen.</p>
<h3>Usage</h3>
<p><em>mm_smarttag("[MM_Member_FirstName]");</em></p>
<p>In this example, we're executing the <em>[MM_Member_FirstName]</em> SmartTag. This SmartTag is responsible for outputting the logged in  member's first name. When passed to this function, the SmartTag will be  executed and the member's first name will be output.</p>
<p><em>$firstName = mm_smarttag("[MM_Member_FirstName]", false);</em></p>
<p>This example, is similar to the previous example with these exception that we've pass <em>false</em> as the <em>output</em> parameter. The result will be that the SmartTag will be executed and  the member's first name will be returned and stored in the <em>$firstName</em> variable.</p>
<p>Here's another example:</p>
<p><em><span class="item">&lt;a href="&lt;?php mm_smarttag("[MM_ChooseMembership memberTypeId='1']") ?&gt;"&gt;Sign Up for Basic&lt;/a&gt;</span></em></p>
<p><span class="item">In this example, we're executing the <em>[MM_ChooseMembership]</em> SmartTag. This SmartTag is responsible for outputing a URL that when  clicked will take the user to the registration process and preselect the  specified member type.<br></span></p>
    <p>&nbsp;</p>
<h1>mm_IsContentAvailable</h1>
<p>This function is used to determine if the given post or page is accessible to the user.</p>
<h3>Parameters</h3>
<p><em>postIds</em> - (Optional) Comma delimited list of post/page ids to check if content is available, otherwise uses given post id.</p>
<p><em>userId</em> - (Optional) ID of user to reference when checking if content is available, otherwise uses current logged in user.</p>
<h3>Usage</h3>
<p><em>mm_IsContentAvailable();</em></p>
<p>In this example, we're testing if the user logged in has the given page or post they are on as available content.</p>
    
      </div>
      </div>
      </div>