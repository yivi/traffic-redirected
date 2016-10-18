<?php
namespace Unir;

// use Zend\I18n\Translator\Translator;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\ArrayUtils;
use ZF\Apigility\Provider\ApigilityProviderInterface;

class Module implements ApigilityProviderInterface
{
    public function getConfig()
    {
        // la configuración de module.config.php es sobreescrita por apigility, y aunque las personalizaciones permanezcan a veces ocurren cosas raras.
        // para mantener separadas la configuración de apigility y la adicional que necesitemos, mergeamos con otro fichero dónde la conservamos
        $non_apigility_config = include __DIR__ . '/config/custom.config.php';
        $apigility_config     = include __DIR__ . '/config/module.config.php';

        return ArrayUtils::merge($apigility_config, $non_apigility_config);
    }

    public function getAutoloaderConfig()
    {
        return [
            'ZF\Apigility\Autoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src',
                ],
            ],
        ];
    }

    public function onBootstrap(MvcEvent $event)
    {
        //** @var Translator $mvc_translator */
        // // $translator = $event->getApplication()->getServiceManager()->get('translator');
        // $translator = new Translator();
        // $mvc_translator = new MvcTranslator($translator);
        //
        // $mvc_translator->setLocale('es');
        //
        // $path = Resources::getBasePath();
        // $pattern = Resources::getPatternForValidator();
        //
        // $mvc_translator->addTranslationFilePattern(
        //     'phparray',
        //     Resources::getBasePath(),
        //     Resources::getPatternForValidator()
        // );
        // AbstractValidator::setDefaultTranslator($mvc_translator);

    }

}
