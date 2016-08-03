<?php

namespace Optimus\Heimdal\Formatters;

use Exception;
use Illuminate\Http\JsonResponse;
use Optimus\Heimdal\Formatters\ExceptionFormatter;

class HttpExceptionFormatter extends ExceptionFormatter
{
    public function format(JsonResponse $response, Exception $e, array $reporterResponses)
    {
        parent::format($response, $e, $reporterResponses);

        $response->setStatusCode($e->getStatusCode());
    }
}
