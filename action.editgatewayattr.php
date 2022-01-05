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

if (!$this->CheckPermission('Modify SimplePayment')) {
	echo $this->ShowErrors($this->Lang('accessdenied', array('Modify SimplePayment')));
	return;
}

if (isset($params['cancel'])) {
	$params = array('tab_message' => $this->Lang('gatewayattrnotupdated'), 'gwgateway_id' => $params['gwgateway_id']);
	$this->Redirect($id, 'editgateway', $returnid, $params);
}

$gwattr_id = '';
if (isset($params['gwattr_id'])) {
	$gwattr_id = $params['gwattr_id'];
}
if (isset($params['gwgateway_id'])) {
	$gwgateway_id = $params['gwgateway_id'];
}
$active = 0;
if (isset($params['gwlocked'])) {
	$gwlocked = $params['gwlocked'];
}

if (isset($params['gwattr_id']) and isset($params['submit'])) {
	$gwparm = '';
	if (isset($params['gwparm'])) {
		$gwparm = trim($params['gwparm']);
	}
	$gwdescription = '';
	if (isset($params['gwdescription'])) {
		$gwdescription = trim($params['gwdescription']);
	}
	$gwvalue = '';
	if (isset($params['gwvalue'])) {
		$gwvalue = trim($params['gwvalue']);
	}
	if ($gwparm != '' and $gwdescription != '' and $gwvalue != '') {
		$query = 'UPDATE ' . cms_db_prefix() . 'module_pms_gwattributes SET gwparm = ?, gwdescription = ?, gwvalue = ?, gwlocked = ?
			WHERE gwattr_id = ?';
		$db->Execute($query, array($gwparm, $gwdescription, $gwvalue, $gwlocked, $gwattr_id));

		$params = array('tab_message' => $this->Lang('gatewayattrupdated'), 'gwgateway_id' => $gwgateway_id);
		$this->Redirect($id, 'editgateway', $returnid, $params);
	} else {
		echo $this->ShowErrors($this->Lang('nogwattributesgiven'));
	}
} else {

	$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_pms_gwattributes,  ' . cms_db_prefix() . 'module_pms_gateways
		WHERE gwgateway_id = gateway_id and gwattr_id = ?';
	$row = $db->GetRow($query, array($gwattr_id));

	if ($row) {
		$gwparm = $row['gwparm'];
		$gwdescription = $row['gwdescription'];
		$gwvalue = $row['gwvalue'];
		$gwlocked = $row['gwlocked'];
		$gateway_code = $row['gateway_code'];
	}
}

$this->smarty->assign('gatewaycodetext', $this->Lang('gatewaycode'));

// Display template
$this->smarty->assign('startform', $this->CreateFormStart($id, 'editgatewayattr', $returnid));
$this->smarty->assign('endform', $this->CreateFormEnd());
$this->smarty->assign('gwcodetext', $this->Lang('gatewaycode'));
$this->smarty->assign('inputgwcode', $gateway_code);
$this->smarty->assign('gwattrparmtext', $this->Lang('parameter'));
$this->smarty->assign('inputgwattrparm', $this->CreateInputText($id, 'gwparm', $gwparm, 30, 30, 'class="defaultfocus"'));
$this->smarty->assign('gwattrdesctext', $this->Lang('description'));
$this->smarty->assign('inputgwattrdesc', $this->CreateInputText($id, 'gwdescription', $gwdescription, 80, 80));
$this->smarty->assign('gwattrvaluetext', $this->Lang('value'));
$this->smarty->assign('inputgwattrvalue', $this->CreateInputText($id, 'gwvalue', $gwvalue, 80, 80));
$this->smarty->assign('gwattrlocktext', $this->Lang('locked'));
$this->smarty->assign('inputattrlock', $this->CreateInputCheckbox($id, 'gwlocked', 1, $gwlocked));
$this->smarty->assign('hiddenattrid', $this->CreateInputHidden($id, 'gwattr_id', $gwattr_id));
$this->smarty->assign('hidden', $this->CreateInputHidden($id, 'gwgateway_id', $gwgateway_id));
$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));

// Now show the template
echo $this->ProcessTemplate('editgatewayattr.tpl');
