<?php

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Optimus\Heimdal\ExceptionHandler;
use Optimus\Heimdal\Formatters\BaseFormatter;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionFormatter extends BaseFormatter
{
    public function format(JsonResponse $response, Exception $e, array $reporterResponses)
    {
        $response->setData(['message' => 'Base']);
    }
}

class HttpExceptionFormatter extends BaseFormatter
{
    public function format(JsonResponse $response, Exception $e, array $reporterResponses)
    {
        $response->setData(['message' => 'Http']);
    }
}

class ExceptionHandlerTest extends Orchestra\Testbench\TestCase {

    public function setUp()
    {
        parent::setUp();

        app()['config']->set('optimus.heimdal', getConfigStub());
    }

    private function createHandler()
    {
        return app()->make(ExceptionHandler::class);
    }

    public function testReport()
    {
        $handler = $this->createHandler();

        $responses = $handler->report(new Exception('Test'));

        $this->assertEquals([
            'test' => 'Test: 1234',
            'test2' => 'Test: 4321'
        ], $responses);
    }

    public function testReportIgnoredException()
    {
        $handler = $this->createHandler();

        $exception = new Exception('Test');

        $reflectionHandler = new ReflectionClass($handler);

        $property = $reflectionHandler->getProperty('dontReport');

        $property->setAccessible(true);

        $property->setValue($handler, [
            get_class($exception)
        ]);

        $responses = $reflectionHandler->getMethod('report')
                                       ->invoke($handler, $exception);

        $this->assertEquals([], $responses);
    }

    public function testRendersAppropriateFormatter()
    {
        app()['config']->set('optimus.heimdal.formatters', [
            HttpException::class => HttpExceptionFormatter::class,
            Exception::class => ExceptionFormatter::class
        ]);

        $handler = $this->createHandler();

        $request = Request::capture();

        $response = $handler->render($request, new Exception('Test'));

        $this->assertEquals('Base', $response->getData()->message);

        $response = $handler->render($request, new NotFoundHttpException('Test'));

        $this->assertEquals('Http', $response->getData()->message);
    }
}
