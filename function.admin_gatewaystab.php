<?php
#-------------------------------------------------------------------------
# Module: Payment Made Simple - A module for handling payments with CMS - CMS Made Simple
# Copyright (c) 2009 by Duketown
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
# The module's homepage is: http://dev.cmsmadesimple.org/projects/pms
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

$gCms = cmsms(); if( !is_object($gCms) ) exit;

$config = $gCms->config;
$themeObject = \cms_utils::get_theme_object();
$curgateway = (isset($params['curgateway']) ? $params['curgateway'] : '');
$newgateway = $curgateway;

if (isset($params['submitgateway'])) {
	$newgateway = (isset($params['newgateway']) ? $params['newgateway'] : $newgateway);
}

// Check if there are maybe new gateways added once the refresh link is used
if (isset($params['refreshgateway'])) {
	$gwchecked = $this->CheckGateways();
}

$curgateway = $newgateway;
$listgateway = array();
$query = 'SELECT * FROM '.cms_db_prefix().'module_pms_gateways ORDER BY gateway_code';
$dbresult = $db->Execute($query);

$rowclass = 'row1';
$entryarray = array();

while ($dbresult && $row = $dbresult->FetchRow())
{
	$onerow = new stdClass();

	$onerow->id = $row['gateway_id'];
	$onerow->code = $this->CreateLink($id, 'editgateway', $returnid, $row['gateway_code'], array('gateway_id'=>$row['gateway_id']));
	$onerow->description = $this->CreateLink($id, 'editgateway', $returnid, $row['description'], array('gateway_id'=>$row['gateway_id']));
	if ( $row['active'] )
		{
			$onerow->statuslink = $this->CreateLink($id, 'switchstatus', $returnid,
				$themeObject->DisplayImage('icons/system/true.gif',$this->Lang('setinactive'),
				'','','systemicon'),array('table'=>'gateways','active'=>false,'record_id'=>$row['gateway_id']));
		}
	else
		{
			$onerow->statuslink = $this->CreateLink($id,'switchstatus', $returnid,
				$themeObject->DisplayImage('icons/system/false.gif',$this->Lang('setactive')
				,'','','systemicon'),array('table'=>'gateways','active'=>true,'record_id'=>$row['gateway_id']));
		}

	// Show the icons needed for editing, deleting
	$onerow->editlink = $this->CreateLink($id, 'editgateway', $returnid,
		$themeObject->DisplayImage('icons/system/edit.gif', $this->Lang('edit'),'','','systemicon'),array('gateway_id'=>$row['gateway_id']));
	$onerow->deletelink = $this->CreateLink($id, 'deletegateway', $returnid, $themeObject->DisplayImage('icons/system/delete.gif', $this->Lang('delete'),'','','systemicon'), array('gateway_id'=>$row['gateway_id']), $this->Lang('areyousuregateway',$row['gateway_code']));

	$onerow->rowclass = $rowclass;

	$entryarray[] = $onerow;

	($rowclass=="row1"?$rowclass="row2":$rowclass="row1");
}
$smarty->assign_by_ref('items', $entryarray);
$smarty->assign('itemcount', count($entryarray));

// Setup links
$smarty->assign('addgatewaylink', $this->CreateLink($id, 'addgateway', $returnid,
	$this->Lang('addgateway'), array(), '', false, false, 'class="pageoptions"'));
$smarty->assign('addgatewaylink', $this->CreateLink($id, 'addgateway', $returnid,
	$themeObject->DisplayImage('icons/system/newobject.gif', $this->Lang('addgateway'),'','','systemicon'), array(), '', false, false, '') .' '. $this->CreateLink($id, 'addgateway', $returnid, $this->Lang('addgateway'), array(), '', false, false, 'class="pageoptions"'));

$smarty->assign('gatewaycodetext', $this->Lang('gatewaycode'));
$smarty->assign('gatewaydesctext', $this->Lang('description'));
$smarty->assign('statustext', $this->Lang('status'));
$smarty->assign('refreshgateway', $this->CreateLink($id, 'defaultadmin', $returnid,
	$this->Lang('refreshgateway'), array('refreshgateway'=>true), '', false, false, 'class="pageoptions"'));

// Display the populated template
echo $this->ProcessTemplate('listgateways.tpl');
