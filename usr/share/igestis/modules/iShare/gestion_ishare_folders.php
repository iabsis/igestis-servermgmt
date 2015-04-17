<?

// If a little malicious guy attempt to launche this file directly, application stop with the message below ...
if(!defined("INDEX_LAUNCHED")) die("Hacking attempt");

include SERVER_FOLDER . "/" . APPLI_FOLDER . "/modules/iShare/config.php";

$ishare_access = $application->module_access("ishare");
if($ishare_access != "ADMIN")
{// FOr the employee, just access for the admins and techs users
	$application->message_die("You have not access to this page");
}


// Create content :
$CONTENT = $application->get_html_content("ishare_gestion_folders.htm");
if(!$CONTENT) $application->message_die("Unable to find the html page");
$application->set_page_title("{LANG_ISHARE_Gestion_folders_description_short}");

$hdir = opendir(ISHARE_FOLDERS) ;
$cpt = 0;

while (false !== ($file = readdir($hdir))) 
{
	if(substr($file, 0, 1) != ".")
	{
		if (is_dir(ISHARE_FOLDERS . "/" . $file)) 
		{
			if ($file != "." && $file != "..")
			{
				$application->add_block("ISHARE_folders_LIST", array(
					"folder_name" => str_replace("'", "\\'", $file),
                                        "show_folder_name" => $file,
					"CLASS" => ($cpt++%2 ? "ligne1" : "ligne2")
				));
			}
		}

	}
}

@closedir(ISHARE_FOLDERS);

################## Create the content of the page #################################
		
$replace = array("MENU" => $application->generate_menu());
$application->add_vars($replace);
$application->show_content($CONTENT);




?>