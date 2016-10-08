<?php

namespace Unir\V1\Rest\Redirects;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AcceptableUriValidatorFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $table = $container->get('Unir\\V1\\Rest\\Redirects\\RedirectsResource');
        /** @var AcceptableTargetValidator $validator */
        $validator = new $requestedName();
        $validator->setAdapter($table);

        return $validator;
    }


    public function create_service(ContainerInterface $container, $requestedName, array $options = null)
    {
        return $this->__invoke($container, $requestedName, $options);

    }

}