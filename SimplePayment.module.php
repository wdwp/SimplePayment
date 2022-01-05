<?php
#-------------------------------------------------------------------------
# Fork of Module: Payment Made Simple - A module for handling payments with CMS - CMS Made Simple
# Copyright (c) 2008 by Duketown <duketown@mantox.nl>
# Forked by Yuri Haperski (wdwp@yandex.ru)
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

class SimplePayment extends CMSModule
{
	function GetName()
	{
		return 'SimplePayment';
	}

	function GetFriendlyName()
	{
		return $this->Lang('friendlyname');
	}

	function GetVersion()
	{
		return '1.0';
	}

	function GetHelp()
	{
		return $this->Lang('help');
	}

	function GetAuthor()
	{
		return 'Duketown';
	}

	function GetAuthorEmail()
	{
		// Due to spam not included
		return '';
	}

	function GetChangeLog()
	{
		return file_get_contents(dirname(__FILE__) . '/changelog.inc');
	}

	function IsPluginModule()
	{
		return true;
	}

	function HasAdmin()
	{
		return true;
	}

	function GetAdminSection()
	{
		global $CMS_VERSION;
		return 'ecommerce';
	}

	function GetAdminDescription()
	{
		return $this->Lang('moddescription');
	}

	function VisibleToAdminUser()
	{
		return $this->CheckPermission('Use SimplePayment');
	}

	function GetDependencies()
	{
		return array('SimpleCart' => '1.0');
	}

	function MinimumCMSVersion()
	{
		return '2.2.0';
	}

	function MaximumCMSVersion()
	{
		return '3.0.0';
	}

	function SetParameters()
	{
	}

	function GetEventDescription($eventname)
	{
		return $this->Lang('event_info_' . $eventname);
	}

	function GetEventHelp($eventname)
	{
		return $this->Lang('event_help_' . $eventname);
	}

	function InstallPostMessage()
	{
		return $this->Lang('postinstall');
	}

	function UninstallPostMessage()
	{
		return $this->Lang('postuninstall');
	}

	function UninstallPreMessage()
	{
		return $this->Lang('really_uninstall');
	}

	function IsPaymentGatewayModule()
	{
		return true;
	}

	function CheckGateways()
	{
		$db = cmsms()->GetDb();
		$gCms = cmsms();
		$gwcodes = array();
		$gwcode = array();
		// Set which part to use as the gateway code
		$gatewaycodepart = $this->GetPreference('gatewaycodepart', '1');
		// Retrieve a list of all the gateways prepared in the directory
		$paymentmsdir = cms_join_path(dirname(__FILE__), 'gw.*');
		$gwcodes = glob($paymentmsdir);
		$i = 0;
		while (isset($gwcodes[$i]) && $gwcodes[$i] != '') {
			// Typical name of gateway program is path\gw.[name].php.
			// First extract the basename
			$path_info = pathinfo($gwcodes[$i]);
			// With explode we retrieve the name portion
			$gwcode = explode(".", $path_info['basename']);

			$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_pms_gateways WHERE UPPER(gateway_code) = UPPER( ? )';
			$row = $db->GetRow($query, array($gwcode[$gatewaycodepart]));
			if (!isset($row['gateway_id'])) {
				// Apparantly a gateway has been found that didn't exist yet in the list of available ones
				$description = $this->Lang('autoinsertedgateway', $gwcode[$gatewaycodepart]);
				$active = 0;
				$gateway_id = $db->GenID(cms_db_prefix() . "module_pms_gateways_seq");
				$query = 'INSERT INTO ' . cms_db_prefix() . 'module_pms_gateways (gateway_id, gateway_code, description, active )
					VALUES (?,?,?,?)';
				$db->Execute($query, array($gateway_id, $gwcode[$gatewaycodepart], $description, $active));

				// Now that we know that new, insert the attributes that belong to the gateway
				include($gwcodes[$i]);
				$newclass = 'pms' . $gwcode[$gatewaycodepart] . '_class';
				$p = new $newclass;

				$p->InstallGateWay($gateway_id);
			}
			$i++;
		}
		return true;
	}
}
