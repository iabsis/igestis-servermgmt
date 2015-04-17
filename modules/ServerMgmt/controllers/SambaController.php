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

        $this->context->render("ServerMgmt/pages/sambaListSharing.twig", array(
            'data_table' =>  array_diff(scandir(ConfigModuleVars::dataFolder()), array('..', '.'))
        ));
    }
}
