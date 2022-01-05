<?php
#-------------------------------------------------------------------------
# Module: Payment Made Simple - A module for handling payments with CMS - CMS Made Simple
# Copyright (c) 2008 by Duketown
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
# The module's homepage is: http://dev.cmsmadesimple.org/projects/pms/
#
#-------------------------------------------------------------------------

$gCms = cmsms(); if( !is_object($gCms) ) exit;

$current_version = $oldversion;
switch($current_version)
{
	case "1.0.0":
  		$current_version = '1.0.1';
		
	case '1.0.1':
		$current_version = '1.0.2';
	
	case '1.0.2':
		$current_version = '1.0.3';
	
	case '1.0.3':
		// Set preference(s)
		$this->SetPreference('gatewaycodepart', '1');

		$current_version = '1.0.4.';
	
	case '1.0.4':
	case '1.0.4.':
		$db = cmsms()->GetDb();
		$query = 'SELECT * FROM '.cms_db_prefix().'module_pms_gateways WHERE UPPER(gateway_code) = UPPER( ? )';
		$row = $db->GetRow($query, array('PayPal') );
		if ($row)
		{
			$gateway_id = $row['gateway_id'];
		}
		// Insert new variables for PayPal to handle one line payments or a number of item detail lines
		$gwparm = 'cartstyle';
		$gwdescription = 'Show summary (0) or details (1) in payment request?';
		$gwvalue = '0';
		// Set all attributes to locked
		$gwlocked = 1;
		$gwattr_id = $db->GenID(cms_db_prefix()."module_pms_gwattributes_seq");
		$query = 'INSERT INTO '.cms_db_prefix().'module_pms_gwattributes (gwattr_id, gwgateway_id, gwparm, gwdescription, gwvalue, gwlocked )
			VALUES (?,?,?,?,?,?)';
		$db->Execute($query, array($gwattr_id, $gateway_id, $gwparm, $gwdescription, $gwvalue, $gwlocked));
		// Prepare return URL
		$gwparm = 'return';
		$gwdescription = 'URL where your customer will be returned after completing payment';
		$gwvalue = $config['root_url'].DIRECTORY_SEPARATOR.'index.php';
		$gwattr_id = $db->GenID(cms_db_prefix()."module_pms_gwattributes_seq");
		$db->Execute($query, array($gwattr_id, $gateway_id, $gwparm, $gwdescription, $gwvalue, $gwlocked));
		// Prepare return URL when payment is canceled
		$gwparm = 'cancel_return';
		$gwdescription = 'URL where your customer will be returned after cancelling payment';
		$gwvalue = $config['root_url'].DIRECTORY_SEPARATOR.'index.php';
		$gwattr_id = $db->GenID(cms_db_prefix()."module_pms_gwattributes_seq");
		$db->Execute($query, array($gwattr_id, $gateway_id, $gwparm, $gwdescription, $gwvalue, $gwlocked));
		
		$current_version = '1.0.5';
	
	case '1.0.5':
		$db = cmsms()->GetDb();
		$query = 'SELECT * FROM '.cms_db_prefix().'module_pms_gateways WHERE UPPER(gateway_code) = UPPER( ? )';
		$row = $db->GetRow($query, array('PayPal') );
		if ($row)
		{
			$gateway_id = $row['gateway_id'];
		}

		$gwparm = 'language_code';
		$gwdescription = 'The code of the language of PayPal screens for customer (EN, FR, DE, IT, JP, ES, GB)';
		$gwvalue = 'EN';
		$gwlocked = 1;
		$gwattr_id = $db->GenID(cms_db_prefix()."module_pms_gwattributes_seq");
		$query = 'INSERT INTO '.cms_db_prefix().'module_pms_gwattributes (gwattr_id, gwgateway_id, gwparm, gwdescription, gwvalue, gwlocked )
			VALUES (?,?,?,?,?,?)';
		$db->Execute($query, array($gwattr_id, $gateway_id, $gwparm, $gwdescription, $gwvalue, $gwlocked));

		$current_version = '1.1';
	
	case '1.1':

		$current_version = '1.2';
	
	case '1.2':
		$db = cmsms()->GetDb();
		$dict = NewDataDictionary($db);
		
		// Table schema description for table: Payments
		$flds = "
			payment_id I KEY,
			order_id I,
			amountpaid F,
			transaction_id C(40),
			entrance_cd C(40),
			bank C(40),
			status C(10),
			payment_date " . CMS_ADODB_DT . ",
			create_date " . CMS_ADODB_DT . "
			";
		
		// Create it. This should do error checking.
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_pms_payments',$flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);
		// Create a sequence
		$db->CreateSequence(cms_db_prefix().'module_pms_payments_seq');
		$current_version = '1.3';
	case '1.3':
		$current_version = '1.4';
	case '1.4':
		$current_version = '1.5';
	case '1.5':
		$current_version = '1.6';
	case '1.6':
		break;
}

// Log the upgrade in the admin audit trail
$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('upgraded',$this->GetVersion()));

?>