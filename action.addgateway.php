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

if (!$this->CheckPermission('Modify SimplePayment')) {
	echo $this->ShowErrors($this->Lang('accessdenied', array('Modify SimplePayment')));
	return;
}

if (isset($params['description'])) {
	$description = trim($params['description']);
}

// When adding a gateway, it is not set to active as default, since the admin has really think about it
if (isset($params['active'])) {
	$active = $params['active'];
}

if (isset($params['cancel'])) {
	$params = array('active_tab' => 'gateways');
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}

$gateway_code = '';
if (isset($params['gateway_code'])) {
	$gateway_code = trim($params['gateway_code']);
	if ($gateway_code != '') {
		$gateway_id = $db->GenID(cms_db_prefix() . "module_pms_gateways_seq");
		$query = 'INSERT INTO ' . cms_db_prefix() . 'module_pms_gateways (gateway_id, gateway_code, description, active )
			VALUES (?,?,?,?)';
		$db->Execute($query, array($gateway_id, $gateway_code, $description, $active));

		$params = array('tab_message' => $this->Lang('gatewayadded'), 'active_tab' => 'gateways');
		$this->Redirect($id, 'defaultadmin', $returnid, $params);
	} else {
		echo $this->ShowErrors($this->Lang('nogatewaycodegiven'));
	}
}

$desc = isset($desc) ? $desc : '';

// Display template
$smarty->assign('startform', $this->CreateFormStart($id, 'addgateway', $returnid));
$smarty->assign('endform', $this->CreateFormEnd());
$smarty->assign('gwcodetext', $this->Lang('gatewaycode'));
$smarty->assign('inputgwcode', $this->CreateInputText($id, 'gateway_code', $gateway_code, 10, 10, 'class="defaultfocus"'));
$smarty->assign('gwdesctext', $this->Lang('gatewaydescription'));
$smarty->assign('inputgwdesc', $this->CreateInputText($id, 'description', $desc, 40, 40));
$smarty->assign('activetext', $this->Lang('active'));
$smarty->assign('inputactive', $this->CreateInputCheckbox($id, 'active', 1));
$smarty->assign('hidden', '');
$smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
$smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));
echo $this->ProcessTemplate('editgateway.tpl');
