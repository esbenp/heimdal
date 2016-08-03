<?php

use Optimus\Heimdal\Formatters\ExceptionFormatter;
use Optimus\Heimdal\Formatters\HttpExceptionFormatter;
use Optimus\Heimdal\Formatters\UnprocessableEntityHttpExceptionFormatter;
use Optimus\Heimdal\ResponseFactory;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UnprocessableEntityHttpExceptionFormatterTest extends Orchestra\Testbench\TestCase {

    public function testErrorObjectIsCreatedByJsonApiStandard()
    {
        $errorsAsJson = json_encode([
            'field1' => ['field1.error1'],
            'field2' => ['field2.error1', 'field2.error2']
        ]);

        $exception = new UnprocessableEntityHttpException($errorsAsJson, null, 12345);
        $response = ResponseFactory::make($exception);
        $formatter = new UnprocessableEntityHttpExceptionFormatter(getConfigStub(), false);

        $formatter->format($response, $exception, []);

        $data = $response->getData();

        $this->assertTrue(isset($data->errors));

        $errors = $data->errors;

        $this->assertEquals(3, count($errors));
        $this->assertEquals('422', $errors[0]->status);
        $this->assertEquals('field2.error2', $errors[2]->detail);
        $this->assertEquals(12345, $errors[2]->code);
    }
}
