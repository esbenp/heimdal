<?php

use Optimus\Heimdal\Formatters\ExceptionFormatter;
use Optimus\Heimdal\ResponseFactory;
use Orchestra\Testbench\TestCase;

class ExceptionFormatterTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();

        $this->exception = new Exception('Error', 100);
        $this->response = ResponseFactory::make($this->exception);
        $this->config = getConfigStub();
    }

    public function testEnsureProductionData()
    {
        $formatter = new ExceptionFormatter($this->config, false);
        $formatter->format($this->response, $this->exception, []);

        $data = $this->response->getData();
        $this->assertEquals('error', $data->status);
        $this->assertEquals('An error occurred.', $data->message);
    }

    public function testEnsureDebugData()
    {
        $formatter = new ExceptionFormatter($this->config, true);
        $formatter->format($this->response, $this->exception, []);

        $data = $this->response->getData();
        $this->assertEquals('error', $data->status);
        $this->assertEquals(100, $data->code);
        $this->assertEquals('Error', $data->message);
    }
}
