<?php

namespace Igestis\Modules\ServerMgmt;
/**
 * Accounting management
 *
 * @author Olivier Bitsch
 */
class SambaController extends \IgestisController {

    /**
     * Show the list of accountings
     */
    public function indexAction() {

        $modulesList = \IgestisModulesList::getInstance();
        exec("../modules/ServerMgmt/bin/helper getDataFolderAcl", $aclDump);

        foreach($aclDump as $line){
//          if(ereg("^# file: (.*\/)*", $line, $match)) {
        if(ereg("^# file: (.*\/)*", $line, $match)) {
              print $match[0] . "<br>";
    					print str_replace($line, $match[0]);
  				}
        }
        exit;


        $this->context->render("ServerMgmt/pages/sambaListSharing.twig", array(
            'data_table' =>  array_diff(scandir(ConfigModuleVars::dataFolder()), array('..', '.'))
        ));
    }
}
