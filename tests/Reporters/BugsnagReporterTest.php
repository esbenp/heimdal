<?php

use Bugsnag\Client;
use Optimus\Heimdal\Reporters\BugsnagReporter;
use Orchestra\Testbench\TestCase;

class BugsnagReporterTest extends TestCase
{
    /** @var BugsnagReporter */
    protected $bugsnagReporter;

    protected $client;

    public function setUp()
    {
        parent::setUp();
        
        $this->client = $this->getMockBuilder(stdClass::class)
            ->setMethods(['notifyException'])
            ->getMock();

        $this->app->instance(Client::class, $this->client);
        
        $this->bugsnagReporter = new BugsnagReporter([]);
    }

    public function testReport()
    {
        $exception = new Exception('Test');

        $expectedResponse = 'success';

        $this->client->expects($this->at(0))
            ->method('notifyException')
            ->with($exception)
            ->willReturn($expectedResponse);

        $response = $this->bugsnagReporter->report($exception);

        $this->assertSame($expectedResponse, $response);
    }
}
