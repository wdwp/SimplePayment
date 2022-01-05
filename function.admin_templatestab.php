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

$templates = array();
$templates2 = array();
$dbtemplates = $this->ListTemplates();

$rowclass = 'row1';
// Prepare a list of the already available templates
foreach ($dbtemplates as $template)
{
	$onerow = new stdClass();
	$onerow->rowclass = $rowclass;
	$onerow->name = $this->CreateLink($id, 'edittemplate', $returnid, $template, array('templatename'=>$template));
	$onerow->editlink = $this->CreateLink($id, 'edittemplate', $returnid, $themeObject->DisplayImage('icons/system/edit.gif', $this->Lang('edit'),'','','systemicon'), array('templatename' => $template));
	// Any automatically loaded gateway templates may not be deleted (since they are used in the gateway itself)
	if (substr($template, 0, 11) != 'pms_gateway') {
		$onerow->deletelink = $this->CreateLink($id, 'deletetemplate', $returnid,
			$themeObject->DisplayImage('icons/system/delete.gif', $this->Lang('delete'),'','','systemicon'),
			array('templatename' => $template), $this->Lang('areyousure'));
	} else {
		$onerow->deletelink = '&nbsp;';
	}

	$templates2[$template]=$template;
	$templates[] = $onerow;

	($rowclass=="row1"?$rowclass="row2":$rowclass="row1");
}
$smarty->assign_by_ref('items', $templates);

$smarty->assign('addlink', $this->CreateLink($id, 'addtemplate', $returnid,
	$themeObject->DisplayImage('icons/system/newobject.gif', $this->Lang('addtemplate')
	,'','','systemicon'), array(), '', false, false, '') .' '.
	$this->CreateLink($id, 'addtemplate', $returnid, $this->Lang('addtemplate'), array(),
	'', false, false, 'class="pageoptions"'));

$smarty->assign('formstart', $this->CreateFormStart($id, 'changetemplate', $returnid));
$smarty->assign('title_templatename', $this->Lang('title_templatename'));
$smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('submit')));
$smarty->assign('formend', $this->CreateFormEnd());

// Display the template
echo $this->ProcessTemplate('listtemplates.tpl');
