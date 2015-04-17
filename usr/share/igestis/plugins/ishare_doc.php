<?

// If a little malicious guy attempt to launche this file directly, application stop with the message below ...
if(!defined("INDEX_LAUNCHED")&& !defined("GENERAL_INDEX_REQUEST_LAUNCHED")) die("Hacking attempt");

if($application->userprefs['user_type'] == "employee") {
    // Création du contenu de l'applet uniquement pour les employés
    $applet_id = $this->add_applet("ISHARE_DOCS", "Documentation", TRUE);


    $user_rights = $application->module_access("ishare");

    // Création du contenu de l'applet
    $data = "";
    $file = $application->get_html_content("plugins/ishare_doc.htm");
    $data = $file;
    $application->add_var("ISHARE_DOC_IS_ADMIN", ($user_rights == "ADMIN"));

    // Ajout du contenu de l'applet
    $this->set_applet_data($applet_id, $data);
}

?>