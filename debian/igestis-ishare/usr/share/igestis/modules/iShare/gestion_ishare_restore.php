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

if(strtolower($application->userprefs['login']) == "root") $application->add_var("IS_ROOT", true);
$application->add_var("DATE", $_GET['date']);
$application->add_var("FOLDER", $_GET['folder']);
$application->add_var("SHARED_FOLDER", $_GET['shared_folder']);


exec("sudo ishare tape_mounted", $out, $return);
$application->add_var("TAPE_MOUNTED", $return);

if($return == 2) $application->message_die("{LANG_No_save_drive_found}");


// Verification de l'etat de la sauvegarde
exec("ishare save_launched", $output, $return);
if($return == 1) {
	// Create content :
	$CONTENT = $application->get_html_content("ishare_gestion_restore_unable.htm");
	if(!$CONTENT) $application->message_die("Unable to find the html page");
        $application->set_page_title("{LANG_ISHARE_Gestion_restore_file_error_description_short}");
}
else {			
            if(!$_GET['folder'])
            {
                    $sql = "SELECT * FROM USERS, CONTACTS WHERE CONTACTS.user_id=USERS.id AND USERS.user_type='employee' AND CONTACTS.active=1 AND login!='root'";
                    $req = mysql_query($sql) or $application->message_die(mysql_error() . $sql);
                    $cpt = 0;
                    while($user = mysql_fetch_array($req)) {
                            $application->add_block("FOLDERS_LIST", array(
                                    "folder" => "/home/" . $user['login'],
                                    "folder_name" => $user['user_label'] . " (" . $user['login'] . ")",
                                    "CLASS" => ($cpt++%2 ? "ligne1" : "ligne2")
                            ));
                    }
            }
            else {
                    list_folder($_GET['folder'], $application, "FOLDERS_LIST", $_GET['date']);
            }

            // Gestion de l'affichage de l'arborescence pour les restaurations de profiles
            $profiles_folder = substr($_GET['folder'], 5);
            if($profiles_folder && $profiles_folder != "/")
            {
                    $application->add_block("exploration_profils_restore", array("icon" => "folder_closed.gif", "folder" => "", "folder_name" => "Root"));

                    $folder_list = explode("/", $profiles_folder);
                    $complete_folder = "/home";

                    if(is_array($folder_list))
                    {
                            foreach($folder_list as $folder)
                            {
                                    if($folder)
                                    {
                                            $complete_folder .= "/" . $folder;
                                            $application->add_block("exploration_profils_restore", array("icon" => "folder_closed.gif", "folder" => $complete_folder, "folder_name" => $folder));
                                    }
                            }
                    }
            }

            if(!$_GET['shared_folder']) $_GET['shared_folder'] = ISHARE_FOLDERS;
            list_folder($_GET['shared_folder'], $application, "SHARED_FOLDERS_LIST", $_GET['date']);

            // Gestion de l'affichage de l'arborescence pour les restaurations avancées
            $folder = substr($_GET['shared_folder'], strlen(ISHARE_FOLDERS));

            if($folder && $folder != "/")
            {
                    $application->add_block("exploration_shared_files_restore", array("folder" => "", "folder_name" => "ROOT"));

                    $folder_list = explode("/", $folder);
                    $complete_folder = ISHARE_FOLDERS;

                    if(is_array($folder_list))
                    {
                            foreach($folder_list as $folder)
                            {
                                    if($folder)
                                    {
                                            $complete_folder .= "/" . $folder;
                                            $application->add_block("exploration_shared_files_restore", array("folder" => $complete_folder, "folder_name" => $folder));
                                    }
                            }
                    }
            }
	
	
	if(is_dir(ISHARE_TAPE_FOLDER . "/" . $_GET['date'] . "/mysql/")) {
		$hdir = opendir(ISHARE_TAPE_FOLDER . "/" . $_GET['date'] . "/mysql/") ;
		while (false !== ($file = readdir($hdir))) 
		{
			if(substr($file, 0, 1) != ".")
			{
				if (!is_dir($BASE_FOLDER . $_GET['dossier'] . "/" . $file)) 
				{
					if(!preg_match("/[\w\W]+\.sql/", $file)) continue;
					$application->add_block("BASES_LIST", array(
						"base_name" => str_replace(".sql", "", $file),
						"CLASS" => ($cpt++%2 ? "ligne1" : "ligne2")
					));
				}
			}
		}
		closedir($hdir);
	}	
	
	// Create content :
	$CONTENT = $application->get_html_content("ishare_gestion_restore.htm");
	if(!$CONTENT) $application->message_die("Unable to find the html page");
        $application->set_page_title("{LANG_ISHARE_Gestion_restore_file_description_short}");
}



################## Create the content of the page #################################
		
$replace = array("MENU" => $application->generate_menu());
$application->add_vars($replace);
$application->show_content($CONTENT);




?>