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
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------

$gCms = cmsms();
if (!is_object($gCms)) exit;
$db = cmsms()->GetDb();

// MySql-specific, but ignored by other database
$taboptarray = array('mysql' => 'ENGINE=MyISAM');

$dict = NewDataDictionary($db);

// Table schema description of payment types
$flds = "
	gateway_id I KEY,
	gateway_code C(20),
	description C(80),
	active I2
	";

// Create it.
$sqlarray = $dict->CreateTableSQL(
	cms_db_prefix() . "module_pms_gateways",
	$flds,
	$taboptarray
);
$dict->ExecuteSQLArray($sqlarray);

// Create a sequence
$db->CreateSequence(cms_db_prefix() . "module_pms_gateways_seq");

// Table schema for gateway attributes
$flds = "
	gwattr_id I KEY,
	gwgateway_id I,
	gwparm C(30),
	gwdescription C(80),
	gwvalue C(80),
	gwlocked I
	";

// Create it
$sqlarray = $dict->CreateTableSQL(
	cms_db_prefix() . "module_pms_gwattributes",
	$flds,
	$taboptarray
);
$dict->ExecuteSQLArray($sqlarray);

// Create a sequence
$db->CreateSequence(cms_db_prefix() . "module_pms_gwattributes_seq");

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
$sqlarray = $dict->CreateTableSQL(cms_db_prefix() . 'module_pms_payments', $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);
// Create a sequence
$db->CreateSequence(cms_db_prefix() . 'module_pms_payments_seq');

// Set preference(s)
$this->SetPreference('gatewaycodepart', '1');

// Create a permission
$this->CreatePermission('Use SimplePayment', 'Use Simple Payment');
$this->CreatePermission('Modify SimplePayment', 'Modify Simple Payment');

// Log that installation has been done in the admin audit trail
$this->Audit(0, $this->Lang('friendlyname'), $this->Lang('installed', $this->GetVersion()));
