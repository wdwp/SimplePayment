<?php
$lang['friendlyname'] = 'Simple Payment';
$lang['postinstall'] = 'Be sure to set "Use Simple Payment" permissions to use this module';
$lang['postuninstall'] = 'Module Simple Payment has been uninstalled';
$lang['really_uninstall'] = 'Really? You\'re sure you want to uninstall this module?';
$lang['uninstalled'] = 'Module Uninstalled.';
$lang['installed'] = 'Module version %s installed.';
$lang['prefsupdated'] = 'Module preferences updated.';
$lang['accessdenied'] = 'Access Denied. Please check your permissions.';
$lang['cancel'] = 'Cancel';
$lang['confirm'] = 'Confirm';
$lang['delete'] = 'Delete';
$lang['edit'] = 'Edit';
$lang['error'] = 'Error!';
$lang['submit'] = 'Submit';
$lang['upgraded'] = 'Module upgraded to version %s.';
$lang['moddescription'] = 'Payment gateways can be prepared with this module that is used by shop type modules (such as Simple Cart)';

$lang['active'] = 'Active';
$lang['addgateway'] = 'Add Gateway';
$lang['addgatewayattr'] = 'Add attribute';
$lang['addtemplate'] = 'Add template';
$lang['areyousure'] = 'Are you sure?';
$lang['areyousuregateway'] = 'Delete gateway %s?';
$lang['autoinsertedgateway'] = 'PMS found new gateway: %s';
$lang['description'] = 'Description';
$lang['gatewayadded'] = 'Gateway has been added';
$lang['gatewaycode'] = 'Code';
$lang['gatewaycodepart'] = 'Part of gateway code (for refresh)';
$lang['gatewaydeleted'] = 'Gateway has been deleted';
$lang['gatewaydescription'] = 'Description';
$lang['gateways'] = 'Gateways';
$lang['gatewayupdated'] = 'Gateway has been updated';
$lang['locked'] = 'Locked';
$lang['noattributesavailable'] = 'No attributes available';
$lang['nogatewaycodegiven'] = 'Gateway code is mandatory';
$lang['nogwattributesgiven'] = 'Attribute properties not filled, attribute not added/updated';
$lang['notemplatecontentgiven'] = 'No template content given';
$lang['notemplatenamegiven'] = 'No template name given';
$lang['options'] = 'Options';
$lang['optionsupdated'] = 'Options have been updated';
$lang['parameter'] = 'Parameter';
$lang['refreshgateway'] = 'Refresh';
$lang['setactive'] = 'Set active';
$lang['setinactive'] = 'Set inactive';
$lang['status'] = 'Status';
$lang['templateadded'] = 'Template added';
$lang['templatecontent'] = 'Content';
$lang['templatedeleted'] = 'Template deleted';
$lang['templatename'] = 'Template name';
$lang['templates'] = 'Templates';
$lang['templateupdated'] = 'Template updated';
$lang['title_templatename'] = 'Name';
$lang['value'] = 'Value';

$lang['welcome_text'] = '<p>Welcome to the Simple Payment admin section.</p>';
$lang['help'] = '<h3>What Does This Do?</h3>
<p>This module is supporting the payment flow of customers as an addition to the Simple Cart Module.</p>
<h3>How Do I Use It</h3>
<p>This module will mostly used in the back office. Since it controles information on payments. After installing it, make sure that one or more gateways are 
	turned active. One payment method is shown always on the front end: \'manual payment\'. Next to this only one of the active gateways can be selected 
	by the customer.<br>
To create your own new gateway, copy file gw.PayPal.php to for example gw.goldpaydeluxe.php. make the changes in the program needed for the \'goldpay de luxe\'
payment gateway. Use the link \'Refresh\' to include the newly created gateway in the back end. Make sure the status is active and the gateway can be selected on the front end by the customer.<br>
If the refresh function gives an error, try it again after changing the \'Part of gatewaycode..\' in the Options tab.<br>
Note:<br>
Since you have this software for free, the development team would like you to share any new gateway that you have prepared. The gateway can then be incorporated in the next release or put up in the forge separately. So let this module grow, like the developers of this module let CMSMS grow!</p>
<p>For PayPal gateway users:<br>If you have turned on to see details of the payments (so multiple items in payment overview within PayPal) and the 
shipping amount is not transferred into PayPal: Turn on override profile in the shipping part within PayPal.</p>
<h3>Support</h3>
<p>This module does not include commercial support. However, there are a number of resources available to help you with it:</p>
<ul>
<li>Additional discussion of this module may also be found in the <a href="http://forum.cmsmadesimple.org">CMS Made Simple Forums</a>.</li>
</ul>
<p>As per the GPL, this software is provided as-is. Please read the text of the license for the full disclaimer.</p>

<h3>Copyright and License</h3>
<p>Copyright &copy; 2008 - 2011.<br>
All Rights Are Reserved.</p>
<p>This module has been released under the <a href="http://www.gnu.org/licenses/licenses.html#GPL">GNU Public License</a>. You must agree to this license before using the module.</p>
';
