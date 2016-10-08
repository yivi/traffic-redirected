<?php

namespace RedirectsResourceTest\V1\Rest\Redirects;

use Zend\EventManager\EventManager;
use Zend\ModuleManager\ModuleEvent;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);

/**
 * Test bootstrap, for setting up autoloading
 */
class Bootstrap
{
    protected static $serviceManager;
    protected static $testsDir = __DIR__;
    protected static $zf2ModulePaths;
    protected static $rootPath;

    public static function init()
    {

        $zf2ModulePaths = array(dirname(dirname(__DIR__)));
        if (($path = static::findParentPath('vendor'))) {
            $zf2ModulePaths[] = $path;
        }
        if (($path = static::findParentPath('module')) !== $zf2ModulePaths[0]) {
            $zf2ModulePaths[] = $path;
        }

        static::$zf2ModulePaths = $zf2ModulePaths;

        static::initAutoloader();

        // use ModuleManager to load this module and it's dependencies

        $config = static::getTestConfig(self::$zf2ModulePaths);

        $smc = new ServiceManagerConfig();
        $serviceManager = new ServiceManager($smc->toArray());
        $serviceManager->setService('ApplicationConfig', $config);
        $moduleManager = $serviceManager->get('ModuleManager');
        $moduleManager->loadModules();

        /** @var EventManager $events */
        // $events = $moduleManager->getEventManager();
        // $events->attach(ModuleEvent::EVENT_MERGE_CONFIG, [self::class, 'onMergeConfig']);

        static::$serviceManager = $serviceManager;

    }

    /**
     * @deprecated
     * @param ModuleEvent $e
     */
    public static function onMergeConfig(ModuleEvent $e)
    {
        $configListener = $e->getConfigListener();
        $config = $configListener->getMergedConfig(false);

        $new_config = ArrayUtils::merge($config, self::getTestConfig(static::$zf2ModulePaths));

        // Pass the changed configuration back to the listener:
        $configListener->setMergedConfig($new_config);
    }

    public static function chroot()
    {
        $rootPath = dirname(static::findParentPath('module'));
        static::$rootPath = $rootPath;
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

    public static function getRootPath() {
        return static::$rootPath;
    }

    /**
     * @param $path
     * @return bool|string
     */
    protected static function findParentPath($path)
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) {
                return false;
            }
            $previousDir = $dir;
        }

        return $dir . '/' . $path;
    }

    /**
     * @param string|null $zf2ModulePaths
     * @return array
     */
    public static function getTestConfig($zf2ModulePaths = null)
    {
        if (is_null($zf2ModulePaths)) {
            $zf2ModulePaths = static::$zf2ModulePaths;
        }

        return [
            'module_listener_options' => [
                'module_paths' => $zf2ModulePaths,
            ],
            'config_glob_paths' => [
                'config/autoload/{{,*.}global,{,*.}local}.php',
            ],
            'modules' => [
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
            ],
            // these down here aren't being used as yet.
            'db' => [
                'adapters' => [
                    'phpunit' => [
                        'database' => 'antevenio',
                        'driver' => 'PDO_Mysql',
                        'username' => 'root',
                        'password' => '12345678',
                    ],
                ],
            ],
            'pepito' => 'grillo'
        ];

    }
}

Bootstrap::init();
Bootstrap::chroot();
