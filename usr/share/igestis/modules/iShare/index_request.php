<?
if(!defined('GENERAL_INDEX_REQUEST_LAUNCHED')) die("Hacking attempt");

function ishare_get_tape_status () {
	$objResponse = new xajaxResponse();
	
	global $application;
	$ishare_access = $application->module_access("ishare");
	if($ishare_access != "ADMIN")
	{// FOr the employee, just access for the admins and techs users
		return $objResponse;
	}	

	
	exec("sudo ishare tape_mounted", $out, $return);
	if($return == 1) {
		$objResponse->AddAssign("mount_button", "innerHTML", "<span id=\"mount_link\"><a class=\"btn btn-danger\" href=\"#\" onclick=\"javascript:document.getElementById('mount_link').innerHTML='" . $application->LANG['LANG_ROUNDCUBE_Mount_Loading'] . "';xajax_ishare_tape_umount()\">" . $application->LANG['LANG_ROUNDCUBE_Umount_tape'] . "</a></span>");	
		$dates = ishare_tape_date_list();
		if(is_array($dates)) {
			foreach($dates as $date) {
				$param .= ",'" . $date[1] . "'";
				$param2 .= ",'" . $date[0] . "'";
			}
			$objResponse->AddScript("ishare_add_date_list(Array(" . substr($param, 1) . "), Array(" . substr($param2, 1) . "))");
		}
	}
	elseif($return == 0) {
		$objResponse->AddAssign("mount_button", "innerHTML", "<span id=\"mount_link\"><a class=\"btn btn-success\" href=\"#\" onclick=\"javascript:document.getElementById('mount_link').innerHTML='" . $application->LANG['LANG_ROUNDCUBE_Mount_Loading'] . "';xajax_ishare_tape_mount()\">" . $application->LANG['LANG_ROUNDCUBE_Mount_tape'] . "</a></span>");
		$objResponse->AddScript("document.getElementById('date_list_fieldset').style.display = 'none')");
	}
	else {
		$objResponse->AddAssign("mount_button", "innerHTML", "Pas de lecteur de sauvegarde");
	}
	return $objResponse;
}

function ishare_tape_mount () { 
	$objResponse = new xajaxResponse();
	
	global $application;
	$ishare_access = $application->module_access("ishare");
	if($ishare_access != "ADMIN")
	{// FOr the employee, just access for the admins and techs users
		return $objResponse;
	}	
	
	exec("sudo ishare tape_mount");
	exec("sudo ishare tape_mounted", $out, $return);
	if($return == 1) {
		$objResponse->AddAssign("mount_button", "innerHTML", "<span id=\"mount_link\"><a href=\"#\" onclick=\"javascript:document.getElementById('mount_link').innerHTML='" . $application->LANG['LANG_ROUNDCUBE_Mount_Loading'] . "';xajax_ishare_tape_umount()\">" . $application->LANG['LANG_ROUNDCUBE_Umount_tape'] . "</a></span>");	
		$dates = ishare_tape_date_list();
		if(is_array($dates)) {
			foreach($dates as $date) {
				$param .= ",'" . $date[1] . "'";
				$param2 .= ",'" . $date[0] . "'";
			}
			$objResponse->AddScript("ishare_add_date_list(Array(" . substr($param, 1) . "), Array(" . substr($param2, 1) . "))");
		}
	}
	elseif($return == 0) {
		$objResponse->AddAssign("mount_button", "innerHTML", "<span id=\"mount_link\"><a href=\"#\" onclick=\"javascript:document.getElementById('mount_link').innerHTML='" . $application->LANG['LANG_ROUNDCUBE_Mount_Loading'] . "';xajax_ishare_tape_mount()\">" . $application->LANG['LANG_ROUNDCUBE_Mount_tape'] . "</a></span>");
		$objResponse->AddScript("document.getElementById('date_list_fieldset').style.display = 'none')");
	}
	else {
		$objResponse->AddAssign("mount_button", "innerHTML", "Pas de lecteur de sauvegarde");
	}
	
	return $objResponse;
}

function ishare_tape_umount () { 
	$objResponse = new xajaxResponse();
	
	global $application;
	$ishare_access = $application->module_access("ishare");
	if($ishare_access != "ADMIN")
	{// FOr the employee, just access for the admins and techs users
		return $objResponse;
	}
	
	exec("sudo ishare tape_umount");
	exec("sudo ishare tape_mounted", $out, $return);
	if($return == 1) $objResponse->AddAssign("mount_button", "innerHTML", "<span id=\"mount_link\"><a href=\"#\" onclick=\"javascript:document.getElementById('mount_link').innerHTML='" . $application->LANG['LANG_ROUNDCUBE_Mount_Loading'] . "';xajax_ishare_tape_umount()\">" . $application->LANG['LANG_ROUNDCUBE_Umount_tape'] . "</a></span>");	
	elseif($return == 0) {
		/*$objResponse->AddAssign("mount_button", "innerHTML", "<input type=\"button\" name=\"mount\" value=\"Mount tape\" onClick=\"javascript:this.disabled=true;this.value='Loading ...';xajax_ishare_tape_mount()\" />");
		$objResponse->AddScript("document.getElementById('date_list_fieldset').style.display = 'none';");*/
		$objResponse->AddScript("window.location.reload(true)");
	}
	else {
		$objResponse->AddAssign("mount_button", "innerHTML", "Pas de lecteur de sauvegarde");
	}
	return $objResponse;
}

function ishare_restore_mysql_db($db_name, $date) {
	$objResponse = new xajaxResponse();
	
	global $application;
	$ishare_access = $application->module_access("ishare");
	if($ishare_access != "ADMIN")
	{// FOr the employee, just access for the admins and techs users
		return $objResponse;
	}	
	
	if(!is_file(ISHARE_TAPE_FOLDER . "/" . $date . "/mysql/" . $db_name . ".sql")) {
		$objResponse->AddScript("alert('Unable to find the sql file')");
		return $objResponse;
	}
	
	exec("sudo ishare mysql_auth", $out);
	$auth = explode("::", $out[0]);
	$login = trim($auth[0]);
	$password = trim($auth[1]);
	
	exec("mysql -u" . escapeshellcmd($login) . " -p" . escapeshellcmd($password) . " " . escapeshellarg($db_name) . " < " . escapeshellarg(ISHARE_TAPE_FOLDER . "/" . $date . "/mysql/" . $db_name . ".sql"), $result, $return);
	return $objResponse;
}

function ishare_restore_file($when, $folder) {	
	$objResponse = new xajaxResponse();
	
	global $application;
	$ishare_access = $application->module_access("ishare");
	if($ishare_access != "ADMIN")
	{// FOr the employee, just access for the admins and techs users
		return $objResponse;
	}	
	
	if(!trim($when)) {
		$objResponse->AddScript("alert('Merci de sélectionner une date')");
		return $objResponse;
	}
	
	if(!trim($folder)) {
		$objResponse->AddScript("alert('Merci de sélectionner un dossier')");
		return $objResponse;
	}
	exec("sudo ishare restore " . escapeshellarg($when) . ' ' .  escapeshellarg($folder));
	//echo ("sudo ishare restore " . escapeshellarg($when) . ' ' .  escapeshellarg($folder));
	//die("ishare restore " . escapeshellcmd($when) . ' "' . str_replace('"', '\\"', $folder) . '"');
	$objResponse->AddScript("messageObj.close()");
	return $objResponse;
}

function ishare_save_want_start() {	
	$objResponse = new xajaxResponse();
	
	global $application;
	$ishare_access = $application->module_access("ishare");
	if($ishare_access != "ADMIN")
	{// FOr the employee, just access for the admins and techs users
		return $objResponse;
	}	
	
	exec("sudo ishare save_launched", $output, $return);	
	if($return == 2) {
		$objResponse->AddScript(
			"show_warning_message('<img src=\"" . $application->get_template_url() . "/images/warning.png\" /><img src=\"" . $application->get_template_url() . "/images/warning.png\" /><img src=\"" . $application->get_template_url() . "/images/warning.png\" /> " .
			"Le script de sauvegarde est lancé. Merci de <a href=\"#\" onclick=\"javascript:xajax_ishare_tape_umount();\">démonter la cassette</a>. (ne pas éjecter) " .
			"<img src=\"" . $application->get_template_url() . "/images/warning.png\" /><img src=\"" . $application->get_template_url() . "/images/warning.png\" /><img src=\"" . $application->get_template_url() . "/images/warning.png\" />', 'info')");	
	}	
	return $objResponse;
}

function ishare_root_warning() {	
	$objResponse = new xajaxResponse();
	global $application;
	$ishare_access = $application->module_access("ishare");

	if($ishare_access != "ADMIN")
	{// FOr the employee, just access for the admins and techs users
		return $objResponse;
	}	
	if($application->userprefs['login'] == "root") {
		$objResponse->AddScript(
			"show_warning_message('<img src=\"" . $application->get_template_url() . "/images/warning.png\" /><img src=\"" . $application->get_template_url() . "/images/warning.png\" /><img src=\"" . $application->get_template_url() . "/images/warning.png\" /> " .
			" " . $application->LANG['LANG_Warning_superadmin_account'] . " " .
			"<img src=\"" . $application->get_template_url() . "/images/warning.png\" /><img src=\"" . $application->get_template_url() . "/images/warning.png\" /><img src=\"" . $application->get_template_url() . "/images/warning.png\" />', 'warning')");	
              	}	
	return $objResponse;
}

function ishare_restart_service($service) {	
	$objResponse = new xajaxResponse();
	
	global $application;
	$ishare_access = $application->module_access("ishare");
	if($ishare_access != "ADMIN")
	{// FOr the employee, just access for the admins and techs users
		return $objResponse;
	}
	
	exec("sudo ishare service " . escapeshellarg($service) . " restart");

	$content = $application->get_html_content("ishare_services_table.htm");
	show_services_status($application);	
	
	$content = $application->show_content($content, true);
	$objResponse->AddAssign("services_table", "innerHTML", $content);	
	
	return $objResponse;
}

function ishare_stop_service($service) {	
	$objResponse = new xajaxResponse();
	
	global $application;
	$ishare_access = $application->module_access("ishare");
	if($ishare_access != "ADMIN")
	{// FOr the employee, just access for the admins and techs users
		return $objResponse;
	}

	exec("sudo ishare service " . escapeshellarg($service) . " stop");
	
	$content = $application->get_html_content("ishare_services_table.htm");
	show_services_status($application);	
	
	$content = $application->show_content($content, true);
	$objResponse->AddAssign("services_table", "innerHTML", $content);	
	
	return $objResponse;

}

function ishare_printer_delete($printer) {
	$objResponse = new xajaxResponse();
	
	global $application;
	$ishare_access = $application->module_access("ishare");
	if($ishare_access != "ADMIN")
	{// FOr the employee, just access for the admins and techs users
		return $objResponse;
	}

	exec("sudo ishare  printer-remove " . escapeshellarg($printer));
	
	$content = $application->get_html_content("ishare_printers_table.htm");
	show_printers_status($application);	
	
	$content = $application->show_content($content, true);
	$objResponse->AddAssign("printers_table", "innerHTML", $content);	
	
	return $objResponse;

}



function ishare_check_updates() {
	global $application;
	$ishare_access = $application->module_access("ishare");
	if($ishare_access != "ADMIN")
	{// FOr the employee, just access for the admins and techs users
		$application->message_die("You have not access to this page");
	}  
                $content = $application->get_html_content("ishare_table_show_updates.htm");
	
	$objResponse = new xajaxResponse();
	
	//exec("dpkg -l igestis-core | grep igestis-core", $output2, $return);
	//preg_match("/[0-9]{1}[0-9\-\.A-Za-z]+/", implode(" ", $output2), $matches);	
	
	exec("sudo ishare check-update", $output, $return);	
	// 0 -> Pas de mise à jour
	// 1 -> Pas d'internet
	// 2 -> Script deja lancé
	// 3 -> Il y a des mises à jours (renvoies le numero de version)
	// 4 -> En cours d'installation
                if(is_array($output)) {
                    @reset($output);
                    foreach ($output as $module) {
                        list($module_name, $actual_version, $new_version) = explode(" ", $module);
                        $new_version = trim($new_version);
                        $application->add_block("MODULES_LIST", array(
                            "NAME" => $module_name,
                            "YOUR_VERSION"=>$actual_version,
                            "AVAILABLE_VERSION" => ($new_version == $actual_version || $new_version == "" ? "{LANG_ISHARE_No_updates}" : $new_version)
                        ));
                    }
                }
                
            
	if($return == 0) $message .= "{LANG_ISHARE_Your_version_is_up_to_date}";
	if($return == 1) $message .= "{LANG_ISHARE_Unable_to_connect_to_update_server}";
	if($return == 2) $message .= "{LANG_ISHARE_Script_already_running_on_another_page}";
	if($return == 3) 
	{
		$message .= "{LANG_ISHARE_Updates_availables}";
		$message .= " <a href=\"#\" onclick=\"javascript:install_updates()\"><img alt=\"Update\" src=\"" . $application->get_template_url() . "/images/" . $application->get_lang_img_folder() . "/ishare_update.png\"/></a>";
	}
	if($return == 4) $message .= "{LANG_ISHARE_Update_in_progress}";
        
                $application->add_var("CUSTOM_MESSAGE_WAIT_DURING_UPDATE", $message);
                $content = $application->show_content($content, true);
	$objResponse->AddAssign("table_show_updates", "innerHTML", $content);
	
	return $objResponse;
}

function ishare_install_updates() {
	global $application;
	$ishare_access = $application->module_access("ishare");
	if($ishare_access != "ADMIN")
	{// FOr the employee, just access for the admins and techs users
		die("You have not access to this page");
	}  
	
	$objResponse = new xajaxResponse();
	
	exec("sudo ishare install-update", $output, $return);	
	$objResponse->AddAssign("upgrades_check", "innerHTML", $application->LANG['LANG_ISHARE_Update_will_start_in_a_couple_of_minutes']);
	
	return $objResponse;
}


function ishare_start_remote_control() {
	global $application;
	$ishare_access = $application->module_access("ishare");
	if($ishare_access != "ADMIN")
	{// FOr the employee, just access for the admins and techs users
		die("You have not access to this page");
	}  
	
	$objResponse = new xajaxResponse();
	exec("sudo ishare remote-control-status", $output, $return);	
	
	if($return) {
		exec("sudo ishare remote-control-stop", $output, $return);
                $_SESSION['REMOTE_PASSWORD'] = "";
	}
	else {
                // Génération du mot de passe
                $password = "";
                for($i = 0; $i < 6; $i++) $password .= (string)rand(0, 9);
                $_SESSION['REMOTE_PASSWORD'] = $password;

		exec("sudo ishare remote-control-start " . escapeshellarg($_SESSION['REMOTE_PASSWORD']) . "> /dev/null 2>&1 &", $output, $return);
		sleep(1);
	}
	
	$return = NULL;
	$output = NULL;
	exec("sudo ishare remote-control-status", $output, $return);
	$application->add_var("remote_control_status", $return);
        $application->add_var("REMOTE_PASSWORD", $_SESSION['REMOTE_PASSWORD']);
	
	$content = $application->get_html_content("ishare_remote_control_status.htm");
	show_services_status($application);	
	
	$content = $application->show_content($content, true);
	$objResponse->AddAssign("remote_control_status", "innerHTML", $content);	
	
	return $objResponse;

}

function ishare_update_remote_control_status() {
	global $application;
	$ishare_access = $application->module_access("ishare");
	if($ishare_access != "ADMIN")
	{// FOr the employee, just access for the admins and techs users
		die("You have not access to this page");
	}  
	
	$objResponse = new xajaxResponse();
	
	$return = NULL;
	$output = NULL;
	exec("sudo ishare remote-control-status", $output, $return);
	$application->add_var("remote_control_status", $return);
        $application->add_var("REMOTE_PASSWORD", $_SESSION['REMOTE_PASSWORD']);
	
	$content = $application->get_html_content("ishare_remote_control_status.htm");
	show_services_status($application);	
	
	$content = $application->show_content($content, true);
	$objResponse->AddAssign("remote_control_status", "innerHTML", $content);	
	
	return $objResponse;

}


function ishare_update_additionnal_modules_list() {
	global $application;
	$ishare_access = $application->module_access("ishare");
	if($ishare_access != "ADMIN")
	{// FOr the employee, just access for the admins and techs users
		die("You have not access to this page");
	}  
	
	$objResponse = new xajaxResponse();
	ishare_update_modules_list();	
	
	$content = $application->get_html_content("ishare_additional_modules_list.htm");	
	$content = $application->show_content($content, true);
	$objResponse->AddAssign("additional_modules_span", "innerHTML", $content);
	
	return $objResponse;
}

function ishare_install_module($module) {
	global $application;
	$ishare_access = $application->module_access("ishare");
	if($ishare_access != "ADMIN")
	{// FOr the employee, just access for the admins and techs users
		die("You have not access to this page");
	}  
	
	$objResponse = new xajaxResponse();
	
	exec("sudo ishare module-install " . escapeshellarg($module), $output, $return);
	//echo "sudo apt-get install " . escapeshellarg($module);
	
	ishare_update_modules_list($module);	
	
	$content = $application->get_html_content("ishare_additional_modules_list.htm");	
	$content = $application->show_content($content, true);
	
	$objResponse->AddAssign("additional_modules_span", "innerHTML", $content);
	return $objResponse;
}

function ishare_uninstall_module($module) {
	global $application;
	$ishare_access = $application->module_access("ishare");
	if($ishare_access != "ADMIN")
	{// FOr the employee, just access for the admins and techs users
		die("You have not access to this page");
	}  
	
	$objResponse = new xajaxResponse();
	
	exec("sudo ishare module-uninstall " . escapeshellarg($module));
	
	ishare_update_modules_list();	
	
	$content = $application->get_html_content("ishare_additional_modules_list.htm");
	$content = $application->show_content($content, true);
	$objResponse->AddAssign("additional_modules_span", "innerHTML", $content);
	
	return $objResponse;
}



?>