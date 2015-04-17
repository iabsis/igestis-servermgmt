<?php

namespace Igestis\Modules\ServerMgmt;
/**
 * Accounting management
 *
 * @author Gilles Hemmerlé
 */
class BackupController extends \IgestisController {

    /**
     * Show the list of accountings
     */
    public function indexAction() {
        $this->context->render("Commercial/pages/backupLog.twig", array(
            'logs' =>  $this->context->entityManager->getRepository("CommercialSellingAccount")->findAll()
        ));
    }

}
