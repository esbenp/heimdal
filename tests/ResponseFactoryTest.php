<?php

use Illuminate\Http\JsonResponse;
use Optimus\Heimdal\ResponseFactory;

class ResponseFactoryTest extends Orchestra\Testbench\TestCase {

    public function testMakeReturnsJsonResponse() {
        $response = ResponseFactory::make(new \Exception());
        $this->assertTrue($response instanceof JsonResponse);
        $this->assertEquals('error', $response->getData()->status);
    }

}
