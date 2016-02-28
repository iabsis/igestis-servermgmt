<?php

namespace Igestis\Modules\ServerMgmt;

/**
 * Configuration of the module
 *
 * @author Gilles Hemmerlé <gilles.h@iabsis.com>
 */
class ConfigModuleVars
{
    const textDomain = "ServerMgmt";
    const version = "0.1";

    private static $version = null;
    private static $params;

    public static function initConfigVars()
    {
        if (empty(static::$params)) {
            self::initFromIniFile();
        }
    }

    public static function configFileFound()
    {
        return is_file(__DIR__ . "/config.ini") && is_readable(__DIR__ . "/config.ini");
    }

    public static function initFromIniFile()
    {

        self::$params =  parse_ini_file(__DIR__ . "/default-config.ini");
        if (self::configFileFound()) {
            self::$params = array_merge(
                self::$params,
                parse_ini_file(__DIR__ . "/config.ini")
            );

            if (!parse_ini_file(__DIR__ . "/config.ini")) {
                throw new \Igestis\Exceptions\ConfigException(\Igestis\I18n\Translate::_("The ServerMgmt config.ini file contains errors"));
            }
        }
    }

    /**
     * Return current module version
     * @return string Current module version
     */
    public static function version()
    {
        self::initConfigVars();
        if (self::$version === null) {
            self::$version = file_get_contents(__DIR__ . "/../version");
        }
        return self::$version;
        return empty(self::$params['DEBUG_MODE']) ? false : (bool)self::$params['DEBUG_MODE'];
    }

    /**
     * Return the module internal name
     * @return string module internal name
     */
    public static function moduleName()
    {
        return "ServerMgmt";
    }

    /**
     * Return the module displayed name
     * @return string module displayed name
     */
    public static function moduleShowedName()
    {
        return "Server Managment";
    }

    /**
     * Return the text domain for the module
     * @return string Text domain
     */
    public static function textDomain()
    {
        self::initConfigVars();
        return self::moduleName() . self::version();
    }

    /**
     * Return the folder path
     * @return string shared folder
     */
    public static function dataFolder()
    {
        self::initConfigVars();
        return self::$params['DATA_FOLDER_MGMT'];
    }

    /**
     * Return the aliases file
     * @return string shared folder
     */
    public static function aliasesFile()
    {
        self::initConfigVars();
        return self::$params['ALIASES_FILE'];
    }


    public static function backupLogFile()
    {
        self::initConfigVars();
        return self::$params['BACKUPNINJA_LOG_FILE'];
    }

    public static function sambaDomain()
    {
        self::initConfigVars();
        return self::$params['SAMBA_DOMAIN'];
    }

    public static function virtualDomain()
    {
        self::initConfigVars();
        return self::$params['VIRTUAL_DOMAIN'];
    }

}
