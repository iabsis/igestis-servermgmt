<?
 
// If a little malicious guy attempt to launche this file directly, application stop with the message below ...
if(!defined("INDEX_LAUNCHED")) die("Hacking attempt");

include SERVER_FOLDER . "/" . APPLI_FOLDER . "/modules/iShare/config.php";
include SERVER_FOLDER . "/" . APPLI_FOLDER . "/modules/iShare/lib_ishare.php";
include SERVER_FOLDER . "/" . APPLI_FOLDER . "/modules/iShare/index_common.php";

$ishare_access = $application->module_access("ishare");
if($ishare_access != "ADMIN")
{// FOr the employee, just access for the admins and techs users
	$application->message_die("You have not access to this page");
}      

// Defined in index_common.php
show_services_status($application);

// Gestion de l'espace disque restant
exec("df -h | grep \"^/\"", $output, $return);

$cpt = 0;
if(is_array($output)) {
	foreach($output as $disk) {
		preg_match("/^([^ ]+)[ ]+([^ ]+)[ ]+([^ ]+)[ ]+([^ ]+)[ ]+([0-9\.\,]+)%[ ]+([^ ]+)$/", $disk, $matches);
		if($matches) {
			if($matches[6] == "/") $matches[1] = $application->LANG['LANG_ISHARE_System_partition'];
			if($matches[6] == "/home") $matches[1] = $application->LANG['LANG_ISHARE_Data_partition'];
			$application->add_block("HD_list", array(
				"CLASS" => ($cpt++%2 ? "ligne1" : "ligne2"),
				"NAME"  => $matches[1],
				"USED"  => $matches[5]
			));
		}
	}
}

$output = $return = NULL;
exec("ishare check-backup-status", $output, $return);


if($return == 0) $save_status = "{LANG_ISHARE_Save_worked}";
else $save_status = "{LANG_ISHARE_Save_Didnt_worked}";
$message = $output[0];

$application->add_vars(array(
	"SAVE_STATUS"  => $save_status,
	"SAVE_MESSAGE" => $message
));



// Create content :
$CONTENT = $application->get_html_content("ishare_gestion_services.htm");
if(!$CONTENT) $application->message_die("Unable to find the html page");
$application->set_page_title("{LANG_ISHARE_Gestion_services_description_short}");



################## Create the content of the page #################################
		
$replace = array("MENU" => $application->generate_menu());
$application->add_vars($replace);
$application->show_content($CONTENT);




?>
