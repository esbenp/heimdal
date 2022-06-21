<?php

use Digitalroll\Heimdal\Formatters\ExceptionFormatter;
use Digitalroll\Heimdal\Formatters\HttpExceptionFormatter;
use Digitalroll\Heimdal\ResponseFactory;
use Orchestra\Testbench\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HttpExceptionFormatterTest extends TestCase
{

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
