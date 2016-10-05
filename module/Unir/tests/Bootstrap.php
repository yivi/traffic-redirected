<?php

use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);

/**
 * Test bootstrap, for setting up autoloading
 */
class Bootstrap
{
    protected static $serviceManager;
    protected static $dataFile;
    protected static $testsDir = __DIR__;

    public static function init()
    {
        self::$dataFile = self::$dataFile . $GLOBALS['dataFile'];
        $zf2ModulePaths = array(dirname(dirname(__DIR__)));
        if (($path = static::findParentPath('vendor'))) {
            $zf2ModulePaths[] = $path;
        }
        if (($path = static::findParentPath('module')) !== $zf2ModulePaths[0]) {
            $zf2ModulePaths[] = $path;
        }

        static::initAutoloader();

        // use ModuleManager to load this module and it's dependencies
        $config         = array(
            'module_listener_options' => array(
                'module_paths' => $zf2ModulePaths,
            ),
            'modules'                 => array(
                'Zend\I18n',
                'Zend\\Db',
                'Zend\\Filter',
                'Zend\\Hydrator',
                'Zend\\InputFilter',
                'Zend\\Paginator',
                'Zend\\Router',
                'Zend\\Validator',
                'ZF\\Apigility',
                'ZF\\Apigility\\Documentation',
                'ZF\\ApiProblem',
                'ZF\\Configuration',
                'ZF\\OAuth2',
                'ZF\\MvcAuth',
                'ZF\\Hal',
                'ZF\\ContentNegotiation',
                'ZF\\ContentValidation',
                'ZF\\Rest',
                'ZF\\Rpc',
                'ZF\\Versioning',
                'Application',
                'Unir',
            )
        );
        $smc            = new ServiceManagerConfig();
        $serviceManager = new ServiceManager($smc->toArray());
        $serviceManager->setService('ApplicationConfig', $config);
        $serviceManager->get('ModuleManager')->loadModules();
        static::$serviceManager = $serviceManager;
    }

    public static function chroot()
    {
        $rootPath = dirname(static::findParentPath('module'));
        chdir($rootPath);
    }

    public static function getServiceManager()
    {
        return static::$serviceManager;
    }

    protected static function initAutoloader()
    {
        $vendorPath = static::findParentPath('vendor');

        if (file_exists($vendorPath . '/autoload.php')) {
            include $vendorPath . '/autoload.php';
        }

    }

    protected static function findParentPath($path)
    {
        $dir         = __DIR__;
        $previousDir = '.';
        while ( ! is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) {
                return false;
            }
            $previousDir = $dir;
        }

        return $dir . '/' . $path;
    }
}

Bootstrap::init();
Bootstrap::chroot();
