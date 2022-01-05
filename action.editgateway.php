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

$themeObject = \cms_utils::get_theme_object();

if (!$this->CheckPermission('Modify SimplePayment')) {
	echo $this->ShowErrors($this->Lang('accessdenied', array('Modify SimplePayment')));
	return;
}

if (isset($params['cancel'])) {
	$params = array('active_tab' => 'gateways', 'tab_message' => $this->Lang('cancel'));
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}

if (isset($params['gateway_id'])) {
	$gateway_id = $params['gateway_id'];
} else {
	// Returned from delete/update attribute, use different parameter
	$gateway_id = $params['gwgateway_id'];
}

$description = '';
if (isset($params['description'])) {
	$description = trim($params['description']);
}

$active = 0;
if (isset($params['active'])) {
	$active = $params['active'];
}

$gateway_code = '';
if (isset($params['gateway_code']) and isset($params['submit'])) {
	$gateway_code = trim($params['gateway_code']);
	if ($gateway_code != '') {
		$query = 'UPDATE ' . cms_db_prefix() . 'module_pms_gateways SET gateway_code = ?, description = ?, active = ? WHERE gateway_id = ?';
		$db->Execute($query, array($gateway_code, $description, $active, $gateway_id));

		$params = array('tab_message' => $this->Lang('gatewayupdated'), 'active_tab' => 'gateways');
		$this->Redirect($id, 'defaultadmin', $returnid, $params);
	} else {
		echo $this->ShowErrors($this->Lang('nogatewaycodegiven'));
	}
} else {
	$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_pms_gateways WHERE gateway_id = ?';
	$row = $db->GetRow($query, array($gateway_id));

	if ($row) {
		$gateway_code = $row['gateway_code'];
		$description = $row['description'];
		$active = $row['active'];
	}
}

$this->smarty->assign('gatewaycodetext', $this->Lang('gatewaycode'));
$this->smarty->assign('gatewaydesctext', $this->Lang('description'));
$this->smarty->assign('statustext', $this->Lang('status'));

// Display template
$this->smarty->assign('startform', $this->CreateFormStart($id, 'editgateway', $returnid));
$this->smarty->assign('endform', $this->CreateFormEnd());
$this->smarty->assign('gwcodetext', $this->Lang('gatewaycode'));
$this->smarty->assign('inputgwcode', $this->CreateInputText($id, 'gateway_code', $gateway_code, 20, 20, 'class="defaultfocus"'));
$this->smarty->assign('gwdesctext', $this->Lang('gatewaydescription'));
$this->smarty->assign('inputgwdesc', $this->CreateInputText($id, 'description', $description, 40, 40));
$this->smarty->assign('activetext', $this->Lang('active'));
$this->smarty->assign('inputactive', $this->CreateInputCheckbox($id, 'active', 1, $active));
$this->smarty->assign('hidden', $this->CreateInputHidden($id, 'gateway_id', $gateway_id));
$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));

// Check if there are any attributes available. If so, show them
$this->smarty->assign('gwedit', '0');
if (isset($params['gwparm']) and isset($params['submitattrid'])) {
	$gwparm = strtoupper(trim($params['gwparm']));

	$gwdescription = isset($params['gwdescription']) ? trim($params['gwdescription']) : '';

	$gwvalue = isset($params['gwvalue']) ? trim($params['gwvalue']) : '';

	$gwlocked = isset($params['gwlock']) ? $params['gwlock'] : 0;

	if ($gwparm != '' and $gwdescription != '' and $gwvalue != '') {
		$gwattr_id = $db->GenID(cms_db_prefix() . "module_pms_gwattributes_seq");
		$query = 'INSERT INTO ' . cms_db_prefix() . 'module_pms_gwattributes (gwattr_id, gwgateway_id, gwparm, gwdescription, gwvalue, gwlocked )
			VALUES (?,?,?,?,?,?)';
		$db->Execute($query, array($gwattr_id, $gateway_id, $gwparm, $gwdescription, $gwvalue, $gwlocked));
		// Initialize for next entry
		$gwparm = '';
		$gwdescription = '';
		$gwvalue = '';
		$gwlocked = 0;
	} else {
		echo $this->ShowErrors($this->Lang('nogwattributesgiven'));
	}
}
$params['gwparm'] = '';

$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_pms_gwattributes WHERE gwgateway_id = ? ORDER BY gwparm';
$dbresult = $db->Execute($query, array($gateway_id));

$rowclass = 'row1';
$entryarray = array();

while ($dbresult && $row = $dbresult->FetchRow()) {

	$onerow = new stdClass();

	$onerow->lock = $row['gwlocked'];

	$onerow->id = $row['gwattr_id'];
	// If the information is locked, only allow editing via the icon (makes it less obvious to change)
	if ($row['gwlocked']) {
		$onerow->parm = $row['gwparm'];
		$onerow->description = $row['gwdescription'];
		$onerow->statuslink = $this->CreateLink(
			$id,
			'switchstatus',
			$returnid,
			$themeObject->DisplayImage('icons/system/true.gif', $this->Lang('setinactive'), '', '', 'systemicon'),
			array('table' => 'gateways', 'active' => false, 'record_id' => $row['gwgateway_id'])
		);
	} else {
		$onerow->parm = $this->CreateLink($id, 'editgatewayattr', $returnid, $row['gwparm'], array('gwgateway_id' => $row['gwgateway_id'], 'gwattr_id' => $row['gwattr_id']));
		$onerow->description = $this->CreateLink($id, 'editgatewayattr', $returnid, $row['gwdescription'], array('gwgateway_id' => $row['gwgateway_id'], 'gwattr_id' => $row['gwattr_id']));
		$onerow->statuslink = $this->CreateLink(
			$id,
			'switchstatus',
			$returnid,
			$themeObject->DisplayImage('icons/system/false.gif', $this->Lang('setactive'), '', '', 'systemicon'),
			array('table' => 'gateways', 'active' => true, 'record_id' => $row['gwgateway_id'])
		);
	}
	$onerow->value = $row['gwvalue'];

	// Show the icons needed for editing, deleting
	$onerow->editlink = $this->CreateLink($id, 'editgatewayattr', $returnid, $themeObject->DisplayImage('icons/system/edit.gif', $this->Lang('edit'), '', '', 'systemicon'), array('gwgateway_id' => $row['gwgateway_id'], 'gwattr_id' => $row['gwattr_id']));
	$onerow->deletelink = $this->CreateLink($id, 'deletegatewayattr', $returnid, $themeObject->DisplayImage('icons/system/delete.gif', $this->Lang('delete'), '', '', 'systemicon'), array('gwgateway_id' => $row['gwgateway_id'], 'gwattr_id' => $row['gwattr_id']), '');

	$onerow->rowclass = $rowclass;

	$entryarray[] = $onerow;

	($rowclass == "row1" ? $rowclass = "row2" : $rowclass = "row1");
}
$this->smarty->assign('items', $entryarray);
$this->smarty->assign('itemcount', count($entryarray));
// Make sure that the attributes are shown once there is at least one
if (count($entryarray) > 0) {
	$this->smarty->assign('gwedit', '1');
}

$gwlocked = isset($gwlocked) ? $gwlocked : 0;
$gwattr_id = isset($gwattr_id) ? $gwattr_id : 0;

// Setup links
$this->smarty->assign('addgwattrlink', $this->CreateLink($id, 'addgatewayattr', $returnid, $this->Lang('addgatewayattr'), array(), '', false, false, 'class="pageoptions"'));
$this->smarty->assign('addgwattrlink', $this->CreateLink($id, 'addgatewayattr', $returnid, $themeObject->DisplayImage('icons/system/newobject.gif', $this->Lang('addgatewayattr'), '', '', 'systemicon'), array(), '', false, false, '') . ' ' . $this->CreateLink($id, 'addgatewayattr', $returnid, $this->Lang('addgatewayattr'), array(), '', false, false, 'class="pageoptions"'));

$this->smarty->assign('gwattrparmtext', $this->Lang('parameter'));
$this->smarty->assign('inputgwattrparm', $this->CreateInputText($id, 'gwparm', '', 30, 30));
$this->smarty->assign('gwattrdesctext', $this->Lang('description'));
$this->smarty->assign('inputgwattrdesc', $this->CreateInputText($id, 'gwdescription', '', 40, 80));
$this->smarty->assign('gwattrvaluetext', $this->Lang('value'));
$this->smarty->assign('inputgwattrvalue', $this->CreateInputText($id, 'gwvalue', '', 40, 80));
$this->smarty->assign('gwattrlocktext', $this->Lang('locked'));
$this->smarty->assign('inputattrlock', $this->CreateInputCheckbox($id, 'gwlock', 1, $gwlocked));
$this->smarty->assign('hiddenattrid', $this->CreateInputHidden($id, 'gwattr_id', $gwattr_id));
$this->smarty->assign('submitattrid', $this->CreateInputSubmit($id, 'submitattrid', $this->Lang('addgatewayattr')));
$this->smarty->assign('noattributesavailable', $this->Lang('noattributesavailable'));

// Now show the template
echo $this->ProcessTemplate('editgateway.tpl');
