<?
session_start();
 
include "../../config.php";
include "../../includes/common_librairie.php";
include "../../includes/xajax/xajax.inc.php";
require_once("../../index_common.php");

// Initialization of the application
$application = new application();

if(!$application->is_loged)
{// Loged or not loged, that's the question.
	die("You are not loged");
}

########################  !! Access to this page !! ###############################
$ishare_access = $application->module_access("ishare");
if($ishare_access != "ADMIN")
{// FOr the employee, just access for the admins and techs users
	$application->message_die("You have not access to this page");
}
###################################################################################
// Functions for this page



###################################################################################
$CONTENT = $application->get_html_content("ishare_edit_folder_list.htm");
if(!$CONTENT) $application->message_die("{LANG_Unable_to_find_the_ishare_folders_form}");
$application->set_page_title("{LANG_ISHARE_New_ISHARE_saved_folders_description_short}");

switch($_GET['action'])
{
	case "edit" :			
		$application->add_vars(array(
			"old_folder_name" => htmlentities($_GET['folder_name'], ENT_COMPAT, "UTF-8"),
			"folder_name" => htmlentities($_GET['folder_name'], ENT_COMPAT, "UTF-8"),
			"action" => "edit_folder"
		));		
		break;
	
	default :		
		$application->add_vars(array(
			"old_folder_name" => htmlentities($_GET['folder_name'], ENT_COMPAT, "UTF-8"),
			"folder_name" => htmlentities($_GET['folder_name'], ENT_COMPAT, "UTF-8"),
			"action" => "new_folder"
		));
		break;
}

################## Create the content of the page #################################

$application->add_vars(array("MENU" => $application->generate_menu(), "XAJAX" => $script_xajax));
$application->show_content($CONTENT);


?>