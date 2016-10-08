<?php

namespace Unir\V1\Rest\Redirects;

use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Where;
use Zend\Validator\AbstractValidator;

/**
 * Class AcceptableTargetValidator
 * @package Unir\V1\Rest\Redirects
 */
class AcceptableTargetValidator extends AbstractValidator
{
    /** @var  RedirectsResource */
    protected $adapter;

    const CONFLICT    = 'conflict';
    const SELFPOINTED = 'self_pointed';

    protected $messageTemplates = [
        self::CONFLICT => "El URI de origen hace conflicto con uno existente, no se puede crear la regla",
        self::SELFPOINTED => "Origen y destino son iguales. Esta ruta no puede resolverse nunca"
    ];


    /**
     * @param $adapter
     *
     * @return $this
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * @param mixed      $value
     *
     * @param null|array $context
     * @return bool
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        // check selfpointed
        if (isset($context['origin']) && $value === $context['origin']) {
            $this->error(self::SELFPOINTED);

            return false;
        }

        // check for circular conlict
        $where = new Where();
        $where
            ->equalTo('origin', $value)
            ->or
            ->nest()
            // if an existing rule partially matches our incoming $value
            ->literal("'$value' LIKE CONCAT(origin, '%')")
            ->and
            // and it's a 'beginning with' type of rule
            ->between('redirect_type', 2, 3)
            ->unnest();

        /** @var HydratingResultSet $rowset */
        $rowset = $this->adapter->getTable()->select($where);

        if ($rowset->count() !== 0) {
            $this->error(self::CONFLICT);
            return false;
        }
        return true;


    }
}