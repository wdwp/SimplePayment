<?php

// No direct access
$gCms = cmsms();
if (!is_object($gCms)) exit;

$params['active'] = empty($params['active']) ? 0 : $params['active'];

// Check permission
if (!$this->CheckPermission('Modify SimplePayment')) {
	// Show an error message
	echo $this->ShowError($this->Lang('access_denied'));
}
// User has sufficient privileges
else {
	switch ($params['table']) {
		case 'gateways':
			$query = 'UPDATE ' . cms_db_prefix() . 'module_pms_gateways SET active = ? WHERE gateway_id = ?';
			$db->Execute($query, array($params['active'], $params['record_id']));
			$params = array('active_tab' => 'Gateways');
			break;
		default:
			break;
	}

	// Redirect the user to the default admin screen
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}
