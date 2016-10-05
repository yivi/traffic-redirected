<?php

namespace Unir\V1\Rest\Redirects;

use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Validator\AbstractValidator;

/**
 * Class AcceptableTargetValidator
 * @package Unir\V1\Rest\Redirects
 */
class AcceptableTargetValidator extends AbstractValidator
{
    /** @var  RedirectsResource */
    protected $adapter;

    /**
     * @var string
     */
    protected $messageTemplate = "El URI de destino '%value%' ya existe como URI de origen, y crearía redirecciones encadenadas/circulares";

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
     * @param mixed $value
     *
     * @return bool
     * @todo hay que comprobar también las rutas "abiertas"
     */
    public function isValid($value)
    {
        $this->setValue($value);

        /** @var HydratingResultSet $rowset */
        $rowset = $this->adapter->getTable()->select(function (Select $select) use ($value) {
            $select->where('origin', $value);
            $select->order('id')->limit(1);
        });

        if ($rowset->count() !== 0) {
            $this->error(str_replace('%value%', $value, $this->messageTemplate));
            return false;
        }
        return true;


    }
}