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

$file = file_get_contents("/etc/postfix/main.cf");

preg_match_all("/mydestination[ ]?=[ ]?([A-Za-z0-9\.\_\-#@\, ]+)/", $file, $return);
$domain_server = str_ireplace("serveur.domaine.local, localhost.domaine.local, , localhost, ishare.homelinux.com,", "", trim($return[1][0]));
$application->add_var("domain_server", trim($domain_server));

preg_match_all("/relayhost[ ]?=[ ]?([A-Za-z0-9\.\_\-#@\, ]+)/", $file, $return);
$application->add_var("smtp_server", trim($return[1][0]));

// Create content :
$CONTENT = $application->get_html_content("ishare_gestion_mail_service.htm");
if(!$CONTENT) $application->message_die("Unable to find the html page");
$application->set_page_title("{LANG_ISHARE_Gestion_mail_description_short}");



################## Create the content of the page #################################
		
$replace = array("MENU" => $application->generate_menu());
$application->add_vars($replace);
$application->show_content($CONTENT);




?>