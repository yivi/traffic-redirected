<?php

namespace RedirectsResourceTest\V1\Rest\Redirects;

use Unir\V1\Rest\Redirects\AcceptableOriginValidator;
use Unir\V1\Rest\Redirects\RedirectsResource;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Test\PHPUnit\Controller\AbstractControllerTestCase;


abstract class AbstractUriValidatorTest extends AbstractControllerTestCase
{

    /** @var  array */
    protected $data;

    /** @var  AcceptableOriginValidator */
    protected $validator;

    /** @var  RedirectsResource $resource */
    protected $resource;

    /** @var  TableGateway $table */
    protected $table;

    /** @var  TableGateway $table */

    public function setUp()
    {

        $dsn = $GLOBALS['DB_DSN'] . ';' . $GLOBALS['DB_DBNAME'];

        $zf_adapter     = new Adapter([
            'driver' => 'pdo',
            'dsn' => $dsn,
            'user' => $GLOBALS['DB_USER'],
            'pass' => $GLOBALS['DB_PASSWD'],
        ]);
        $this->table    = new TableGateway('redirects', $zf_adapter);
        $this->resource = new RedirectsResource($this->table, 'id', 'Zend\Paginator\Paginator');


        $this->data = include '_files/data.php';
        // $this->validator = $validator;
        $this->validator = new AcceptableOriginValidator();
        $this->validator->setAdapter($this->resource);

        // getting the data where it belongs.
        foreach ($this->data as $row) {
            $this->table->insert($row);
        }

        parent::setUp();

    }

    public function tearDown()
    {
        // Getting rid of the data.
        $this->table->getAdapter()->query('TRUNCATE TABLE redirects')->execute();
        parent::tearDown();

    }
}