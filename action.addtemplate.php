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


$gCms = cmsms();
if (!is_object($gCms)) exit;

if (!$this->CheckPermission('Modify SimplePayment')) {
	echo $this->ShowErrors($this->Lang('accessdenied', array('Modify SimplePayment')));
	return;
}

if (isset($params['cancel'])) {
	$params = array('active_tab' => 'templates');
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}

// Validate the input
$message = '';
if (isset($params['submit'])) {
	$templatename = $params['templatename'];
	$template = $params['template'];
	if ($templatename != '' && $template != '') {
		$this->SetTemplate($templatename, $template, $this->GetName());

		$params = array('tab_message' => $this->Lang('templateadded'), 'active_tab' => 'templates');
		$this->Redirect($id, 'defaultadmin', $returnid, $params);
	} else {
		if ($templatename == '') $message .= $this->Lang('notemplatenamegiven');
		if ($template == '') {
			if ($message != '') $message .= '<br>';
			$message .= $this->Lang('notemplatecontentgiven');
		}
		echo $this->ShowErrors($message);
	}
}

$templatename = isset($templatename) ? $templatename : '';
$template = isset($template) ? $template : '';

// Display template
$smarty->assign('startform', $this->CreateFormStart($id, 'addtemplate', $returnid));
$smarty->assign('endform', $this->CreateFormEnd());
$smarty->assign('prompt_templatename', $this->Lang('templatename'));
$smarty->assign('templatename', $this->CreateInputText($id, 'templatename', $templatename, 40, 40, 'class="defaultfocus"'));
$smarty->assign('prompt_template', $this->Lang('templatecontent'));
$smarty->assign('template', $this->CreateTextArea(false, $id, $template, 'template', '', '', '', '', 80, 25));
$smarty->assign('hidden', '');
$smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
$smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));
echo $this->ProcessTemplate('edittemplate.tpl');
