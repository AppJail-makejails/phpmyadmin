<?php

require __DIR__ . '/config.secret.inc.php';

$containerdir = '/pma';
$pma_queryhistorydb = $containerdir . '/QueryHistoryDB';
$pma_queryhistorymax = $containerdir . '/QueryHistoryMax';
$pma_allowarbitraryserver = $containerdir . '/AllowArbitraryServer';
$pma_absoluteuri = $containerdir . '/PmaAbsoluteUri';
$pma_host = $containerdir . '/host';
$pma_hosts = $containerdir . '/hosts';
$pma_verbose = $containerdir . '/verbose';
$pma_verboses = $containerdir . '/verboses';
$pma_port = $containerdir . '/port';
$pma_ports = $containerdir . '/ports';
$pma_socket = $containerdir . '/socket';
$pma_sockets = $containerdir . '/sockets';
$pma_user = $containerdir . '/user';
$pma_pmadb = $containerdir . '/pmadb';
$pma_password = $containerdir . '/password';
$pma_controlhost = $containerdir . '/controlhost';
$pma_controlport = $containerdir . '/controlport';
$pma_controluser = $containerdir . '/controluser';
$pma_controlpass = $containerdir . '/controlpass';
$pma_uploaddir = $containerdir . '/UploadDir';
$pma_savedir = $containerdir . '/SaveDir';
$pma_exectimelimit = $containerdir . '/ExecTimeLimit';
$pma_memorylimit = $containerdir . '/MemoryLimit';

if (is_file($pma_queryhistorydb)) {
	$cfg['QueryHistoryDB'] = true;
} else {
	$cfg['QueryHistoryDB'] = false;
}

if (is_file($pma_queryhistoryMax)) {
	$cfg['QueryHistoryMax'] = (int) file_get_contents($pma_queryhistorymax);
}

if (is_file($pma_allowarbitraryserver)) {
	$cfg['AllowArbitraryServer'] = true;
}

if (is_file($pma_absoluteuri)) {
	$cfg['PmaAbsoluteUri'] = trim(file_get_contents($pma_absoluteuri));
}

$hosts = [];
$verboses = [];
$ports = [];

if (is_file($pma_host)) {
	$hosts = [file_get_contents($pma_host)];
	
	if (is_file($pma_verbose)) {
		$verboses = [file_get_contents($pma_verbose)];
	}
	
	if (is_file($pma_port)) {
		$ports = [file_get_contents($pma_port)];
	}
} elseif (is_file($pma_hosts)) {
	$hosts = array_map('trim', explode(',', file_get_contents($pma_hosts)));
	
	if (is_file($pma_verboses)) {
		$verboses = array_map('trim', explode(',', file_get_contents($pma_verboses)));
	}
	
	if (is_file($pma_ports)) {
		$ports = array_map('trim', explode(',', file_get_contents($pma_ports)));
	}
}

$sockets = [];

if (is_file($pma_socket)) {
	$sockets = [file_get_contents($pma_socket)];
} elseif (is_file($pma_sockets)) {
	$sockets = explode(',', file_get_contents($pma_sockets));
}

for ($i = 1; isset($hosts[$i - 1]); $i++) {
	$cfg['Servers'][$i]['host'] = $hosts[$i - 1];
	
	if (isset($verboses[$i - 1])) {
		$cfg['Servers'][$i]['verbose'] = $verboses[$i - 1];
	}
	
	if (isset($ports[$i - 1])) {
		$cfg['Servers'][$i]['port'] = $ports[$i - 1];
	}
	
	if (is_file($pma_user)) {
		$cfg['Servers'][$i]['auth_type'] = 'config';
		$cfg['Servers'][$i]['user'] = file_get_contents($pma_user);
		$cfg['Servers'][$i]['password'] = is_file($pma_password) ? file_get_contents($pma_password) : '';
	} else {
		$cfg['Servers'][$i]['auth_type'] = 'cookie';
	}
	
	if (is_file($pma_pmadb)) {
		$cfg['Servers'][$i]['pmadb'] = file_get_contents($pma_pmadb);
		$cfg['Servers'][$i]['relation'] = 'pma__relation';
		$cfg['Servers'][$i]['table_info'] = 'pma__table_info';
		$cfg['Servers'][$i]['table_coords'] = 'pma__table_coords';
		$cfg['Servers'][$i]['pdf_pages'] = 'pma__pdf_pages';
		$cfg['Servers'][$i]['column_info'] = 'pma__column_info';
		$cfg['Servers'][$i]['bookmarktable'] = 'pma__bookmark';
		$cfg['Servers'][$i]['history'] = 'pma__history';
		$cfg['Servers'][$i]['recent'] = 'pma__recent';
		$cfg['Servers'][$i]['favorite'] = 'pma__favorite';
		$cfg['Servers'][$i]['table_uiprefs'] = 'pma__table_uiprefs';
		$cfg['Servers'][$i]['tracking'] = 'pma__tracking';
		$cfg['Servers'][$i]['userconfig'] = 'pma__userconfig';
		$cfg['Servers'][$i]['users'] = 'pma__users';
		$cfg['Servers'][$i]['usergroups'] = 'pma__usergroups';
		$cfg['Servers'][$i]['navigationhiding'] = 'pma__navigationhiding';
		$cfg['Servers'][$i]['savedsearches'] = 'pma__savedsearches';
		$cfg['Servers'][$i]['central_columns'] = 'pma__central_columns';
		$cfg['Servers'][$i]['designer_settings'] = 'pma__designer_settings';
		$cfg['Servers'][$i]['export_templates'] = 'pma__export_templates';
	}
	
	if (is_file($pma_controlhost)) {
		$cfg['Servers'][$i]['controlhost'] = file_get_contents($pma_controlhost);
	}
	
	if (is_file($pma_controlport)) {
		$cfg['Servers'][$i]['controlport'] = file_get_contents($pma_controlport);
	}
	
	if (is_file($pma_controluser)) {
		$cfg['Servers'][$i]['controluser'] = file_get_contents($pma_controluser);
	}
	
	if (is_file($pma_controlpass)) {
		$cfg['Servers'][$i]['controlpass'] = file_get_contents($pma_controlpass);
	}
	
	$cfg['Servers'][$i]['compress'] = false;
	$cfg['Servers'][$i]['AllowNoPassword'] = true;
}

for ($i = 1; isset($sockets[$i - 1]); $i++) {
	$cfg['Servers'][$i]['socket'] = $sockets[$i - 1];
	$cfg['Servers'][$i]['host'] = 'localhost';
}

/*
 * Revert back to last configured server to make
 * it easier in config.user.inc.php
 */
$i--;

if (is_file($pma_uploaddir)) {
	$cfg['UploadDir'] = file_get_contents($pma_uploaddir);
}

if (is_file($pma_savedir)) {
	$cfg['SaveDir'] = file_get_contents($pma_savedir);
}

if (is_file($pma_exectimelimit)) {
	$cfg['ExecTimeLimit'] = file_get_contents($pma_exectimelimit);
}

if (is_file($pma_memorylimit)) {
	$cfg['MemoryLimit'] = file_get_contents($pma_memorylimit);
}

if (is_file(__DIR__ . '/config.user.inc.php')) {
	include __DIR__ . '/config.user.inc.php';
}

if (is_dir(__DIR__ . '/conf.d/')) {
	foreach (glob(__DIR__ . '/conf.d/*.php') as $filename) {
		include $filename;
	}
}
