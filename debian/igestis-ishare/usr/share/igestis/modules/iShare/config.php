<?
if(!defined("ISHARE_FOLDERS")) define("ISHARE_FOLDERS", "/home/samba/data/");
if(!defined("ISHARE_TAPE_FOLDER")) define("ISHARE_TAPE_FOLDER", "/var/home/restore_tape/");
if(!defined("ISHARE_BACKUP_FILE_LIST")) define("ISHARE_BACKUP_FILE_LIST", "/etc/ishare/backup/folder_list.conf");



$MODULE_NAME = "iShare"; 

$iShare['module_name'] = "{LANG_ISHARE_MODULE_NAME}";

$iShare['rights_list'][0]['RIGHT_NAME'] = "{LANG_ISHARE_RIGHT_NONE}";
$iShare['rights_list'][0]['RIGHT_CODE'] = "NONE";
$iShare['rights_list'][0]['RIGHT_HELP'] = "{LANG_ISHARE_Help_none}";

$iShare['rights_list'][1]['RIGHT_NAME'] = "{LANG_ISHARE_RIGHT_ADMIN}";
$iShare['rights_list'][1]['RIGHT_CODE'] = "ADMIN";
$iShare['rights_list'][1]['RIGHT_HELP'] = "{LANG_ISHARE_Help_admin}";

$iShare['module_menu_name']['title'][0] = "{LANG_ISHARE_MODULES_INSTALL_TITLE}";
$iShare['module_menu_name']['script_file'][0] = "gestion_ishare_rmodules_install.php";
$iShare['module_menu_name']['client_access'][0] = false;
$iShare['module_menu_name']['administration_section'][0] = true;
$iShare['module_menu_name']['employee_access'][0] = array("ADMIN");

$iShare['module_menu_name']['title'][1] = "{LANG_ISHARE_FOLDERS_TITLE}";
$iShare['module_menu_name']['script_file'][1] = "gestion_ishare_folders.php";
$iShare['module_menu_name']['client_access'][1] = false;
$iShare['module_menu_name']['administration_section'][1] = true;
$iShare['module_menu_name']['employee_access'][1] = array("ADMIN");

$iShare['module_menu_name']['title'][2] = "{LANG_ISHARE_MAIL_SERVICE_TITLE}";
$iShare['module_menu_name']['script_file'][2] = "gestion_ishare_mail_service.php";
$iShare['module_menu_name']['client_access'][2] = false;
$iShare['module_menu_name']['administration_section'][2] = true;
$iShare['module_menu_name']['employee_access'][2] = array("ADMIN");

$iShare['module_menu_name']['title'][3] = "{LANG_ISHARE_RESTORE_TITLE}";
$iShare['module_menu_name']['script_file'][3] = "gestion_ishare_restore.php";
$iShare['module_menu_name']['client_access'][3] = false;
$iShare['module_menu_name']['administration_section'][3] = true;
$iShare['module_menu_name']['employee_access'][3] = array("ADMIN");

$iShare['module_menu_name']['title'][4] = "{LANG_ISHARE_RESTORE_ADMIN_TITLE}";
$iShare['module_menu_name']['script_file'][4] = "gestion_ishare_restore_admin.php";
$iShare['module_menu_name']['client_access'][4] = false;
$iShare['module_menu_name']['administration_section'][4] = true;
$iShare['module_menu_name']['employee_access'][4] = array("ADMIN");

$iShare['module_menu_name']['title'][5] = "{LANG_ISHARE_SERVICES_TITLE}";
$iShare['module_menu_name']['script_file'][5] = "gestion_ishare_services.php";
$iShare['module_menu_name']['client_access'][5] = false;
$iShare['module_menu_name']['administration_section'][5] = true;
$iShare['module_menu_name']['employee_access'][5] = array("ADMIN");

$iShare['module_menu_name']['title'][6] = "{LANG_ISHARE_PRINTERS_TITLE}";
$iShare['module_menu_name']['script_file'][6] = "gestion_ishare_printers.php";
$iShare['module_menu_name']['client_access'][6] = false;
$iShare['module_menu_name']['administration_section'][6] = true;
$iShare['module_menu_name']['employee_access'][6] = array("ADMIN");

$iShare['module_menu_name']['title'][7] = "{LANG_ISHARE_REMOTE_CONTROL_TITLE}";
$iShare['module_menu_name']['script_file'][7] = "gestion_ishare_remote_control.php";
$iShare['module_menu_name']['client_access'][7] = false;
$iShare['module_menu_name']['administration_section'][7] = true;
$iShare['module_menu_name']['employee_access'][7] = array("ADMIN");


?>
