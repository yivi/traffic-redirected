<?php
/**
 * Created by PhpStorm.
 * User: yivi
 * Date: 6/10/16
 * Time: 17:25
 */

namespace RedirectsResourceTest\V1\Rest\Redirects;


use Unir\V1\Rest\Redirects\AcceptableOriginValidator;

class AcceptableOriginValidatorTest extends \PHPUnit_Framework_TestCase
{

    protected $data;

    protected $validator;

    public function setUp()
    {

        $this->data = include '_files/data.php';

        $this->validator = new AcceptableOriginValidator();

    }


    public function testOne()
    {
        $this->assertTrue(false);
    }

    public function testTwo()
    {

        $this->assertTrue(false);
    }

    public function testThree()
    {

        $this->assertTrue(false);
    }

    public function testFour()
    {

        $this->assertTrue(false);
    }

    public function testFive()
    {

        $this->assertTrue(false);
    }

    public function testEight()
    {
        $this->assertTrue(false);
    }

}
