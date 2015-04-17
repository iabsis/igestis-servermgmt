<?
//Reports functions

$xajax->registerFunction("ishare_get_tape_status");
$xajax->registerFunction("ishare_tape_mount");
$xajax->registerFunction("ishare_tape_umount");
$xajax->registerFunction("ishare_restore_mysql_db");
$xajax->registerFunction("ishare_restore_file");
$xajax->registerFunction("ishare_save_want_start");

$xajax->registerFunction("ishare_stop_service");
$xajax->registerFunction("ishare_restart_service");
$xajax->registerFunction("ishare_check_updates");
$xajax->registerFunction("ishare_install_updates");

$xajax->registerFunction("ishare_printer_delete");

$xajax->registerFunction("ishare_start_remote_control");
$xajax->registerFunction("ishare_update_remote_control_status");

$xajax->registerFunction("ishare_update_additionnal_modules_list");
$xajax->registerFunction("ishare_install_module");
$xajax->registerFunction("ishare_uninstall_module");

$xajax->registerFunction("ishare_root_warning");

function ishare_tape_date_list() {
	$dates = NULL;
	
	exec("sudo ishare tape_mounted", $out, $return);
	if($return == 1) {
		// La casette est montee, on recherche la liste des dates ...
		if(is_file(ISHARE_TAPE_FOLDER . "/lastest/date"))  $dates[] = array("lastest",  trim(file_get_contents(ISHARE_TAPE_FOLDER . "/lastest/date")));
		if(is_file(ISHARE_TAPE_FOLDER . "/backup-1/date")) $dates[] = array("backup-1", trim(file_get_contents(ISHARE_TAPE_FOLDER . "/backup-1/date")));
		if(is_file(ISHARE_TAPE_FOLDER . "/backup-2/date")) $dates[] = array("backup-2", trim(file_get_contents(ISHARE_TAPE_FOLDER . "/backup-2/date")));
	}

	return $dates ;
}


function show_services_status(&$application) {
	exec("sudo ishare service apache status", $output, $return);
	$application->add_var("APACHE2_STARTED", $return);
	
	exec("sudo ishare service cups status", $output, $return);
	$application->add_var("CUPS_STARTED", $return);
	
	exec("sudo ishare service dovecot status", $output, $return);
	$application->add_var("DOVECOT_STARTED", $return);
	
	exec("sudo ishare service mysql status", $output, $return);
	$application->add_var("MYSQL_STARTED", $return);
	
	exec("sudo ishare service postfix status", $output, $return);
	$application->add_var("POSTFIX_STARTED", $return);
	
	exec("sudo ishare service samba status", $output, $return);
	$application->add_var("SAMBA_STARTED", $return);
	
	exec("sudo ishare service ldap status", $output, $return);
	$application->add_var("SLAPD_STARTED", $return);
}

function show_printers_status(&$application) {
	exec("ishare printer-list", $output, $return);
	if(is_array($output)) {
		$ID = 1;
		foreach($output as $printer) {
			preg_match("/printer ([\W\w]+) (is idle|disabled)/", $printer, $matches);			
			if($matches) {
				if($matches[2] == "is idle") $matches[2] = "{LANG_ISHARE_Printer_is_idle}";
				if($matches[2] == "disabled") $matches[2] = "{LANG_ISHARE_Printer_is_unable}";
				$application->add_block("PRINTERS_LIST", array(
					"ID" => $ID,
					"CLASS" => ($ID++ ? "ligne1" : "ligne2"),
					"STATUS" => $matches[2],
					"NAME" => $matches[1]
				));
			}
			
		}
	}
}

function ishare_update_modules_list($module_just_installed = false) {
	global $application;

        $language = strtolower($application->userprefs['language']);
        if(!$language) $language = "fr";
        $language = $language . "_" . strtoupper($language) . ".utf8";

	$return = NULL;
	$output = NULL;
	exec("sudo ishare check-update " . escapeshellarg($language));
	exec("LANG=\"" . $language . "\" apt-cache search igestis-", $output, $return);
	
	// Pour chaque module on vérifie s'il est deja installé et on recupère la description
	$cpt = 0;
	if(is_array($output)) {
		foreach($output as $module) {
			preg_match("/^[A-Za-z0-9\_\-]+/", $module, $result);
			if(!$result[0]) continue;
			$module_name = $result[0];
			
			$return = NULL;
			$output = NULL;
			exec("LANG=\"" . $language . "\" apt-cache show " . $module_name, $output, $return);
                                            
			$description = implode("<br />",$output);
			
			$return = NULL;
			$output = NULL;
			
			exec("dpkg -l " . $module_name . " | grep " . $module_name. " | grep ^ii" , $output, $return);
			if(!$output) $installed = false; else $installed = true;
			
			// On ne garde que la description
                        preg_match("/Description-" . strtolower($application->userprefs['language']) . ":([\W\w]+)/", $description, $matches);
			if(!$matches[1]) {
                            $matches = NULL;
                            preg_match("/Description:([\W\w]+)/", $description, $matches);
                        }
			$description = $matches[1];
			$description = preg_replace("/Package:[\W\w]+/", "", $description);

			if($module_name != "igestis-core" && $module_name != "igestis-file-manager" && $module_name != "igestis-ishare" && $module_name != "igestis-ldap" && $module_name != "igestis-roundcube" && $module_name != "igestis-file-manager-ldap") {
				$your_version = $last_version = NULL;
				exec('dpkg -l | grep ' . $module_name . ' | grep ii | awk \'{print $3}\'' , $your_version);	
				//exec('LANG=\"' . $language . '\" apt-cache show ' . $module_name . ' | grep "Version:" | awk \'{print $2}\'' , $last_version);
                                                                exec('LANG=\"' . $language . '\" apt-get install ' . $module_name . '  -s | grep "Inst"  | sed "s/Inst.*(//g" | cut -f1 -d" "', $last_version);
				
				$upgrade_available = false;
				if(trim($last_version[0])!= "" && $installed) $upgrade_available = true;
				$application->add_block("ADDITIONAL_MODULES_LIST", array(
					"CLASS" => ($cpt++%2 ? "ligne1" : "ligne2"),
					"MODULE" => $module_name,
					"DESCRIPTION" => $description,
					"INSTALLED" => $installed,
					"YOUR_VERSION" => $your_version[0],
					"LAST_VERSION" => ($last_version[0] ? $last_version[0] : $your_version[0]),
					"UPGRADE_AVAILABLE" => $upgrade_available,
					"module_just_installed" => ($module_name == $module_just_installed)
				));
			}
		}
	}
}



?>
