<?php

namespace RedirectsResourceTest\V1\Rest\Redirects;


use Unir\V1\Rest\Redirects\AcceptableTargetValidator;

class AcceptableTargetValidatorTest extends AbstractUriValidatorTest
{

    public function setUp()
    {
        parent::setUp();
        // $this->validator = $validator;
        $this->validator = new AcceptableTargetValidator();
        $this->validator->setAdapter($this->resource);

    }

    public function testTargetPointsToOriginFailure()
    {
        $target  = "http://www.australia.com/123";
        $context = ['origin' => $target];
        $result  = $this->validator->isValid($target, $context);

        $this->assertFalse($result);
    }

    public function testTargetPointsToExistingOriginFailure()
    {
        $target = "http://www.example.com/foo/bar";
        $result = $this->validator->isValid($target);

        $this->assertFalse($result);

    }

}