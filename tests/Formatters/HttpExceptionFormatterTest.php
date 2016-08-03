<?php

use Optimus\Heimdal\Formatters\ExceptionFormatter;
use Optimus\Heimdal\Formatters\HttpExceptionFormatter;
use Optimus\Heimdal\ResponseFactory;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HttpExceptionFormatterTest extends Orchestra\Testbench\TestCase {

    public function testHttpCodeIsset()
    {
        $config = getConfigStub();
        $exception = new HttpException('401', 'Error');
        $response = ResponseFactory::make($exception);

        $formatter = new HttpExceptionFormatter($config, true);
        $formatter->format($response, $exception, []);

        $this->assertTrue($formatter instanceof ExceptionFormatter);
        $this->assertEquals(401, $response->getStatusCode());
    }
}
