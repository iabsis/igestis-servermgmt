<?php
// config/ConfigControllers.php

// Le fichier de config se trouve dans le namespace du module
namespace Igestis\Modules\ServerMgmt;

class ConfigControllers extends \IgestisConfigController {
    /**
     * Retourne un tableau (attention à garder la même syntaxe de tableau)
     * contenant la liste des routes du module.
     * @return Array Liste des routes de ce module
     */
    public static function get() {
        return  array(
            /*********** Routes for the ServerMgmt module ***********/
            array(
                'id' => 'servermgmt_samba_index',
                'Parameters' => array(
                    'Module' => 'serverMgmt',
                    'Action' => 'samba_index'
                ),
                'Controller' => '\Igestis\Modules\ServerMgmt\SambaController',
                'Action' => 'indexAction',
                'Access' => array('CORE:ADMIN')
            ),

            array(
                'id' => 'servermgmt_backup_index',
                'Parameters' => array(
                    'Module' => 'serverMgmt',
                    'Action' => 'backup_index'
                ),
                'Controller' => '\Igestis\Modules\ServerMgmt\BackupController',
                'Action' => 'indexAction',
                'Access' => array('CORE:ADMIN')
            ),

         );
    }
}
