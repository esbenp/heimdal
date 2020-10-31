<?php

namespace Optimus\Heimdal\Formatters;

use Throwable;
use Illuminate\Http\JsonResponse;

abstract class BaseFormatter
{
    protected $config;

    protected $debug;

    public function __construct(array $config, $debug)
    {
        $this->debug = $debug;
        $this->config = $config;
    }

    abstract protected function format(JsonResponse $response, Throwable $e, array $reporterResponses);
}
