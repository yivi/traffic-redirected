<?php
namespace RedirectsResourceTest\V1\Rest\Redirects;

use Unir\V1\Rest\Redirects\AcceptableOriginValidator;
use Unir\V1\Rest\Redirects\RedirectsResource;
use Zend\Db\TableGateway\TableGateway;


class AcceptableOriginValidatorTest extends AbstractUriValidatorTest
{

    /** @var  array */
    protected $data;

    /** @var  AcceptableOriginValidator */
    protected $validator;

    /** @var  RedirectsResource $resource */
    protected $resource;

    /** @var  TableGateway $table */
    protected $table;

    public function setUp()
    {
        parent::setUp();

        $this->validator = new AcceptableOriginValidator();
        $this->validator->setAdapter($this->resource);

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

    public function testNarrowerThanExistingExactRuleSuccess()
    {

        $origin = "http://www.abc.com/kknknk";

        $result = $this->validator->isValid($origin);

        $this->assertTrue($result);

    }

}