<?php

/*******************************************************************************
 *                      PHP Paypal IPN Integration Class
 *******************************************************************************
 *      Author:     Micah Carrick & extended for CMSMS by Duketown
 *      Email:      email@micahcarrick.com
 *      Website:    http://www.micahcarrick.com
 *
 *      File:       paypal.class.php
 *      Version:    1.3.0
 *      Copyright:  (c) 2005 - Micah Carrick
 *                  You are free to use, distribute, and modify this software
 *                  under the terms of the GNU General Public License.  See the
 *                  included license.txt file.
 *
 *******************************************************************************
 *  VERION HISTORY:
 *      v1.3.0 [10.10.2005] - Fixed it so that single quotes are handled the
 *                            right way rather than simple stripping them.  This
 *                            was needed because the user could still put in
 *                            quotes.
 *
 *      v1.2.1 [06.05.2005] - Fixed typo from previous fix :)
 *
 *      v1.2.0 [05.31.2005] - Added the optional ability to remove all quotes
 *                            from the paypal posts.  The IPN will come back
 *                            invalid sometimes when quotes are used in certian
 *                            fields.
 *
 *      v1.1.0 [05.15.2005] - Revised the form output in the submit_paypal_post
 *                            method to allow non-javascript capable browsers
 *                            to provide a means of manual form submission.
 *
 *      v1.0.0 [04.16.2005] - Initial Version
 *
 *******************************************************************************
 *  DESCRIPTION:
 *
 *      NOTE: See www.micahcarrick.com for the most recent version of this class
 *            along with any applicable sample files and other documentaion.
 *
 *      This file provides a neat and simple method to interface with paypal and
 *      The paypal Instant Payment Notification (IPN) interface.  This file is
 *      NOT intended to make the paypal integration "plug 'n' play". It still
 *      requires the developer (that should be you) to understand the paypal
 *      process and know the variables you want/need to pass to paypal to
 *      achieve what you want.
 *
 *      This class handles the submission of an order to paypal aswell as the
 *      processing an Instant Payment Notification.
 *
 *      This code is based on that of the php-toolkit from paypal.  I've taken
 *      the basic principals and put it in to a class so that it is a little
 *      easier--at least for me--to use.  The php-toolkit can be downloaded from
 *      http://sourceforge.net/projects/paypal.
 *
 *      To submit an order to paypal, have your order form POST to a file with:
 *
 *          $p = new pmspaypal_class;
 *          $p->add_field('business', 'somebody@domain.com');
 *          $p->add_field('first_name', $_POST['first_name']);
 *          ... (add all your fields in the same manor)
 *          $p->submit_paypal_post();
 *
 *      To process an IPN, have your IPN processing file contain:
 *
 *          $p = new pmspaypal_class;
 *          if ($p->validate_ipn()) {
 *          ... (IPN is verified.  Details are in the ipn_data() array)
 *          }
 *
 *
 *      In case you are new to paypal, here is some information to help you:
 *
 *      1. Latest info found on http://www.paypal.com. Next to you can check out:
 *         https://www.paypal.com/cgi-bin/webscr?cmd=p/pdn/howto_checkout-outside.
 *         This gives you all the information you need including the fields you can
 *         pass to paypal (using add_field() with this class) aswell as all the fields
 *         that are returned in an IPN post (stored in the ipn_data() array in
 *         this class).  It also diagrams the entire transaction process.
 *
 *      2. Create a "sandbox" account for a buyer and a seller.  This is just
 *         a test account(s) that allow you to test your site from both the
 *         seller and buyer perspective.  The instructions for this is available
 *         at https://developer.paypal.com/ as well as a great forum where you
 *         can ask all your paypal integration questions.  Make sure you follow
 *         all the directions in setting up a sandbox test environment, including
 *         the addition of fake bank accounts and credit cards.
 *
 *******************************************************************************
 */

class pmspaypal_class extends CMSModule
{

	var $last_error;				// holds the last error encountered

	var $ipn_log;				// bool: log IPN results to text file?

	var $ipn_log_file;				// filename of the IPN log
	var $ipn_response;			// holds the IPN response from paypal
	var $ipn_data = array();		// array contains the POST values for IPN

	var $fields = array();			// array holds the fields to submit to paypal

	function GetVersion()
	{
		return '1.0';
	}

	function pmspaypal_class()
	{

		// Initialization constructor.  Called when class is created.

		$this->last_error = '';

		$this->ipn_log_file = '.ipn_audit_trail.txt';
		if ($this->getGatewayValue('PayPal', 'logfile') <> '') {
			$this->ipn_log_file = $this->getGatewayValue('PayPal', 'logfile');
		}
		$this->ipn_log = true;
		$this->ipn_response = '';

		// Populate $fields array with a few default values.
		// See the paypal documentation for a list of fields and their data types.
		// These default values can be overwritten by the calling script.

		$this->add_field('rm', '2');           // Return method = POST
		// Prepare setting if only one line with amount or a cart situation with multiple lines possible
		if ($this->getGatewayValue('PayPal', 'cartstyle') == '0') {
			$this->add_field('cmd', '_xclick');
		} else {
			$this->add_field('cmd', '_cart');
			$this->add_field('upload', '1');
		}
	}

	function add_field($field, $value)
	{
		// adds a key=>value pair to the fields array, which is what will be
		// sent to paypal as POST variables.  If the value is already in the
		// array, it will be overwritten.

		$this->fields[$field] = $value;
	}

	function submit_payment()
	{
		// this function actually generates an entire HTML page consisting of
		// a form with hidden elements which is submitted to paypal via the
		// BODY element's onLoad attribute.  We do this so that you can validate
		// any POST vars from you custom form before submitting to paypal.  So
		// basically, you'll have your own form which is submitted to your script
		// to validate the data, which in turn calls this function to create
		// another hidden form and submit to paypal.

		// The user will briefly see a message on the screen that reads:
		// "Please wait, your order is being processed..." and then immediately
		// is redirected to paypal.

		$gateway_code = 'PayPal';
		$this->add_field('business', $this->getGateWayValue($gateway_code, 'business_email'));
		//$this->add_field('currency_code', $orderheader['currency']);
		// Check if a language code has been passed. If not use a default
		if (!isset($this->fields['lc'])) {
			$language_code = $this->getGateWayValue($gateway_code, 'language_code');
			if ($language_code != false) {
				$this->add_field('lc', $language_code);
			} else {
				$this->add_field('lc', 'EN');
			}
		}
		// Check if IPN is in use
		$this->add_field('notify_url', $this->getGateWayValue($gateway_code, 'notify_url'));

		if ($this->getGatewayValue('PayPal', 'environment') == 'live') {
			$this->paypal_url = $this->getGatewayValue($gateway_code, 'url_live');
		} else {
			$this->paypal_url = $this->getGatewayValue($gateway_code, 'url_test');
		}
		// To get a dump of all fields, prepare a parameter in the back end for the PayPal gateway
		// with name debug and value yes (shortly show the contents of fields or die (show and stop)
		$dumphiddenfields = $this->getGatewayValue($gateway_code, 'debug');
		if ($dumphiddenfields == 'yes' || $dumphiddenfields == 'die') {
			$this->dump_fields();
			if ($dumphiddenfields == 'die') {
				die();
			}
		}
		$this->smarty->assign('paypal_url', $this->paypal_url);
		$entryarray = array();
		foreach ($this->fields as $name => $value) {
			$onerow = new stdClass();
			$onerow->name = $name;
			$onerow->value = $value;
			$entryarray[] = $onerow;
		}
		$this->smarty->assign_by_ref('items', $entryarray);
		$this->smarty->assign('itemcount', count($entryarray));

		$template = 'pms_gateway_PayPal';
		echo $this->ProcessTemplateFromDatabase($template, '', true, 'SimplePayment');
		die(); // Left over from previous version

	}

	function validate_ipn()
	{

		// Parse the paypal URL
		$url_parsed = parse_url($this->paypal_url);

		// Generate the post string from the _POST vars aswell as load the
		// _POST vars into an arry so we can play with them from the calling script.
		$post_string = '';
		foreach ($_POST as $field => $value) {
			$this->ipn_data["$field"] = $value;
			$post_string .= $field . '=' . urlencode(stripslashes($value)) . '&';
		}
		// Append ipn command
		$post_string .= "cmd=_notify-validate";

		// Open the connection to paypal
		$fp = fsockopen($url_parsed['host'], 443, $err_num, $err_str, 30);
		if (!$fp) {
			// Could not open the connection.
			// If loggin is on, the error message will be in the log.
			$this->last_error = "fsockopen error no. $err_num: $err_str";
			$this->log_ipn_results(false);
			return false;
		} else {

			// Post the data back to paypal
			fputs($fp, "POST $url_parsed[path] HTTP/1.1\r\n");
			fputs($fp, "Host: $url_parsed[host]\r\n");
			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-length: " . strlen($post_string) . "\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			fputs($fp, $post_string . "\r\n\r\n");

			// Loop through the response from the server and append to variable
			while (!feof($fp)) {
				$this->ipn_response .= fgets($fp, 1024);
			}

			// Close connection
			fclose($fp);
		}

		if (preg_match("VERIFIED", $this->ipn_response)) {

			// Valid IPN transaction.
			$this->log_ipn_results(true);
			return true;
		} else {

			// Invalid IPN transaction.  Check the log for details.
			$this->last_error = 'IPN Validation Failed.';
			$this->log_ipn_results(false);
			return false;
		}
	}

	function log_ipn_results($success)
	{

		// is logging turned off?
		if (!$this->ipn_log) return;

		// Timestamp
		$text = '[' . date('m/d/Y g:i A') . '] - ';

		// Success or failure being logged?
		if ($success) $text .= "SUCCESS!\n";
		else $text .= 'FAIL: ' . $this->last_error . "\n";

		// Log the POST variables
		$text .= "IPN POST Vars from Paypal:\n";
		foreach ($this->ipn_data as $key => $value) {
			$text .= "$key=$value, ";
		}

		// Log the response from the paypal server
		$text .= "\nIPN Response from Paypal Server:\n " . $this->ipn_response;

		// Write to log
		$config = cmsms()->GetConfig();
		$logfile = $config['uploads_path'] . DIRECTORY_SEPARATOR . 'SimplePayment' . DIRECTORY_SEPARATOR . $this->ipn_log_file;
		$fp = fopen($logfile, 'a');
		fwrite($fp, $text . "\n\n");

		// Close file
		fclose($fp);
	}

	function dump_fields()
	{

		// Used for debugging, this function will output all the field/value pairs
		// that are currently defined in the instance of the class using the
		// add_field() function.

		echo "<h3>pmspaypal_class->dump_fields() Output:</h3>";
		echo "<table width=\"95%\" border=\"1\" cellpadding=\"2\" cellspacing=\"0\">
			<tr>
				<td bgcolor=\"black\"><b><font color=\"white\">Field Name</font></b></td>
				<td bgcolor=\"black\"><b><font color=\"white\">Value</font></b></td>
			</tr>";

		ksort($this->fields);
		foreach ($this->fields as $key => $value) {
			echo "<tr><td>$key</td><td>" . urldecode($value) . "&nbsp;</td></tr>";
		}

		echo "</table><br>";
	}

	function getGateWayValue($gateway_code, $gatewayparm)
	{
		$db = cmsms()->GetDb();
		// Retrieve the internal id for this gateway. Passed is
		$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_pms_gwattributes, ' . cms_db_prefix() . 'module_pms_gateways
			 WHERE lower(gateway_code) = lower( ? ) AND gwgateway_id = gateway_id AND
			 lower(gwparm) = lower( ? )';
		$row = $db->GetRow($query, array($gateway_code, $gatewayparm));
		if (isset($row['gwvalue'])) {
			return $row['gwvalue'];
		} else {
			return false;
		}
	}

	function InstallGateWay($gateway_id)
	{
		// Installing PayPal will include inserting a number of semi fixed attributes
		$db = cmsms()->GetDb();
		// Set all attributes to locked
		$gwlocked = 1;

		$gwparm = 'business_email';
		$gwdescription = 'To which email address is PayPal connected';
		$gwvalue = 'payment@paypal.space';
		$gwattr_id = $db->GenID(cms_db_prefix() . "module_pms_gwattributes_seq");
		$query = 'INSERT INTO ' . cms_db_prefix() . 'module_pms_gwattributes (gwattr_id, gwgateway_id, gwparm, gwdescription, gwvalue, gwlocked )
			VALUES (?,?,?,?,?,?)';
		$db->Execute($query, array($gwattr_id, $gateway_id, $gwparm, $gwdescription, $gwvalue, $gwlocked));

		$gwparm = 'environment';
		$gwdescription = 'Run PayPal live or test?';
		$gwvalue = 'test';
		$gwattr_id = $db->GenID(cms_db_prefix() . "module_pms_gwattributes_seq");
		$query = 'INSERT INTO ' . cms_db_prefix() . 'module_pms_gwattributes (gwattr_id, gwgateway_id, gwparm, gwdescription, gwvalue, gwlocked )
			VALUES (?,?,?,?,?,?)';
		$db->Execute($query, array($gwattr_id, $gateway_id, $gwparm, $gwdescription, $gwvalue, $gwlocked));

		$gwparm = 'numberofcents';
		$gwdescription = 'Number of decimals/cents allowed';
		$gwvalue = 2;
		$gwattr_id = $db->GenID(cms_db_prefix() . "module_pms_gwattributes_seq");
		$db->Execute($query, array($gwattr_id, $gateway_id, $gwparm, $gwdescription, $gwvalue, $gwlocked));
		$gwparm = 'logfile';
		$gwdescription = 'The log file that will contain the transfer audit trail';
		$gwvalue = '.ipn_audit_trail.txt';
		$gwattr_id = $db->GenID(cms_db_prefix() . "module_pms_gwattributes_seq");
		$query = 'INSERT INTO ' . cms_db_prefix() . 'module_pms_gwattributes (gwattr_id, gwgateway_id, gwparm, gwdescription, gwvalue, gwlocked )
			VALUES (?,?,?,?,?,?)';
		$db->Execute($query, array($gwattr_id, $gateway_id, $gwparm, $gwdescription, $gwvalue, $gwlocked));

		$gwparm = 'url_test';
		$gwdescription = 'The URL to the test site of PayPal';
		$gwvalue = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		$gwattr_id = $db->GenID(cms_db_prefix() . "module_pms_gwattributes_seq");
		$query = 'INSERT INTO ' . cms_db_prefix() . 'module_pms_gwattributes (gwattr_id, gwgateway_id, gwparm, gwdescription, gwvalue, gwlocked )
			VALUES (?,?,?,?,?,?)';
		$db->Execute($query, array($gwattr_id, $gateway_id, $gwparm, $gwdescription, $gwvalue, $gwlocked));

		$gwparm = 'url_live';
		$gwdescription = 'The URL to your live account at PayPal';
		$gwvalue = 'https://www.paypal.com/cgi-bin/webscr';
		$gwattr_id = $db->GenID(cms_db_prefix() . "module_pms_gwattributes_seq");
		$query = 'INSERT INTO ' . cms_db_prefix() . 'module_pms_gwattributes (gwattr_id, gwgateway_id, gwparm, gwdescription, gwvalue, gwlocked )
			VALUES (?,?,?,?,?,?)';
		$db->Execute($query, array($gwattr_id, $gateway_id, $gwparm, $gwdescription, $gwvalue, $gwlocked));

		$gwparm = 'cartstyle';
		$gwdescription = 'Show summary (0) or details (1) in payment request?';
		$gwvalue = '0';
		$gwattr_id = $db->GenID(cms_db_prefix() . "module_pms_gwattributes_seq");
		$query = 'INSERT INTO ' . cms_db_prefix() . 'module_pms_gwattributes (gwattr_id, gwgateway_id, gwparm, gwdescription, gwvalue, gwlocked )
			VALUES (?,?,?,?,?,?)';
		$db->Execute($query, array($gwattr_id, $gateway_id, $gwparm, $gwdescription, $gwvalue, $gwlocked));

		$gwparm = 'language_code';
		$gwdescription = 'The code of the language of PayPal screens for customer (EN, FR, DE, IT, JP, ES, GB)';
		$gwvalue = 'EN';
		$gwattr_id = $db->GenID(cms_db_prefix() . "module_pms_gwattributes_seq");
		$query = 'INSERT INTO ' . cms_db_prefix() . 'module_pms_gwattributes (gwattr_id, gwgateway_id, gwparm, gwdescription, gwvalue, gwlocked )
			VALUES (?,?,?,?,?,?)';
		$db->Execute($query, array($gwattr_id, $gateway_id, $gwparm, $gwdescription, $gwvalue, $gwlocked));

		$gwparm = 'itemdesc';
		$gwdescription = 'The item description as used in the payment';
		$gwvalue = 'Products from CMSMadeSimple';
		$gwattr_id = $db->GenID(cms_db_prefix() . "module_pms_gwattributes_seq");
		$db->Execute($query, array($gwattr_id, $gateway_id, $gwparm, $gwdescription, $gwvalue, $gwlocked));

		$root = cmsms()->config['root_url'];
		$gwparm = 'return';
		$gwdescription = 'URL where your customer will be returned after completing payment';
		$gwvalue = $root . DIRECTORY_SEPARATOR . 'index.php';
		$gwattr_id = $db->GenID(cms_db_prefix() . "module_pms_gwattributes_seq");
		$db->Execute($query, array($gwattr_id, $gateway_id, $gwparm, $gwdescription, $gwvalue, $gwlocked));

		$gwparm = 'cancel_return';
		$gwdescription = 'URL where your customer will be returned after cancelling payment';
		$gwvalue = $root . DIRECTORY_SEPARATOR . 'index.php';
		$gwattr_id = $db->GenID(cms_db_prefix() . "module_pms_gwattributes_seq");
		$db->Execute($query, array($gwattr_id, $gateway_id, $gwparm, $gwdescription, $gwvalue, $gwlocked));

		// For the final overview a template is handy
		$template = '<!-- Automatically generated: for future use --><br />';
		$template .= '<!-- The developer can make use of this template. Since the name of this template starts of with
			\'pms_gateway\', you are not able to delete it.<br />';
		$template .= '<!-- For more information, search the forum or ask the author for information. --><br />';
		$template .= '{literal}
  <script type="text/javascript">
    var ihtml = \'<center><span style="font-family: verdana;">\' +
\'Please wait, your order is being processed and you will be redirected to the PayPal website.<br />If you are not automatically redirected to PayPal within 5 seconds...<input type="submit"  value="Click Here"></span></center>\' +
\'<form method="post" name="paypal_form" action="{/literal}{$paypal_url}{literal}">\' +
\'{/literal}{foreach from=$items item=entry}{literal}\' +
   \'<input type="hidden" name="{/literal}{$entry->name}{literal}" value="{/literal}{$entry->value}{literal}" />\' +
\'{/literal}{/foreach}{literal}\' +
\'</fo\' + \'rm>\';
    window.onload=function(){
       document.body.innerHTML = ihtml;
       document.forms[\'paypal_form\'].submit();
    }
  </script>
{/literal}';
		$this->SetTemplate('pms_gateway_PayPal', $template, 'SimplePayment');
	}
}
