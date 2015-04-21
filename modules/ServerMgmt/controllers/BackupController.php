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


        $line = '';

        $f = fopen(ConfigModuleVars::backupLogFile(), 'r');
        $cursor = -1;

        fseek($f, $cursor, SEEK_END);
        $char = fgetc($f);

        /**
         * Trim trailing newline chars of the file
         */
        while ($char === "\n" || $char === "\r") {
            fseek($f, $cursor--, SEEK_END);
            $char = fgetc($f);
        }

        /**
         * Read until the start of file or first newline char
         */
        while ($char !== false && $char !== "\n" && $char !== "\r") {
            /**
             * Prepend the new char
             */
            $line = $char . $line;
            fseek($f, $cursor--, SEEK_END);
            $char = fgetc($f);
        }

        $this->context->render("ServerMgmt/pages/backupLog.twig", array(
            'line' =>  $line
        ));
    }

}
