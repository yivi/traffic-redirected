<?php

namespace Unir\V1\Rest\Redirects;

use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class AcceptableOriginValidator extends AcceptableTargetValidator
{
    const CIRCULAR = 'circular';
    const DUPE = 'duplicated';
    const CONFLICT = 'conflict';
    const SELFPOINTED = 'SELFPOINTED';

    protected $messageTemplates = [
        self::CONFLICT    => "El URI de origen hace conflicto con uno existente, no se puede crear la regla",
        self::CIRCULAR    => "El URI de origen ya existe como URI de destino, y crearía redirecciones encadenadas y/o circulares",
        self::DUPE        => "El URI de origen ya existe como URI de origen. No se puede redirigir un mismo origen a dos destinos",
        self::SELFPOINTED => "Origen y destino son iguales. Esta ruta no puede resolverse nunca"
    ];

    public function isValid($value, $context = null)
    {

        $this->setValue($value);
        $redirect_type = isset($context['redirect_type']) ? $context['redirect_type'] : 1;
        $id            = isset($context['id']) ? $context['id'] : '';
        $target        = isset($context['target']) ? $context['target'] : '';

        if ($value === $target) {

            $this->error(self::SELFPOINTED);

            return false;
        }

        // no circular redirections here, please. //

        /*
         * Duplicadas
         */
        // Si estamos haciendo un update de un valor existente, $id es igual al id de fila, y lo usamos para evitar
        // Así evitamos que inserten duplicados, el valor se da por bueno si es el del registro existente.
        $where = new Where();
        $where->equalTo('origin', $value)->and->notEqualTo('id', $id);

        /** @var HydratingResultSet $rowset */
        $rowset = $this->adapter->getTable()->select($where);

        if ($rowset->count() !== 0) {
            $this->error(self::DUPE);

            return false;
        }


        /*
         * Conflictivas
         */
        $where = new Where();

        // fixme: this query sucks a lil' bit
        // Si la regla está contenida por una existente o viceversa (si la regla que mandamos es abierta)
        $where
            ->nest()// ->
            ->nest()// -->
            ->like("origin", "$value%")
            ->or
            ->literal("'$value' LIKE CONCAT(origin, '%')")
            ->unnest()// <--
            ->and
            ->literal("$redirect_type BETWEEN 2 AND 3")
            ->unnest()// <-
            ->or
            // si la regla que mandamos es cerrada, sólo comprobamos que no haya ninguna regla más "amplia"
            // en la db que matchee la que mandamos.
            ->nest()
            ->literal("'$value' LIKE CONCAT(origin, '%')")
            ->and
            ->literal("$redirect_type = 1")
            ->and
            ->between('redirect_type', 2, 3);
        if ($id) {
            $where->notEqualTo('id', $context['id']);
        }

        /** @var HydratingResultSet $rowset */
        $rowset = $this->adapter->getTable()->select($where);

        // if we had any of these on record, BAD
        if ($rowset->count() !== 0) {
            $this->error(self::CONFLICT);

            return false;
        }

        /*
         * Circulares
         */
        $where = new Where();
        $where->equalTo("target", $value)->or
            ->nest()
            ->literal("$redirect_type BETWEEN 2 and 3")->and
            ->literal("target LIKE CONCAT('$value', '%')")
            ->unnest();

        $rowset = $this->adapter->getTable()->select($where);
        if ($rowset->count() !== 0) {
            $this->error(self::CIRCULAR);

            return false;
        }

        return true;


    }
}