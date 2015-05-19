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
          if(ereg("^# file: (.*\/)*", $line, $match)) {
                $folder=preg_replace('/^\#\ file: (.*\/)*/','', $line);
      					$folderTree[$folder] = "";
    			}

          if(ereg("^user:.+:", $line, $match)) {
            $user = preg_replace('/:([rwx-])*/','', preg_replace('/^user:/','', $line));
            $right = preg_replace('/user:.+:/', '', $line);
            if ($folderTree[$folder]) {
              $folderTree[$folder] = array_merge($folderTree[$folder], array($user => $right));
            } else {
              $folderTree[$folder] = array($user => $right);
            }
    			}
        }

        $employees = $this->context->entityManager->getRepository("CoreContacts")->getEmployeesList();

        $this->context->render("ServerMgmt/pages/sambaListSharing.twig", array(
            'data_table' =>  $folderTree,
            'employee_list' => $employees
        ));
    }
}
