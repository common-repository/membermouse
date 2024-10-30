<?php
$sql = array();

$sql[] = "INSERT INTO `mm_smarttag_groups` (`id`, `parent_id`, `name`, `visible`) VALUES(1, 0, 'Core Page Content', 1);";
$sql[] = "INSERT INTO `mm_smarttag_groups` (`id`, `parent_id`, `name`, `visible`) VALUES(2, 0, 'Core Pages', 1);";
$sql[] = "INSERT INTO `mm_smarttag_groups` (`id`, `parent_id`, `name`, `visible`) VALUES(3, 0, 'Data Access', 1);";
$sql[] = "INSERT INTO `mm_smarttag_groups` (`id`, `parent_id`, `name`, `visible`) VALUES(4, 3, 'Email Accounts', 1);";
$sql[] = "INSERT INTO `mm_smarttag_groups` (`id`, `parent_id`, `name`, `visible`) VALUES(5, 3, 'Member Data', 1);";
$sql[] = "INSERT INTO `mm_smarttag_groups` (`id`, `parent_id`, `name`, `visible`) VALUES(6, 3, 'Order Data', 1);";
$sql[] = "INSERT INTO `mm_smarttag_groups` (`id`, `parent_id`, `name`, `visible`) VALUES(7, 0, 'Membership Management', 1);";
$sql[] = "INSERT INTO `mm_smarttag_groups` (`id`, `parent_id`, `name`, `visible`) VALUES(8, 0, 'Smart Content', 1);";
$sql[] = "INSERT INTO `mm_smarttag_groups` (`id`, `parent_id`, `name`, `visible`) VALUES(9, 0, 'eCommerce', 1);";
$sql[] = "INSERT INTO `mm_smarttag_groups` (`id`, `parent_id`, `name`, `visible`) VALUES(10, 3, 'Custom Data', 1);";

$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(100, 1, 'MM_Content_Checkout', 0);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(101, 1, 'MM_Content_LimeLightSuccess', 0);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(102, 1, 'MM_Content_LoginForm', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(103, 1, 'MM_Content_LostPasswordForm', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(104, 1, 'MM_Content_ErrorMessage', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(105, 1, 'MM_Content_MyAccount', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(106, 1, 'MM_Content_RegistrationProcess', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(107, 1, 'MM_Content_LogoutForm', 1);";

$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(200, 2, 'MM_Page_Cancellation', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(201, 2, 'MM_Page_Home', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(202, 2, 'MM_Page_Login', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(203, 2, 'MM_Page_Logout', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(204, 2, 'MM_Page_LostPassword', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(205, 2, 'MM_Page_MyAccount', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(206, 2, 'MM_Page_Registration', 1);";

$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(400, 4, 'MM_Email_Name', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(401, 4, 'MM_Email_Address', 1);";

$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(500, 5, 'MM_Member_BillingAddress', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(501, 5, 'MM_Member_BillingCity', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(502, 5, 'MM_Member_BillingCountry', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(503, 5, 'MM_Member_BillingState', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(504, 5, 'MM_Member_BillingZipCode', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(505, 5, 'MM_Member_Email', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(506, 5, 'MM_Member_FirstName', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(507, 5, 'MM_Member_ID', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(508, 5, 'MM_Member_LastName', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(509, 5, 'MM_Member_MemberTypeId', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(510, 5, 'MM_Member_MemberTypeName', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(511, 5, 'MM_Member_Password', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(512, 5, 'MM_Member_Phone', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(513, 5, 'MM_Member_ShippingAddress', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(514, 5, 'MM_Member_ShippingCity', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(515, 5, 'MM_Member_ShippingCountry', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(516, 5, 'MM_Member_ShippingState', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(517, 5, 'MM_Member_ShippingZipCode', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(518, 5, 'MM_Member_Username', 1);";

$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(600, 6, 'MM_Order_BillingAddress', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(601, 6, 'MM_Order_BillingCity', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(602, 6, 'MM_Order_BillingCountry', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(603, 6, 'MM_Order_BillingState', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(604, 6, 'MM_Order_BillingZipCode', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(605, 6, 'MM_Order_DateTime', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(606, 6, 'MM_Order_ID', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(607, 6, 'MM_Order_ProductName', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(608, 6, 'MM_Order_ProductSKU', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(609, 6, 'MM_Order_ShippingAddress', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(610, 6, 'MM_Order_ShippingCity', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(611, 6, 'MM_Order_ShippingCountry', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(612, 6, 'MM_Order_ShippingState', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(613, 6, 'MM_Order_ShippingZipCode', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(614, 6, 'MM_Order_Total', 1);";

$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(700, 7, 'MM_CancelMembership', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(701, 7, 'MM_ChangeMembership', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(702, 7, 'MM_ChooseMembership', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(703, 7, 'MM_PauseMembership', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(704, 7, 'MM_FastForwardMembership', 1);";

$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(800, 8, 'MM_SmartContent', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(801, 8, 'MM_DefaultContent', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(802, 8, 'MM_AlternateContent', 1);";

$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(900, 9, 'MM_1ClickBuy', 1);";
$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(1000, 10, 'MM_CustomField', 1);";

$sql[] = "INSERT INTO `mm_corepage_tag_requirements` (`id`, `core_page_type_id`, `smarttag_id`, `is_global`) VALUES(1, ".MM_CorePageType::$CANCELLATION.", 700, 1);";
$sql[] = "INSERT INTO `mm_corepage_tag_requirements` (`id`, `core_page_type_id`, `smarttag_id`, `is_global`) VALUES(8, ".MM_CorePageType::$CANCELLATION.", 703, 1);";
$sql[] = "INSERT INTO `mm_corepage_tag_requirements` (`id`, `core_page_type_id`, `smarttag_id`, `is_global`) VALUES(2, ".MM_CorePageType::$ERROR.", 104, 0);";
$sql[] = "INSERT INTO `mm_corepage_tag_requirements` (`id`, `core_page_type_id`, `smarttag_id`, `is_global`) VALUES(3, ".MM_CorePageType::$LOGIN_PAGE.", 102, 1);";
$sql[] = "INSERT INTO `mm_corepage_tag_requirements` (`id`, `core_page_type_id`, `smarttag_id`, `is_global`) VALUES(4, ".MM_CorePageType::$FORGOT_PASSWORD.", 103, 1);";
$sql[] = "INSERT INTO `mm_corepage_tag_requirements` (`id`, `core_page_type_id`, `smarttag_id`, `is_global`) VALUES(5, ".MM_CorePageType::$REGISTRATION.", 106, 1);";
$sql[] = "INSERT INTO `mm_corepage_tag_requirements` (`id`, `core_page_type_id`, `smarttag_id`, `is_global`) VALUES(6, ".MM_CorePageType::$MY_ACCOUNT.", 105, 1);";
$sql[] = "INSERT INTO `mm_corepage_tag_requirements` (`id`, `core_page_type_id`, `smarttag_id`, `is_global`) VALUES(7, ".MM_CorePageType::$LOGOUT_PAGE.", 107, 1);";
?>