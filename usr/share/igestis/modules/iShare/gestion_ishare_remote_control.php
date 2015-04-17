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

exec("sudo ishare remote-control-status", $output, $return);
$application->add_var("remote_control_status", $return);

// Create content :
$CONTENT = $application->get_html_content("ishare_gestion_remote_control.htm");
if(!$CONTENT) $application->message_die("Unable to find the html page");
$application->set_page_title("{LANG_ISHARE_Gestion_remote_control_description_short}");



################## Create the content of the page #################################
		
$replace = array("MENU" => $application->generate_menu());
$application->add_vars($replace);
$application->show_content($CONTENT);


?>