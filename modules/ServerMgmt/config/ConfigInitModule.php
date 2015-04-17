<?php
// config/ConfigInitModule.php

// Le fichier de config se trouve dans le namespace du module
namespace Igestis\Modules\ServerMgmt;

/*
 * La classe ConfigInitModule sera lancée par le coeur de l'application à différents moments,
 * afin de  rapatrier la liste des droits ou les entrées du menu.
 * Il est conseillé d'implémenter les interfaces ConfigMenuInterface et
 * ConfigRightsListInterface afin que votre logiciel puisse aisément vous aider à compléter
 * les méthodes abstraites.
 */
class ConfigInitModule implements \Igestis\Interfaces\ConfigMenuInterface {

    /* Ajoute au menu les différentes url, inutile de faire des vérifications des droits,
     * le core ne les affichera automatiquement que pour les personnes aillant le droit
     * d'accéder à la page.
     */
    public static function menuSet(\Application $context, \IgestisMenu &$menu) {
        $menu->addItem(
                \Igestis\I18n\Translate::_("Administration", ConfigModuleVars::textDomain()),
                \Igestis\I18n\Translate::_("Windows sharing", ConfigModuleVars::textDomain()),
                "servermgmt_samba_index"
        );

        $menu->addItem(
                \Igestis\I18n\Translate::_("Administration", ConfigModuleVars::textDomain()),
                \Igestis\I18n\Translate::_("Backup status", ConfigModuleVars::textDomain()),
                "servermgmt_backup_index"
        );
    }
}
