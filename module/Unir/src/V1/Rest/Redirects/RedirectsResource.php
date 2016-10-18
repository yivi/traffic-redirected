<?php

namespace Unir\V1\Rest\Redirects;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbTableGateway;
use ZF\Apigility\DbConnectedResource;

class RedirectsResource extends DbConnectedResource
{

    /** @var  TableGateway */
    protected $table;

    /**
     *
     * @param array|object $data
     *
     * @return array|object
     */
    public function create($data)
    {
        $data = $this->retrieveData($data);
        $data = array_filter($data, function ($value) {
            if ($value === null) {
                return false;
            }

            return true;
        });

        $select = $this->table->getSql()->select();
        $select->columns(['id' => new Expression('MAX(id)')]);


        // Autoincrementales, a mi?
        $rowset = $this->table->selectWith($select);
        $row    = $rowset->current();

        if ( ! $row) {
            $id = 1;
        } else {
            $id = $row->id + 1;
        }

        $data['id'] = $id;

        // Insertamos
        $this->table->insert($data);

        // Devolvemos
        return $this->fetch($id);
    }

    /**
     *
     * @param array|object $data
     *
     * @return void|\Zend\Paginator\Paginator
     */
    public function fetchAll($data = [])
    {
        $where  = [];
        // Los parámetros a los que les hago caso para el filtro
        $params = ['origin', 'target', 'owner', 'code'];

        // sql
        $select   = new Select("redirects");
        $whereObj = new Where();

        // si vienen parámetros, por defecto buscaremos siempre el keyword con wildcards
        if ($data) {
            foreach ($params as $param) {
                if ($data->get($param, false) !== false) {
                    $where[$param] = '%' . $data->get($param) . '%';
                }
            }
        }

        // que hacemos cuando la búsqueda es "PRECISA" (o sea, se está consumiendo para efectuar una redirección)
        if ($data && $data->get('precise_origin')) {
            // we no longer want those wildcards
            $where['origin'] = trim('%', $where['origin']);

            $whereObj
                ->expression("'66'=?", 66)
                ->equalTo('active', '1')
                ->nest()
                  ->nest()
                    ->literal("'{$data['origin']}' REGEXP concat(origin, '.?') " )
                    ->between('redirect_type', 2, 3)
                  ->unnest()
                ->or
                  ->equalTo('origin', $data['origin'])
                ->unnest();

            // no vamos a volver a buscar por origin
            unset($where['origin']);

        } else {
            // si no es precisa, vale un poco todo.
            $whereObj->expression("'77'=?", 77);
        }

        // si tenemos algo en el $where, lo agregamos a nuestro predicado
        if ( ! empty($where)) {
            foreach ($where as $wh => $ere) {
                $whereObj->like($wh, $ere);
            }
        }

        // construimos la sentencia
        $select->where($whereObj);

        // en caso de búsqueda precisa ordenamos por tipo de redirect
        if ($data && $data->get('precise_origin')) {
            $order='redirect_type ASC';
        } else {
            $order='origin ASC';
        }

        // exec búsqueda
        $adapter    = new DbTableGateway($this->table, $select->where, $order);
        // new Paginator
        $collection = new $this->collectionClass($adapter);

        // devolvemos paginator
        return $collection;
    }

    /**
     *
     * Replace an existing resource.
     *
     * @param int|string   $id   Identifier of resource.
     * @param array|object $data Data with which to replace the resource.
     *
     * @return array|object Updated resource.
     */
    public function update($id, $data)
    {
        $data = $this->retrieveData($data);
        $data = array_filter($data, function ($value) {
            if ($value === null) {
                return false;
            }

            return true;
        });
        $this->table->update($data, [$this->identifierName => $id]);

        return $this->fetch($id);
    }


    /**
     * A lil' help for my friends.
     *
     * @return \Zend\Db\TableGateway\TableGateway
     */
    public function getTable()
    {
        return $this->table;
    }

}