<?php
namespace Unir;

// use Zend\I18n\Translator\Translator;
use Zend\Mvc\MvcEvent;
use ZF\Apigility\Provider\ApigilityProviderInterface;

class Module implements ApigilityProviderInterface
{
    public function getConfig()
    {
        // $non_apigility_config = include __DIR__ . '/config/module.custom.config.php';
        return include __DIR__ . '/config/module.config.php';
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
