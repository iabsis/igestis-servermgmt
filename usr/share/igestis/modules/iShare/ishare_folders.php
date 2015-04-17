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



switch($_GET['action'])
{
	case "edit" :			
		$CONTENT = $application->get_html_content("ishare_folders.htm");
		if(!$CONTENT) $application->message_die("{LANG_Unable_to_find_the_ishare_folders_form}");
                $application->set_page_title("{LANG_ISHARE_New_ISHARE_folders_description_short}");

		


		$to_replace = array(
					"id" => htmlentities($_GET['id'],0, "UTF-8"),
					"action" => "edit", 
					"folder_name" => htmlentities($_GET['id'], ENT_COMPAT, "UTF-8"));
		
		exec("getfacl " . escapeshellarg(ISHARE_FOLDERS . "/" . $_GET['id']), $user_access_list);
		if(is_array($user_access_list)) {
			$cpt = 0;
			$user_access = NULL;
			foreach($user_access_list as $user) {
				$cpt++;
				if($cpt < 4) continue;
				if(preg_match("/^user\:([\w]+)\:/", $user, $return)) {
					$user_access[] = $return[1];
				}	
			}
		}
				
		$application->add_vars($to_replace);
		break;
	
	default :
		// Create content :
		$CONTENT = $application->get_html_content("ishare_folders.htm");
		if(!$CONTENT) $application->message_die("{LANG_Unable_to_find_the_ishare_folders_form}");
                $application->set_page_title("{LANG_ISHARE_New_ISHARE_folders_description_short}");
		$id = 0;

		
		$to_replace = array(
					"id" => '',
					"action" => "new",
					"folder_name" => '');
		
		$application->add_vars($to_replace);		
		break;
}



// Affichage des users :
$sql = "SELECT user_label, CONTACTS.login
		FROM CONTACTS, USERS 
		WHERE USERS.id=CONTACTS.user_id AND user_type='employee' AND CONTACTS.active=1 
		ORDER BY user_label";
$req = mysql_query($sql) or die(mysql_error() . $sql);
while($user = mysql_fetch_array($req)) {
        if($user['login'] == "root") continue;
	$has_access = false;
	if(is_array($user_access) && in_array($user['login'], $user_access)) $has_access = true;

	$application->add_block("user_list", array(
		"user_label" => $user['user_label'],
		"has_folder_access" => $has_access,
		"login" => $user['login']
	));
}


################## Create the content of the page #################################

$application->add_vars(array("MENU" => $application->generate_menu(), "XAJAX" => $script_xajax));
$application->show_content($CONTENT);


?>