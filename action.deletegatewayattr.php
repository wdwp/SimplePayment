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
	echo $this->ShowErrors($this->Lang('needpermission', array('Modify SimplePayment')));
	return;
}

$gwgateway_id = '';
if (isset($params['gwgateway_id'])) {
	$gwgateway_id = $params['gwgateway_id'];
}
$gwattr_id = '';
if (isset($params['gwattr_id'])) {
	$gwattr_id = $params['gwattr_id'];
}

// Remove the gateway attribure (might have to check if all payments have been received first)
$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_pms_gwattributes WHERE gwattr_id = ?';
$row = $db->GetRow($query, array($gwattr_id));
if ($row) {
	$query = 'DELETE FROM ' . cms_db_prefix() . 'module_pms_gwattributes WHERE gwattr_id = ?';
	$db->Execute($query, array($gwattr_id));
}

$params = array('tab_message' => $this->Lang('gatewayattrdeleted'), 'gwgateway_id' => $gwgateway_id);
$this->Redirect($id, 'editgateway', $returnid, $params);
