<?
session_start();

include "../../config.php";
include "config.php";
include "lib_ishare.php";
include "../../includes/common_librairie.php";
 
// Initialization of the application
$application = new application();

if(!$application->is_loged)
{// Loged or not loged, that's the question.
	$application->login_form();
}

#################### Gestion des ISHARE_folders #########################################################
if ($_GET['section'] == "ishare_folders" || $_POST['section'] == "ishare_folders")
{
	$ishare_access = $application->module_access("ishare");
	if($ishare_access != "ADMIN")
	{
		$application->message_die("{LANG_Not_Access_to_thie_page}", true);
	}

	if ($_POST['action'] == "edit")
	{// Edition d'un ishare_folders
		if($_POST['id'] != $_POST['folder_name']) {
			if(is_array(ISHARE_FOLDERS . "/" . $_POST['id'])) {
				$application->message_die("This folder already exists !");
			}
			else exec("mv " . escapeshellarg(ISHARE_FOLDERS . "/" . $_POST['id']) . " " . escapeshellarg(ISHARE_FOLDERS . "/" . $_POST['folder_name']));
		}
		
		exec("setfacl -b " . escapeshellarg(ISHARE_FOLDERS . "/" . $_POST['folder_name']));
		exec("chmod -R 700 "  . escapeshellarg(ISHARE_FOLDERS . "/" . $_POST['folder_name']));
			
		if(is_array($_POST['assoc_user'])) {
			foreach($_POST['assoc_user'] as $login) {
				exec("setfacl -m u:" . escapeshellcmd($login) . ":rwx " . escapeshellarg(ISHARE_FOLDERS . "/" . $_POST['folder_name']));
			}
		}

		die ("<script language='javascript'>window.opener.location.reload(true); window.close();</script>") ;
	}
	
	if ($_POST['action'] == "new")
	{// Cration d'un nouveau ishare_folders
	
		if(!is_dir(ISHARE_FOLDERS . $_POST['folder_name'])) 
		{
			mkdir(ISHARE_FOLDERS . "/" . $_POST['folder_name']);
			exec("setfacl -b " . escapeshellarg(ISHARE_FOLDERS . "/" . $_POST['folder_name']));
			exec("chmod -R 700 "  . escapeshellarg(ISHARE_FOLDERS . "/" . $_POST['folder_name']));
			
			if(is_array($_POST['assoc_user'])) {
				foreach($_POST['assoc_user'] as $login) {
					exec("setfacl -m u:" . escapeshellcmd($login) . ":rwx " . escapeshellarg(ISHARE_FOLDERS . "/" . $_POST['folder_name']));
				}
			}
		}		
		
		die ("<script language='javascript'>window.opener.location.reload(true); window.close();</script>") ;		
	}

	if ($_GET['action'] == "del")
	{// Suppression d'un ishare_folders
		
		if(is_folder_empty(ISHARE_FOLDERS . "/" . $_GET['id'] . "/")) rmdir(ISHARE_FOLDERS . "/" . $_GET['id'] . "/");
		else $application->message_die($application->LANG["LANG_ISHARE_Delete_file_before_delete_share"], false, MANAGIS_MESSAGE_INFO);

                header("location:" . $_SERVER['HTTP_REFERER']) ;
		exit;
	}
} ############################################################################################################


if($_POST['section'] == "mail_service") {

	$ishare_access = $application->module_access("ishare");
	if($ishare_access != "ADMIN")
	{// FOr the employee, just access for the admins and techs users
		$application->message_die("You have not access to this page");
	}

	if($_POST['action'] == "edit") {
		exec('sudo /usr/bin/ishare postfix "' . escapeshellcmd($_POST['domain_server']) . '"');
		exec('sudo /usr/bin/ishare smtp "' . escapeshellcmd($_POST['smtp_server']) . '"');
		header("location:../../.." . urldecode($_POST['page_url']));
		exit;
	}

}

if($_POST['section'] == "config" || $_GET['section'] == "config") {
	$ishare_access = $application->module_access("ishare");
	if($ishare_access != "ADMIN")
	{// FOr the employee, just access for the admins and techs users
		$application->message_die("You have not access to this page");
	}
	
	if($_POST['action'] == "saving_time") {	
		if(!preg_match("/^([0-1][0-9]|2[0-3]|[0-9]):([0-5][0-9])$/", $_POST['saving_time'])) $application->message_die("Invalid time format");	
		$time = explode(":", $_POST['saving_time']);
		exec("sudo ishare backup-crontab-set " . escapeshellarg($time[1]) . " " . escapeshellarg($time[0]));
		header("location:../../.." . urldecode($_POST['page_url']));
		exit;
	}
	
	if($_POST['action'] == "edit_folder") {
		$folder_list = file(ISHARE_BACKUP_FILE_LIST);
		$folder_list = array_map("trim", $folder_list);
		$comment_passed = false;
		if(is_array($folder_list) && !in_array($_POST['folder_name'], $folder_list)) {			
			for($i = 0; $i < count($folder_list); $i++) {
				if(trim($folder_list[$i]) == "# Folder added by iShare") {
					$comment_passed = true;
				}
				
				if(trim($folder_list[$i]) == trim($_POST['old_folder_name'])) {
					$folder_list[$i] = $_POST['folder_name'];
					break;
				}
			}
			
			$folder_list = implode("\n", $folder_list);
			$f = fopen(ISHARE_BACKUP_FILE_LIST, "w");
			fwrite($f, trim($folder_list));
			fclose($f);
			
			die ("<script language='javascript'>window.opener.location.reload(true); window.close();</script>") ;
		}		
	}
	
	if($_POST['action'] == "new_folder") {
		$folder_list = file(ISHARE_BACKUP_FILE_LIST);
		$folder_list = array_map("trim", $folder_list);
		
		if(!in_array($_POST['folder_name'], $folder_list)) {
		
			if(!in_array("# Folder added by iShare", $folder_list)) $folder_list[] = "# Folder added by iShare";
			$folder_list[] = $_POST['folder_name'];
			$folder_list = implode("\n", $folder_list);
			
			$f = fopen(ISHARE_BACKUP_FILE_LIST, "w");
			fwrite($f, trim($folder_list));
			fclose($f);
			
			die ("<script language='javascript'>window.opener.location.reload(true); window.close();</script>") ;
		}		
	}
	
	if($_GET['action'] == "del_folder") {
		$folder_list = file(ISHARE_BACKUP_FILE_LIST);
		$folder_list = array_map("trim", $folder_list);
		
		$new_folder_list = NULL;
		$deleted = false;
		
		for($i = 0; $i < count($folder_list); $i++) {
			if(!$deleted && trim($_GET['folder_name']) == trim($folder_list[$i])) continue;
			if(!trim($folder_list[$i])) continue;
			$new_folder_list[] = $folder_list[$i];
		}
		
		$new_folder_list = implode("\n", $new_folder_list);
		$f = fopen(ISHARE_BACKUP_FILE_LIST, "w");
		fwrite($f, trim($new_folder_list));
		fclose($f);	
		header("location:../../.." . urldecode($_GET['page_url']));
		exit;
	}
}


@mysql_close();

?>