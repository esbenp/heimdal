<?php

namespace Optimus\Heimdal;

use Exception;
use ReflectionClass;
use InvalidArgumentException;
use Asm89\Stack\CorsService;
use Illuminate\Foundation\Exceptions\Handler as LaravelExceptionHandler;
use Optimus\Heimdal\Formatters\BaseFormatter;
use Optimus\Heimdal\Reporters\ReporterInterface;
use Illuminate\Contracts\Container\Container;

class ExceptionHandler extends LaravelExceptionHandler
{
    protected $config;

    protected $container;

    protected $debug;

    protected $reportResponses = [];

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->config = $container['config']->get('optimus.heimdal');
        $this->debug = $container['config']->get('app.debug');
    }

    public function report(Exception $e)
    {
        parent::report($e);

        $reporters = $this->config['reporters'];

        $this->reportResponses = [];
        foreach ($reporters as $key => $reporter) {
            $class = !isset($reporter['class']) ? null : $reporter['class'];
            if (is_null($class) || !class_exists($class) || !in_array(ReporterInterface::class, class_implements($class))) {
                throw new InvalidArgumentException("$key: $class is not a valid reporter class.");
            }

            $config = isset($reporter['config']) && is_array($reporter['config']) ? $reporter['config'] : [];
            $reporterInstance = $this->container->make($class, [$config]);
            $this->reportResponses[$key] = $reporterInstance->report($e);
        }

        return $this->reportResponses;
    }

    public function render($request, Exception $e)
    {
        $response = $this->generateExceptionResponse($request, $e);

        if ($this->config['add_cors_headers']) {
            if (!class_exists(CorsService::class)) {
                throw new InvalidArgumentException(
                    'asm89/stack-cors has not been installed. Optimus\Heimdal needs it for adding CORS headers to response.'
                );
            }

            $cors = $this->container->make(CorsService::class);
            $cors->addActualRequestHeaders($response, $request);
        }

        return $response;
    }

    private function generateExceptionResponse($request, Exception $e)
    {
        $formatters = $this->config['formatters'];

        // :: notation will otherwise not work for PHP <= 5.6
        $responseFactoryClass = $this->config['response_factory'];
        // Allow users to have a base formatter for every response.
        $response = $responseFactoryClass::make($e);
        foreach($formatters as $exceptionType => $formatter) {
            if ($e instanceof $exceptionType) {
                if (!class_exists($formatter) ||
                    !(new ReflectionClass($formatter))->isSubclassOf(new ReflectionClass(BaseFormatter::class))) {
                    throw new InvalidArgumentException("$formatter is not a valid formatter class.");
                }

                $formatterInstance = new $formatter($this->config, $this->debug);
                $formatterInstance->format($response, $e, $this->reportResponses);
                break;
            }
        }

        return $response;
    }
}
