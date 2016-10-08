<?php
/**
 * Created by PhpStorm.
 * User: yivi
 * Date: 6/10/16
 * Time: 17:25
 */

namespace RedirectsResourceTest\V1\Rest\Redirects;

use Unir\V1\Rest\Redirects\AcceptableOriginValidator;
use Unir\V1\Rest\Redirects\RedirectsResource;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Test\PHPUnit\Controller\AbstractControllerTestCase;


class AcceptableOriginValidatorTest extends AbstractControllerTestCase
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

    public function testExistsAsTargetFailure()
    {

        $result = $this->validator->isValid('http://whoiswho.co.uk/batman');

        // falló la validación
        $this->assertFalse($result);
    }

    public function testDuplicateFailure()
    {
        $result = $this->validator->isValid('http://www.yivoff.com/');

        // falló la validación
        $this->assertFalse($result);
    }

    public function testValidAndUnique()
    {

        $result = $this->validator->isValid('http://www.justiceleague.com/america');

        // validación pasada
        $this->assertTrue($result);
    }

    public function testCircularRuleFailure()
    {

        $result = $this->validator->isValid('http://www.yivoff.com/newurl/');

        $this->assertFalse($result);
    }

    /**
     * Una regla de coincidencia que coincida con otra regla de coincidencia existente debiera ser rechazada
     *
     */
    public function testConflictingRuleFailure()
    {
        $context = [
            'redirect_type' => '3'
        ];
        $results = $this->validator->isValid('http://www.yivoff.com/foobar/', $context);

        $this->assertFalse($results);
    }

    public function testOriginEqualsTargetFailure()
    {

        $origin  = "http://www.dog.com";
        $context = [
            'target' => $origin
        ];

        $result = $this->validator->isValid($origin, $context);

        $this->assertFalse($result);
    }

    /**
     * Probamos una regla de coincidencia parcial que pueda ser matchear reglas exactas más largas.
     * It should pass.
     */
    public function testWiderThanExistingExactRuleSuccess()
    {

        $origin = 'http://www.abcdotcom.org/fizz/';

        $result = $this->validator->isValid($origin);

        $this->assertTrue($result);

    }

}
