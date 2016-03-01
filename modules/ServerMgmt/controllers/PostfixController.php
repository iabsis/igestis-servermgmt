<?php

namespace Igestis\Modules\ServerMgmt;

/**
 * Accounting management
 *
 * @author Olivier Bitsch
 */
class PostfixController extends \IgestisController
{

    /**
     * Show the list of accountings
     */
    public function indexAction()
    {

        if (!is_readable(ConfigModuleVars::aliasesFile())) {
            new \wizz(\Igestis\I18n\Translate::_(sprintf("Warning: the file '%s' is not readable by the webserver", ConfigModuleVars::aliasesFile())), \wizz::$WIZZ_ERROR);
        } elseif (!is_writable(ConfigModuleVars::aliasesFile())) {
            new \wizz(\Igestis\I18n\Translate::_(sprintf("Warning: the file '%s' is not writeable by the webserver", ConfigModuleVars::aliasesFile())), \wizz::$WIZZ_WARNING);
        }

        if ($this->request->IsPost()) {

            if (is_array($this->request->getPost("alias"))) {

                foreach ($this->request->getPost("alias") as $id => $alias) {
                    $user = $this->request->getPost("user");
                    if (isset($user[$id]) && is_array($user[$id])) {
                        if (!ConfigModuleVars::virtualDomain()) {
                            $users = $user[$id];
                        } else {
                            unset($users);
                            foreach ($user[$id] as $user) {
                                $users[] = $user . "@" . ConfigModuleVars::virtualDomain();
                            }
                        }
                        $data .= $alias . ": " . implode($users, ", ") . "\n";
                    }
                }
            }
            file_put_contents(ConfigModuleVars::aliasesFile(), $data);

            exec("/usr/bin/sudo ../modules/ServerMgmt/bin/helper updateAliases", $message, $returncode);
            if ($returncode == 0) {
                new \wizz(\Igestis\I18n\Translate::_(sprintf("The changes have been successfully written to the aliases file", ConfigModuleVars::aliasesFile())), \wizz::$WIZZ_SUCCESS);
            } else {
                new \wizz(\Igestis\I18n\Translate::_(sprintf("The changes have been written to the aliases file, but the following error occured: %s", $message)), \wizz::$WIZZ_SUCCESS);
            }

            $this->redirect(ConfigControllers::createUrl("servermgmt_postfix_index"));

        }

        $rows = file(ConfigModuleVars::aliasesFile(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $data = array();

        foreach ($rows as $line) {

            $line = str_replace(" ", "", $line);
            $currentLine = explode(':', $line);
            if (!ConfigModuleVars::virtualDomain()) {
                $data[$currentLine[0]] = explode(",", $currentLine[1]);
            } else {
                foreach (explode(",", $currentLine[1]) as $user) {
                    $explodedLine = explode("@", $user);
                    $data[$currentLine[0]][] = $explodedLine[0];
                }
            }

        }

        $employees = $this->context->entityManager->getRepository("CoreContacts")->getEmployeesList();

        $this->context->render("ServerMgmt/pages/PostfixController.twig", array(
            'row_data' => $data,
            'employee_list' => $employees,
        ));

    }
}
