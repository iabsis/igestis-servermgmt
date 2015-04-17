<?
 
// If a little malicious guy attempt to launche this file directly, application stop with the message below ...
if(!defined("INDEX_LAUNCHED")) die("Hacking attempt");

include SERVER_FOLDER . "/" . APPLI_FOLDER . "/modules/iShare/config.php";
include SERVER_FOLDER . "/" . APPLI_FOLDER . "/modules/iShare/lib_ishare.php";


$ishare_access = $application->module_access("ishare");
if($ishare_access != "ADMIN")
{// FOr the employee, just access for the admins and techs users
	$application->message_die("You have not access to this page");
}



exec("sudo ishare backup-crontab-get", $return);

$time = explode(" ", $return[0]);
$minute = $time[0];
$hour = $time[1];
if(strlen($minute) < 2) $minute = "0" . $minute;
if(strlen($hour) < 2) $hour = "0" . $hour;

$application->add_var("saving_time", $hour . ":" . $minute);

// Gestion des dossiers à sauvegarder
$folder_list = file(ISHARE_BACKUP_FILE_LIST);
$comment_passed = false;

if(is_array($folder_list)) {
	$cpt = 0;
	foreach($folder_list as $folder) {
		if(trim($folder) == "# Folder added by iShare") {
			$comment_passed = true;
			continue;
		}
		if(trim($folder)) {
			$application->add_block("FOLDERS_LIST", array (
				"CLASS" => ($cpt++%2 ? "ligne1" : "ligne2"),
				"folder" => $folder,
				"is_editable" => $comment_passed
			));
		}
	}
}



	
// Create content :
$CONTENT = $application->get_html_content("ishare_gestion_restore_admin.htm");
if(!$CONTENT) $application->message_die("Unable to find the html page");
$application->set_page_title("{LANG_ISHARE_Gestion_restore_admin_description_short}");


################## Create the content of the page #################################
		
$replace = array("MENU" => $application->generate_menu());
$application->add_vars($replace);
$application->show_content($CONTENT);




?>