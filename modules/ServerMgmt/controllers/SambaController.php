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

    public function changeRightAction($folderName, $employeeAccount, $right) {
      $ajaxResponse = new \Igestis\Ajax\AjaxResult();

      exec("/usr/bin/sudo ../modules/ServerMgmt/bin/helper setDataFolderAcl " . $employeeAccount . " " . $folderName . " " . $right, $message, $returncode);

      if (returncode == 0) {
        $ajaxResponse->addWizz(\Igestis\I18n\Translate::_("Right changed successfully"), \WIZZ::$WIZZ_SUCCESS);
      } else {
        $ajaxResponse->setError(\Igestis\I18n\Translate::_("Error during the folder right change: " . $message));
      };

    }
}
