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

      exec("/usr/bin/sudo ../modules/ServerMgmt/bin/helper setDataFolderAcl "
         . escapeshellarg($employeeAccount) . " "
         . escapeshellarg(trim($folderName)) . " "
         . escapeshellarg($right), $message, $returncode);

      if ($returncode == 0) {
        header("Content-Type: application/json");
        die(json_encode(array('result'=>'success')));
      } else {
        header("Content-Type: application/json");
        die(json_encode(array('error'=>$message[0])));
      }

    }

    public function createNewFolder() {


      if ($this->request->IsPost()) {

        $folderName = $this->request->getPost("folderName");

        if (strstr($folderName, "/")) {
          new \wizz(_("Error during the folder creation: the folder cannot contain any slashes"), \WIZZ::$WIZZ_ERROR);
          $this->redirect(\ConfigControllers::createUrl("servermgmt_samba_index"));
        }

        exec("/usr/bin/sudo ../modules/ServerMgmt/bin/helper createDataFolder "
          . escapeshellarg(trim($folderName)), $message, $returncode);

        if ($returncode == 0) {
          new \wizz(_("The folder " . $folderName . " has been created successfully"), \WIZZ::$WIZZ_SUCCESS);
        } else {
          new \wizz(_("Error during the creation of " . $folderName . " folder: " . $message[0]), \WIZZ::$WIZZ_ERROR);
        };
      }
      $this->redirect(\ConfigControllers::createUrl("servermgmt_samba_index"));

    }


    public function renameFolder()
    {
        $ajaxResponse = new \Igestis\Ajax\AjaxResult();

        if ($this->request->IsPost()) {

            $previousName = $this->request->getPost("previousName");
            $newName = $this->request->getPost("newName");

            if (strstr($previousName, "/") || strstr($newName, "/")) {
                $ajaxResponse
                    ->addWizz(\Igestis\I18n\Translate::_("Error during the folder creation: the folder cannot contain any slashes"), \WIZZ::$WIZZ_ERROR)
                    ->render();
            }
            exec("/usr/bin/sudo ../modules/ServerMgmt/bin/helper renameDataFolder " . escapeshellarg(trim($previousName)) . " " . escapeshellarg(trim($newName)), $message, $returncode);

            if ($returncode == 0) {
                new \wizz(_("The folder " . $folderName . " has been created successfully"), \WIZZ::$WIZZ_SUCCESS);
                $ajaxResponse
                    ->addWizz(\Igestis\I18n\Translate::_(sprintf("The '%s' folder has been moved successfully", $previousName)), \WIZZ::$WIZZ_SUCCESS)
                    ->render();
            } else {
                $ajaxResponse
                    ->addWizz(\Igestis\I18n\Translate::_(sprintf("Error during the move of '%s' folder:  %s", $previousName, $message[0])), \WIZZ::$WIZZ_ERROR)
                    ->render();
            }
        } else {
            $ajaxResponse
                ->addWizz(\Igestis\I18n\Translate::_("No post data"), \WIZZ::$WIZZ_ERROR)
                ->render();
        }

    }

    public function deleteFolder($folderName) {

      exec("/usr/bin/sudo ../modules/ServerMgmt/bin/helper deleteDataFolder "
        . escapeshellarg($folderName), $message, $returncode);

      if ($returncode == 0) {
        new \wizz(_("The folder " . $folderName . " has been deleted successfully"), \WIZZ::$WIZZ_SUCCESS);
      } else {
        new \wizz(_("Error during the deletion of folder " . $folderName . ": " . $message[0]), \WIZZ::$WIZZ_ERROR);
      };
      $this->redirect(\ConfigControllers::createUrl("servermgmt_samba_index"));

    }
}
