<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
?>
<h2>Thank you for your purchase, [MM_Member_FirstName]!</h2>

You login credentials are:
<b>Username:</b> [MM_Member_Username]
<b>Password:</b> [MM_Member_Password]

<a href="[MM_Page_Login]">Click here</a> to login.

Here are your order details:
<b>Name:</b> [MM_Member_FirstName] [MM_Member_LastName]
<b>Email:</b> [MM_Member_Email]

<b>Order ID:</b> [MM_Order_ID]
<b>Order Total:</b> $[MM_Order_Total]

<b>Billing Address:</b>
[MM_Order_BillingAddress]
[MM_Order_BillingCity], [MM_Order_BillingState] [MM_Order_BillingZipCode]
[MM_Order_BillingCountry]

<b>Shipping Address:</b>
[MM_Order_ShippingAddress]
[MM_Order_ShippingCity], [MM_Order_ShippingState] [MM_Order_ShippingZipCode]
[MM_Order_ShippingCountry]

If you have any questions concerning your order, feel free to contact us at <a href="mailto:[MM_Email_Address isDefault='1']">[MM_Email_Address isDefault='1']</a>.