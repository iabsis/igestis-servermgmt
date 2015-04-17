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
show_printers_status($application);


// Create content :
$CONTENT = $application->get_html_content("ishare_gestion_printers.htm");
if(!$CONTENT) $application->message_die("Unable to find the html page");
$application->set_page_title("{LANG_ISHARE_Gestion_my_printers_description_short}");


################## Create the content of the page #################################
		
$replace = array("MENU" => $application->generate_menu());
$application->add_vars($replace);
$application->show_content($CONTENT);




?>