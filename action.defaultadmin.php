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

if (!$this->CheckPermission('Use SimplePayment')) {
	return $this->DisplayErrorPage($id, $params, $returnid, $this->Lang('accessdenied'));
}

// Check if a specific tab has been past as the one to show first
if (FALSE == empty($params['active_tab'])) {
	$tab = $params['active_tab'];
} else {
	$tab = 'gateways';
}

echo $this->StartTabHeaders();
echo $this->SetTabHeader('gateways', $this->Lang('gateways'), ('gateways' == $tab) ? true : false);
if ($this->CheckPermission('Modify Templates')) {
	echo $this->SetTabHeader('templates', $this->Lang('templates'), ('templates' == $tab) ? true : false);
}
if ($this->CheckPermission('Modify SimplePayment')) {
	echo $this->SetTabHeader('options', $this->Lang('options'), ('options' == $tab) ? true : false);
}
echo $this->EndTabHeaders();

// The content of the tabs
echo $this->StartTabContent(); {
	// --- Start tab Gateways ---
	echo $this->StartTab('gateways', $params);
	include(dirname(__FILE__) . '/function.admin_gatewaystab.php');

	echo $this->EndTab();

	// --- Start tab Templates ---
	if ($this->CheckPermission('Modify Templates')) {
		echo $this->StartTab('templates', $params);
		include(dirname(__FILE__) . '/function.admin_templatestab.php');

		echo $this->EndTab();
	}

	// --- Start tab Options ---
	if ($this->CheckPermission('Modify SimplePayment')) {
		echo $this->StartTab('options', $params);
		include(dirname(__FILE__) . '/function.admin_optionstab.php');

		echo $this->EndTab();
	}
}
echo $this->EndTabContent();
