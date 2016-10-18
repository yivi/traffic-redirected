<?php
namespace Unir\V1\Rest\RedirectCollection;

use Interop\Container\ContainerInterface;
use Unir\V1\Rest\Redirects\AcceptableOriginValidator;
use Unir\V1\Rest\Redirects\AcceptableTargetValidator;
use Unir\V1\Rest\Redirects\RedirectsResource;
use Zend\Config\Config;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Validator\ValidatorChain;

class RedirectCollectionResourceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {

        $origin_validators = new ValidatorChain();
        $target_validators = new ValidatorChain();
        $redirects_resource = $services->get(RedirectsResource::class);

        $config                   = new Config($services->get('config'));
        $config_origin_validators = $config->get('importer_validators')->get('origin')->toArray();
        $config_target_validators = $config->get('importer_validators')->get('target')->toArray();

        foreach ($config_origin_validators as $validator => $options) {
            $vm = $services->get('ValidatorManager');
            $val = $vm->get($validator, $options);
            $origin_validators->attach($val);
        }

        foreach ($config_target_validators as $validator => $options) {
            $target_validators->attach($services->get('ValidatorManager')->get($validator));
        }

        $collection_resource = new RedirectCollectionResource($redirects_resource, $origin_validators, $target_validators);

        return $collection_resource;

    }

    public function create_service(ContainerInterface $container, $requestedName, array $options = null)
    {
        return $this->__invoke($container, $requestedName, $options);

    }


}
