<?
session_start();

include "../../config.php";
include "../../includes/common_librairie.php";

// Initialization of the application
$application = new application();

if(!$application->is_loged)
{// Loged or not loged, that's the question.
	die("You are not loged");
}

if(!$application->userprefs['login'] == "root") $application->message_die("You must login with root account");
if(ereg('^\.\.\/|\/\.\.\/|\/\.\.$|^\.\.$', $_GET['file'])) die("Hacking attempt");
$BASE_FOLDER = create_smb_url() . "/.TAPE/";

#########################################################################################################
download($_GET['file']);
function download($file){
   global $BASE_FOLDER;
   $filename = basename($file);
   $file = $BASE_FOLDER . $file;
  
   //First, see if the file exists
   if (!is_file($file)) {
       header("HTTP/1.0 403 Forbidden" );
       exit;
   }

   //Secondly, see if we have access to the file
   if(!smb::is_file_accessible($BASE_FOLDER . $_GET['fichier'])) {
       header("HTTP/1.0 403 Forbidden" );
       exit;
   }


   //Gather relevent info about file
   $len = filesize($file);
   
   $file_extension = strtolower(substr(strrchr($filename,"."),1));

   //This will set the Content-Type to the appropriate setting for the file
   switch( $file_extension ) {
     case "pdf": $ctype="application/pdf"; break;
     case "exe": $ctype="application/octet-stream"; break;
     case "zip": $ctype="application/zip"; break;
     case "doc": $ctype="application/msword"; break;
     case "xls": $ctype="application/vnd.ms-excel"; break;
     case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
     case "gif": $ctype="image/gif"; break;
     case "png": $ctype="image/png"; break;
     case "jpeg":
     case "jpg": $ctype="image/jpg"; break;
     case "mp3": $ctype="audio/mpeg"; break;
     case "wav": $ctype="audio/x-wav"; break;
     case "mpeg":
     case "mpg":
     case "mpe": $ctype="video/mpeg"; break;
     case "mov": $ctype="video/quicktime"; break;
     case "avi": $ctype="video/x-msvideo"; break;

     //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
     case "php":
     //case "htm":
     //case "html":
     die("<b>Cannot be used for ". $file_extension ." files!</b>"); break;

     default: $ctype="application/force-download";
   }

   //Begin writing headers
   header("Pragma: public");
   header("Expires: 0");
   header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
   header("Cache-Control: public");
   header("Content-Description: File Transfer");
  
   //Use the switch-generated Content-Type
   header("Content-Type: $ctype");

   //Force the download
   $header="Content-Disposition: attachment; filename=\"".$filename."\";";
   header($header );
   header("Content-Transfer-Encoding: binary");
   header("Content-Length: ".$len);
   
   @readfile($file);
   exit;
}


?>