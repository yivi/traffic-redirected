<?php

namespace RedirectsResourceTest\V1\Rest\Redirects;


use Unir\V1\Rest\Redirects\AcceptableTargetValidator;

class AcceptableTargetValidatorTest extends AbstractUriValidatorTest
{

    public function setUp()
    {
        parent::setUp();
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

    public function testTargetPointsToExistingOriginMatchFailure()
    {
        $target = "http://www.example.com/foo/bat/baz.html";

        $result = $this->validator->isValid($target);

        $this->assertFalse($result);
    }

    public function testTargetValidWithPartialMatch()
    {
        $target = "http://www.example.net/newurl/";

        // origin should be rejected, but this validator shouldn't care about it.
        $context = ['origin' => 'http://www.yivoff.com/oldurl/'];

        $result = $this->validator->isValid($target, $context);

        $this->assertTrue($result);
    }

}